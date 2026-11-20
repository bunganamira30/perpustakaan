<?php

namespace App\Http\Controllers\Vulnerable;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VulnerableCirculationController extends Controller
{
    /**
     * Resolve the database connection for vulnerable demo.
     */
    protected function getConnection(): string
    {
        return config()->has('database.connections.sqlite_vulnerable') ? 'sqlite_vulnerable' : config('database.default');
    }

    /**
     * 1. Celah SQL Injection (SEC-08): Pencarian buku via konkatenasi string langsung tanpa prepared statements.
     */
    public function booksIndex(Request $request): View
    {
        $searchTerm = $request->input('q', $request->input('search', ''));
        $executedQuery = '';

        if ($searchTerm !== '') {
            // VULNERABLE SQLi: Penggabungan string mentah (raw string concatenation)
            // Payload serangan: ' OR '1'='1 --
            $executedQuery = "SELECT * FROM books WHERE title LIKE '%" . $searchTerm . "%'";
            $books = DB::connection($this->getConnection())->select($executedQuery);
        } else {
            $executedQuery = "SELECT * FROM books WHERE is_archived = 0";
            $books = DB::connection($this->getConnection())->select($executedQuery);
        }

        return view('vuln.books.index', compact('books', 'searchTerm', 'executedQuery'));
    }

    /**
     * 2. Celah Stored XSS (SEC-09): Menampilkan detail buku dan ulasan dengan sintaks Blade unescaped {!! !!}.
     */
    public function bookShow($id): View
    {
        $book = DB::connection($this->getConnection())->table('books')->where('id', $id)->first();

        if (!$book) {
            abort(404, 'Buku tidak ditemukan.');
        }

        $reviews = DB::connection($this->getConnection())
            ->table('book_reviews')
            ->join('users', 'book_reviews.user_id', '=', 'users.id')
            ->where('book_reviews.book_id', $id)
            ->select('book_reviews.*', 'users.name as user_name')
            ->orderBy('book_reviews.created_at', 'desc')
            ->get();

        return view('vuln.books.show', compact('book', 'reviews'));
    }

    /**
     * Menyimpan ulasan buku pada versi rentan (tanpa sanitasi ketat).
     */
    public function reviewStore(Request $request, $id): RedirectResponse
    {
        $comment = $request->input('comment', '');
        $rating = (int) $request->input('rating', 5);

        DB::connection($this->getConnection())->table('book_reviews')->insert([
            'book_id' => $id,
            'user_id' => auth()->id() ?? 2, // fallback to Budi Santoso jika unauthenticated
            'rating' => $rating,
            'comment' => $comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('vuln.books.show', $id)->with('success', 'Ulasan versi rentan tersimpan!');
    }

    /**
     * 3. Celah Insecure Direct Object Reference / IDOR (SEC-10): Mengambil detail pinjaman langsung dari parameter URL tanpa Gate/Policy.
     */
    public function borrowingShow($id): View
    {
        // VULNERABLE IDOR: Tidak memeriksa $user->id === $borrowing->user_id, tidak memanggil Gate::authorize
        $borrowing = DB::connection($this->getConnection())
            ->table('borrowings')
            ->join('books', 'borrowings.book_id', '=', 'books.id')
            ->join('users', 'borrowings.user_id', '=', 'users.id')
            ->leftJoin('members', 'users.id', '=', 'members.user_id')
            ->where('borrowings.id', $id)
            ->select(
                'borrowings.*',
                'books.title as book_title',
                'books.author as book_author',
                'books.isbn as book_isbn',
                'users.name as user_name',
                'users.email as user_email',
                'members.phone_encrypted as member_phone'
            )
            ->first();

        if (!$borrowing) {
            abort(404, 'Data peminjaman tidak ditemukan.');
        }

        return view('vuln.borrowings.show', compact('borrowing'));
    }
}
