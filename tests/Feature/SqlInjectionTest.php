<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Book::create([
            'title' => 'Dasar Kriptografi Modern',
            'author' => 'Penulis Kripto',
            'isbn' => '978-001',
            'category' => 'Keamanan',
            'stock' => 5,
            'is_archived' => false,
        ]);

        Book::create([
            'title' => 'Buku Terarsip Rahasia',
            'author' => 'Penulis Rahasia',
            'isbn' => '978-999',
            'category' => 'Rahasia',
            'stock' => 0,
            'is_archived' => true,
        ]);
    }

    public function test_search_neutralizes_sql_injection_payload(): void
    {
        // Serangan SQL Injection: ' OR '1'='1 -- 
        $response = $this->get('/books?q=' . urlencode("' OR '1'='1 -- "));

        $response->assertStatus(200);

        // SEC-08 Safe: Kueri menggunakan parameter binding murni
        // Buku terarsip tidak boleh bocor ke hasil pencarian
        $response->assertDontSee('Buku Terarsip Rahasia');

        // String literal tidak cocok dengan judul buku mana pun
        $response->assertSee('Tidak ada koleksi buku yang ditemukan');
    }

    public function test_search_returns_expected_matching_book(): void
    {
        $response = $this->get('/books?q=Kriptografi');

        $response->assertStatus(200);
        $response->assertSee('Dasar Kriptografi Modern');
    }
}
