<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookReview;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Seeder;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budi = User::where('username', 'budi_santoso')->first();
        $book = Book::where('isbn', '978-602-001-001-1')->first();

        if ($budi && $book) {
            // Peminjaman aktif Budi Santoso untuk persiapan uji IDOR (SEC-10)
            Borrowing::create([
                'user_id' => $budi->id,
                'book_id' => $book->id,
                'borrowed_at' => now()->subDays(2),
                'due_date' => now()->addDays(5),
                'returned_at' => null,
                'status' => 'borrowed',
            ]);

            // Ulasan awal untuk buku 1 (persiapan uji XSS contextual escaping SEC-09)
            BookReview::create([
                'book_id' => $book->id,
                'user_id' => $budi->id,
                'rating' => 5,
                'comment' => 'Buku referensi yang sangat sistematis dalam mengulas enkripsi simetris, hashing lambat, dan pertahanan jaringan!',
            ]);
        }
    }
}
