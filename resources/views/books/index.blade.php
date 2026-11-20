@extends('layouts.app')

@section('content')
<div class="mb-8">
    <!-- Header Bagian -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Koleksi Pustaka</h1>
            <p class="text-sm text-slate-500 mt-1">Jelajahi buku referensi, modul praktikum, dan literatur keamanan siber</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <span class="inline-flex items-center text-xs font-medium text-slate-600 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-xs">
                <x-heroicon-o-book-open class="w-4 h-4 mr-1.5 text-slate-500" />
                Total {{ $books->total() }} Buku Tersedia
            </span>
        </div>
    </div>

    <!-- Form Pencarian Aman (SEC-08 Safe Parameterized Search) -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('books.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Input Kata Kunci -->
            <div class="sm:col-span-8 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $searchQuery }}" 
                       placeholder="Cari berdasarkan judul, penulis, atau ISBN..."
                       class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="category" 
                        class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Cari -->
            <div class="sm:col-span-1">
                <button type="submit" 
                        class="w-full h-full flex justify-center items-center py-2 px-3 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-xs">
                    Cari
                </button>
            </div>
        </form>

        @if(!empty($searchQuery))
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                <span>Hasil pencarian untuk: <strong class="text-slate-900 font-mono">"{{ $searchQuery }}"</strong></span>
                <a href="{{ route('books.index') }}" class="text-slate-700 font-semibold hover:underline">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>

    <!-- Grid Koleksi Buku -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <x-heroicon-o-book-open class="w-12 h-12 text-slate-300 mx-auto mb-3" />
            <h3 class="text-base font-semibold text-slate-900">Tidak ada koleksi buku yang ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Silakan periksa ejaan kata kunci pencarian Anda atau reset filter untuk melihat seluruh koleksi pustaka.
            </p>
            <div class="mt-4">
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200">
                    Tampilkan Semua Buku
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
