@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Header Navigasi -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('books.index') }}" class="hover:text-slate-900">Katalog Buku</a>
        <span>/</span>
        <a href="{{ route('my-borrowings') }}" class="hover:text-slate-900">Pinjaman Saya</a>
        <span>/</span>
        <span class="text-slate-900 font-medium">Detail Peminjaman #{{ $borrowing->id }}</span>
    </nav>

    <!-- Kartu Detail Peminjaman Terproteksi Policy (Anti-IDOR SEC-10 Safe) -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                        ID Transaksi: #{{ $borrowing->id }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <x-heroicon-o-shield-check class="w-3.5 h-3.5 mr-1" />
                        Anti-IDOR Protected
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $borrowing->book->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Penulis: <span class="font-medium text-slate-800">{{ $borrowing->book->author }}</span> • 
                    Kategori: <span class="font-medium text-slate-800">{{ $borrowing->book->category }}</span>
                </p>
            </div>
            <div>
                @if($borrowing->status === 'borrowed')
                    <x-status-badge status="borrowed" label="Sedang Dipinjam" />
                @else
                    <x-status-badge status="returned" label="Telah Dikembalikan" />
                @endif
            </div>
        </div>

        <!-- Rincian Sirkulasi & Peminjam -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-6 text-sm">
            <!-- Informasi Buku -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                    <x-heroicon-o-book-open class="w-4 h-4 mr-1.5 text-slate-500" />
                    Informasi Koleksi Buku
                </h2>
                <div>
                    <span class="text-xs text-slate-500 block">Nomor ISBN:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $borrowing->book->isbn }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Status Stok Terkini:</span>
                    <span class="font-semibold text-slate-800">{{ $borrowing->book->stock }} Eksemplar</span>
                </div>
            </div>

            <!-- Informasi Peminjam (Sensitif & Terotorisasi) -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                    <x-heroicon-o-user class="w-4 h-4 mr-1.5 text-slate-500" />
                    Data Anggota Peminjam
                </h2>
                <div>
                    <span class="text-xs text-slate-500 block">Nama Anggota:</span>
                    <span class="font-semibold text-slate-800">{{ $borrowing->user->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Nomor Telepon (Dekripsi AEAD):</span>
                    <span class="font-mono font-semibold text-slate-800">
                        {{ $borrowing->user->member?->phone_encrypted ?? 'Tidak tersedia' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Timeline Jadwal Peminjaman -->
        <div class="p-4 rounded-lg border border-slate-200 bg-white space-y-3 mb-6">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                <x-heroicon-o-calendar class="w-4 h-4 mr-1.5 text-slate-500" />
                Jadwal & Waktu Sirkulasi
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 block">Tanggal Dipinjam:</span>
                    <span class="font-semibold text-slate-900 text-sm mt-0.5 block">
                        {{ $borrowing->borrowed_at->format('d M Y, H:i') }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Batas Waktu Pengembalian:</span>
                    @php
                        $isOverdue = $borrowing->status === 'borrowed' && $borrowing->due_date->isPast();
                    @endphp
                    <span class="font-semibold text-sm mt-0.5 block {{ $isOverdue ? 'text-red-700' : 'text-slate-900' }}">
                        {{ $borrowing->due_date->format('d M Y') }}
                    </span>
                    @if($isOverdue)
                        <span class="text-[11px] text-red-600 font-medium">Telah melewati batas tempo</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-500 block">Waktu Pengembalian Aktual:</span>
                    <span class="font-semibold text-slate-900 text-sm mt-0.5 block">
                        {{ $borrowing->returned_at ? $borrowing->returned_at->format('d M Y, H:i') : '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            @if($borrowing->status === 'borrowed')
                <form method="POST" action="{{ route('borrowings.return', $borrowing->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?');">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-xs focus:outline-none focus:ring-2 focus:ring-slate-900">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-1.5" />
                        Kembalikan Buku Ini
                    </button>
                </form>
            @endif

            <a href="{{ route('my-borrowings') }}" 
               class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 shadow-xs">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1.5" />
                Kembali ke Daftar Pinjaman
            </a>

            <a href="{{ route('books.show', $borrowing->book_id) }}" 
               class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 shadow-xs">
                <x-heroicon-o-book-open class="w-4 h-4 mr-1.5" />
                Halaman Buku
            </a>
        </div>
    </div>
</div>
@endsection
