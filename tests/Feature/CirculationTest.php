<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CirculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_santoso',
            'email' => 'budi@test.local',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $this->book = Book::create([
            'title' => 'Buku Sirkulasi Uji',
            'author' => 'Penulis Sirkulasi',
            'isbn' => '978-602-0001-01-1',
            'category' => 'Teknologi',
            'stock' => 5,
            'is_archived' => false,
        ]);
    }

    public function test_atomic_borrowing_decrements_book_stock_by_one(): void
    {
        $initialStock = $this->book->stock;

        $response = $this->actingAs($this->user)->post("/books/{$this->book->id}/borrow");

        $response->assertRedirect('/my-borrowings');
        $response->assertSessionHas('success');

        $this->assertEquals($initialStock - 1, $this->book->fresh()->stock);
        $this->assertDatabaseHas('borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'status' => 'borrowed',
        ]);
    }

    public function test_cannot_borrow_book_with_zero_stock(): void
    {
        $outOfStockBook = Book::create([
            'title' => 'Buku Habis',
            'author' => 'Penulis Habis',
            'isbn' => '978-602-0001-02-2',
            'category' => 'Sains',
            'stock' => 0,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->user)->post("/books/{$outOfStockBook->id}/borrow");

        $response->assertSessionHas('error', 'Maaf, stok buku ini sedang habis.');
        $this->assertEquals(0, $outOfStockBook->fresh()->stock);
        $this->assertDatabaseMissing('borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $outOfStockBook->id,
        ]);
    }

    public function test_cannot_borrow_more_than_three_active_books(): void
    {
        // Buat 3 buku berbeda
        $book2 = Book::create(['title' => 'Buku 2', 'author' => 'A', 'isbn' => '978-2', 'category' => 'C', 'stock' => 2]);
        $book3 = Book::create(['title' => 'Buku 3', 'author' => 'B', 'isbn' => '978-3', 'category' => 'C', 'stock' => 2]);
        $book4 = Book::create(['title' => 'Buku 4', 'author' => 'C', 'isbn' => '978-4', 'category' => 'C', 'stock' => 2]);

        // Pinjam 3 buku
        $this->actingAs($this->user)->post("/books/{$this->book->id}/borrow");
        $this->actingAs($this->user)->post("/books/{$book2->id}/borrow");
        $this->actingAs($this->user)->post("/books/{$book3->id}/borrow");

        $this->assertEquals(3, Borrowing::where('user_id', $this->user->id)->where('status', 'borrowed')->count());

        // Coba pinjam buku ke-4 (BR-01)
        $response = $this->actingAs($this->user)->post("/books/{$book4->id}/borrow");
        $response->assertSessionHas('error', 'Anda telah mencapai batas maksimal 3 buku pinjaman aktif.');
        $this->assertEquals(2, $book4->fresh()->stock);
    }

    public function test_cannot_borrow_duplicate_active_book(): void
    {
        // Pinjam pertama
        $this->actingAs($this->user)->post("/books/{$this->book->id}/borrow");

        // Pinjam kedua untuk buku yang sama (BR-03)
        $response = $this->actingAs($this->user)->post("/books/{$this->book->id}/borrow");
        $response->assertSessionHas('error', 'Anda masih memiliki peminjaman aktif untuk buku ini.');
    }

    public function test_idempotent_return_increments_stock_only_once_on_repeated_requests(): void
    {
        // Pinjam buku (stok 5 -> 4)
        $this->actingAs($this->user)->post("/books/{$this->book->id}/borrow");
        $borrowing = Borrowing::where('user_id', $this->user->id)->firstOrFail();
        $this->assertEquals(4, $this->book->fresh()->stock);

        // Pengembalian Pertama (Normal)
        $resp1 = $this->actingAs($this->user)->post("/borrowings/{$borrowing->id}/return");
        $resp1->assertSessionHas('success');
        $this->assertEquals(5, $this->book->fresh()->stock);
        $this->assertEquals('returned', $borrowing->fresh()->status);

        // Pengembalian Kedua (Simulasi Double Click / Retry Idempoten)
        $resp2 = $this->actingAs($this->user)->post("/borrowings/{$borrowing->id}/return");
        $resp2->assertSessionHas('info');

        // Stok TIDAK BOLEH bertambah menjadi 6 (Idempoten BR-04)
        $this->assertEquals(5, $this->book->fresh()->stock);
    }
}
