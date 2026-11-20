<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdorTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected User $admin;
    protected Borrowing $borrowingA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_santoso',
            'email' => 'budi@test.local',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $this->userB = User::create([
            'name' => 'Siti Aminah',
            'username' => 'siti_aminah',
            'email' => 'siti@test.local',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Bunga',
            'username' => 'admin_bunga',
            'email' => 'admin@test.local',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $book = Book::create([
            'title' => 'Buku Uji IDOR',
            'author' => 'Penulis IDOR',
            'isbn' => '978-602-9999-01-1',
            'category' => 'Security',
            'stock' => 5,
        ]);

        $this->borrowingA = Borrowing::create([
            'user_id' => $this->userA->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'borrowed',
        ]);
    }

    public function test_user_cannot_view_another_users_borrowing_detail(): void
    {
        // Siti Aminah mencoba membuka detail peminjaman milik Budi Santoso (IDOR Attack)
        $response = $this->actingAs($this->userB)->get("/borrowings/{$this->borrowingA->id}");

        // SEC-10 Safe: Penolakan server-side via BorrowingPolicy
        $response->assertStatus(403);
    }

    public function test_user_can_view_own_borrowing_detail(): void
    {
        // Budi Santoso membuka peminjamannya sendiri
        $response = $this->actingAs($this->userA)->get("/borrowings/{$this->borrowingA->id}");

        $response->assertStatus(200);
        $response->assertSee('Buku Uji IDOR');
    }

    public function test_admin_can_view_any_users_borrowing_detail(): void
    {
        // Administrator membuka peminjaman anggota untuk audit sirkulasi
        $response = $this->actingAs($this->admin)->get("/borrowings/{$this->borrowingA->id}");

        $response->assertStatus(200);
        $response->assertSee('Buku Uji IDOR');
    }
}
