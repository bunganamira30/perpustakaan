<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Keamanan Sistem Komputer Modern',
                'author' => 'Dr. Ir. Heru W., M.T.',
                'isbn' => '978-602-001-001-1',
                'category' => 'Keamanan Siber',
                'stock' => 5,
                'is_archived' => false,
            ],
            [
                'title' => 'Kriptografi dan Keamanan Data',
                'author' => 'Prof. Ir. Budi Rahardjo, M.Sc., Ph.D.',
                'isbn' => '978-602-001-002-8',
                'category' => 'Kriptografi',
                'stock' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Arsitektur Jaringan dan Protokol Kriptografi',
                'author' => 'Dr. Onno W. Purbo, M.Eng.',
                'isbn' => '978-602-001-003-5',
                'category' => 'Jaringan Komputer',
                'stock' => 1, // Kritis konkurensi (Race Condition testing)
                'is_archived' => false,
            ],
            [
                'title' => 'Rekayasa Perangkat Lunak Berorientasi Objek',
                'author' => 'Ian Sommerville',
                'isbn' => '978-602-001-004-2',
                'category' => 'Rekayasa Perangkat Lunak',
                'stock' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'Pengembangan Aplikasi Web Modern dengan Laravel',
                'author' => 'Taylor Otwell & Fikri Alamsyah',
                'isbn' => '978-602-001-005-9',
                'category' => 'Pemrograman Web',
                'stock' => 5,
                'is_archived' => false,
            ],
            [
                'title' => 'Desain Antarmuka Pengguna dan Aksesibilitas WCAG',
                'author' => 'Don Norman & Jakob Nielsen',
                'isbn' => '978-602-001-006-6',
                'category' => 'UI/UX Desain',
                'stock' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Struktur Data dan Algoritma Tingkat Lanjut',
                'author' => 'Thomas H. Cormen, Charles E. Leiserson',
                'isbn' => '978-602-001-007-3',
                'category' => 'Algoritma',
                'stock' => 2,
                'is_archived' => false,
            ],
            [
                'title' => 'Dasar-Dasar Administrasi Sistem Linux dan Server',
                'author' => 'Eko Kurniawan Khannedy, S.Kom.',
                'isbn' => '978-602-001-008-0',
                'category' => 'Sistem Operasi',
                'stock' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'Manajemen Basis Data Relasional dan SQLite Lanjut',
                'author' => 'C.J. Date',
                'isbn' => '978-602-001-009-7',
                'category' => 'Basis Data',
                'stock' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Prinsip Pertahanan Siber dan Pengujian Penetrasi (Pentesting)',
                'author' => 'Georgia Weidman',
                'isbn' => '978-602-001-010-3',
                'category' => 'Keamanan Siber',
                'stock' => 2,
                'is_archived' => false,
            ],
            [
                'title' => 'Cloud Computing dan Arsitektur Microservices',
                'author' => 'Sam Newman',
                'isbn' => '978-602-001-011-0',
                'category' => 'Cloud Computing',
                'stock' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Kecerdasan Buatan dan Pembelajaran Mesin',
                'author' => 'Stuart Russell & Peter Norvig',
                'isbn' => '978-602-001-012-7',
                'category' => 'Kecerdasan Buatan',
                'stock' => 2,
                'is_archived' => false,
            ],
            [
                'title' => 'Kompilasi Teori Bahasa dan Otomata',
                'author' => 'John E. Hopcroft, Jeffrey D. Ullman',
                'isbn' => '978-602-001-013-4',
                'category' => 'Ilmu Komputer',
                'stock' => 0, // Stok habis untuk pengujian validasi sirkulasi
                'is_archived' => false,
            ],
            [
                'title' => 'Metodologi Penelitian Bidang Ilmu Komputer',
                'author' => 'Prof. Richardus Eko Indrajit',
                'isbn' => '978-602-001-014-1',
                'category' => 'Metodologi',
                'stock' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Pemrograman Sistem Warisan (Legacy Systems)',
                'author' => 'Martin Fowler',
                'isbn' => '978-602-001-015-8',
                'category' => 'Rekayasa Perangkat Lunak',
                'stock' => 1,
                'is_archived' => true, // Buku diarsipkan (Soft Archived BR-05)
            ],
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }
    }
}
