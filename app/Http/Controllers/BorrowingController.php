<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the user's active borrowings and history.
     */
    public function myBorrowings(): View
    {
        $user = auth()->user();

        $activeBorrowings = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->orderByDesc('borrowed_at')
            ->get();

        $historyBorrowings = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'returned')
            ->orderByDesc('returned_at')
            ->paginate(10);

        return view('borrowings.my', compact('activeBorrowings', 'historyBorrowings'));
    }

    /**
     * Handle an atomic book borrowing request (FR-006, BR-01, BR-02, BR-03).
     */
    public function borrow(Request $request, $id): RedirectResponse
    {
        $user = auth()->user();
        $book = Book::findOrFail($id);

        // Validasi: Buku tidak boleh dalam kondisi diarsipkan
        if ($book->is_archived) {
            return back()->with('error', 'Buku ini sedang diarsipkan dan tidak dapat dipinjam.');
        }

        // BR-01: Maksimal 3 buku dengan status 'borrowed' aktif per anggota
        $activeCount = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        if ($activeCount >= 3) {
            return back()->with('error', 'Anda telah mencapai batas maksimal 3 buku pinjaman aktif.');
        }

        // BR-03: Larangan duplikasi pinjaman aktif untuk buku yang sama
        $alreadyBorrowed = Borrowing::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('error', 'Anda masih memiliki peminjaman aktif untuk buku ini.');
        }

        // BR-02: Transaksi stok atomik untuk mencegah race condition (overselling)
        try {
            DB::transaction(function () use ($book, $user) {
                // Conditional atomic decrement: hanya kurangi jika stok > 0 dan buku aktif
                $affected = DB::update(
                    'UPDATE books SET stock = stock - 1, updated_at = ? WHERE id = ? AND stock > 0 AND is_archived = 0',
                    [now(), $book->id]
                );

                if ($affected === 0) {
                    throw new \Exception('Maaf, stok buku ini sedang habis.');
                }

                Borrowing::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'borrowed_at' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => 'borrowed',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('my-borrowings')->with('success', 'Buku "' . $book->title . '" berhasil dipinjam! Batas waktu pengembalian adalah 7 hari ke depan.');
    }

    /**
     * Handle idempotent book return request (FR-007, BR-04).
     */
    public function returnBook($id): RedirectResponse
    {
        $borrowing = Borrowing::with('book')->findOrFail($id);

        // Otorisasi: Pemilik pinjaman atau Administrator
        Gate::authorize('return', $borrowing);

        $affected = 0;

        // BR-04: Transaksi pembaruan status kondisional idempoten
        DB::transaction(function () use ($borrowing, &$affected) {
            // Hanya perbarui jika status saat ini masih 'borrowed'
            $affected = DB::update(
                "UPDATE borrowings SET status = 'returned', returned_at = ?, updated_at = ? WHERE id = ? AND status = 'borrowed'",
                [now(), now(), $borrowing->id]
            );

            // Tambah stok buku HANYA jika baris peminjaman berhasil diubah dari 'borrowed' ke 'returned'
            if ($affected > 0) {
                DB::update(
                    'UPDATE books SET stock = stock + 1, updated_at = ? WHERE id = ?',
                    [now(), $borrowing->book_id]
                );
            }
        });

        if ($affected > 0) {
            return back()->with('success', 'Buku "' . $borrowing->book->title . '" berhasil dikembalikan. Terima kasih!');
        }

        // Request duplikat (idempoten): status sudah returned, tidak ada error dan stok tidak ditambah dua kali
        return back()->with('info', 'Buku ini sudah berstatus dikembalikan sebelumnya (operasi idempoten).');
    }
}
