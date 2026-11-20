@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center">
                <x-heroicon-o-book-open class="w-7 h-7 mr-2.5 text-slate-800" />
                Daftar Pinjaman Saya
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Pantau buku yang sedang Anda pinjam serta riwayat sirkulasi peminjaman buku perpustakaan.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('books.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 shadow-xs">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 mr-1.5" />
                Cari Buku Lagi
            </a>
        </div>
    </div>

    <!-- Bagian 1: Pinjaman Aktif (Maksimal 3 Buku - BR-01) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <h2 class="text-lg font-bold text-slate-900">Pinjaman Aktif</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $activeBorrowings->count() >= 3 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-700' }}">
                    {{ $activeBorrowings->count() }} / 3 Buku
                </span>
            </div>
            @if($activeBorrowings->count() >= 3)
                <span class="text-xs text-amber-700 font-medium">Batas maksimal peminjaman aktif tercapai.</span>
            @endif
        </div>

        @if($activeBorrowings->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center shadow-xs">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 mx-auto flex items-center justify-center mb-3">
                    <x-heroicon-o-bookmark-slash class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Tidak ada pinjaman aktif</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">Anda sedang tidak meminjam buku apa pun saat ini.</p>
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-xs">
                    <x-heroicon-o-book-open class="w-4 h-4 mr-1.5" />
                    Jelajahi Katalog Buku
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Buku</th>
                                <th class="px-6 py-3.5">Tanggal Pinjam</th>
                                <th class="px-6 py-3.5">Batas Pengembalian</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal">
                            @foreach($activeBorrowings as $borrowing)
                                <tr class="hover:bg-slate-50/75 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">
                                            <a href="{{ route('books.show', $borrowing->book_id) }}" class="hover:underline text-slate-900">
                                                {{ $borrowing->book->title }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            Penulis: {{ $borrowing->book->author }} • <span class="font-mono text-slate-600">{{ $borrowing->book->isbn }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-700">
                                        {{ $borrowing->borrowed_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @php
                                            $isOverdue = $borrowing->due_date->isPast();
                                        @endphp
                                        <div class="{{ $isOverdue ? 'text-red-700 font-semibold' : 'text-slate-700' }}">
                                            {{ $borrowing->due_date->format('d M Y') }}
                                        </div>
                                        @if($isOverdue)
                                            <span class="inline-flex items-center text-[11px] text-red-600 mt-0.5">
                                                <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5 mr-1" />
                                                Jatuh Tempo
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-400">
                                                ({{ $borrowing->due_date->diffForHumans() }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge status="borrowed" label="Sedang Dipinjam" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('borrowings.return', $borrowing->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-xs focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors">
                                                <x-heroicon-o-arrow-up-tray class="w-3.5 h-3.5 mr-1.5" />
                                                Kembalikan Buku
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Bagian 2: Riwayat Peminjaman Selesai -->
    <div class="pt-4">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Riwayat Pengembalian Selesai</h2>

        @if($historyBorrowings->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-6 text-center shadow-xs">
                <p class="text-xs text-slate-500 italic">Belum ada riwayat buku yang telah dikembalikan.</p>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Buku</th>
                                <th class="px-6 py-3.5">Tanggal Pinjam</th>
                                <th class="px-6 py-3.5">Tanggal Kembali</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal">
                            @foreach($historyBorrowings as $history)
                                <tr class="hover:bg-slate-50/75 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">
                                            <a href="{{ route('books.show', $history->book_id) }}" class="hover:underline text-slate-900">
                                                {{ $history->book->title }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $history->book->author }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600">
                                        {{ $history->borrowed_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-700 font-medium">
                                        {{ $history->returned_at ? $history->returned_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge status="returned" label="Dikembalikan" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('books.show', $history->book_id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50">
                                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-3.5 h-3.5 mr-1 text-slate-500" />
                                            Beri Ulasan
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($historyBorrowings->hasPages())
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200">
                        {{ $historyBorrowings->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
