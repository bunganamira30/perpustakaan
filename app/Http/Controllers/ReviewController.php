<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created book review in storage (FR-009, SEC-09 Safe).
     */
    public function store(Request $request, $id): RedirectResponse
    {
        $book = Book::findOrFail($id);

        if ($book->is_archived) {
            return back()->with('error', 'Buku ini telah diarsipkan dan tidak dapat diulas.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Silakan pilih rating bintang untuk buku ini.',
            'rating.between' => 'Rating harus bernilai antara 1 sampai 5 bintang.',
            'comment.required' => 'Kolom ulasan buku tidak boleh kosong.',
            'comment.max' => 'Panjang ulasan tidak boleh melebihi 1000 karakter.',
        ]);

        // Simpan komentar asli secara aman ke basis data
        BookReview::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirimkan. Terima kasih atas masukan Anda!');
    }
}
