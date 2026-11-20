<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XssTest extends TestCase
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
            'title' => 'Buku Ulasan XSS',
            'author' => 'Penulis XSS',
            'isbn' => '978-002',
            'category' => 'Security',
            'stock' => 3,
            'is_archived' => false,
        ]);
    }

    public function test_stored_xss_payload_is_contextually_escaped_in_blade(): void
    {
        $payload = "<script>alert('XSS-Terkonfirmasi-Bunga0120')</script>";

        // Kirim ulasan dengan payload script injeksi
        $this->actingAs($this->user)->post("/books/{$this->book->id}/reviews", [
            'rating' => 5,
            'comment' => $payload,
        ]);

        // Verifikasi database menyimpan komentar asli (raw storage)
        $this->assertDatabaseHas('book_reviews', [
            'book_id' => $this->book->id,
            'comment' => $payload,
        ]);

        // Buka halaman detail buku
        $response = $this->get("/books/{$this->book->id}");

        $response->assertStatus(200);

        // SEC-09 Safe: Wajib di-escape menjadi entitas HTML aman
        $response->assertSee('&lt;script&gt;alert(', false);

        // Dilarang mengandung tag script mentah unescaped
        $response->assertDontSee($payload, false);
    }

    public function test_security_headers_middleware_attaches_csp_header(): void
    {
        $response = $this->get("/books/{$this->book->id}");

        $response->assertStatus(200);

        // Verifikasi kehadiran header keamanan berlapis (Defense-in-Depth)
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');

        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy'));
    }
}
