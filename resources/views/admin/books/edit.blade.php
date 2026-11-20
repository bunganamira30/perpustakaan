@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Koleksi Buku</h1>
        <p class="text-sm text-slate-500 mt-1">Perbarui informasi katalog buku #{{ $book->id }}</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('admin.books.update', $book->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Judul Buku -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Judul Buku
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $book->title) }}" 
                       required 
                       class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penulis -->
            <div>
                <label for="author" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Penulis Buku
                </label>
                <input type="text" 
                       name="author" 
                       id="author" 
                       value="{{ old('author', $book->author) }}" 
                       required 
                       class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('author') border-red-500 @enderror">
                @error('author')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- ISBN & Kategori (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="isbn" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor ISBN
                    </label>
                    <input type="text" 
                           name="isbn" 
                           id="isbn" 
                           value="{{ old('isbn', $book->isbn) }}" 
                           required 
                           class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('isbn') border-red-500 @enderror">
                    @error('isbn')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori
                    </label>
                    <input type="text" 
                           name="category" 
                           id="category" 
                           value="{{ old('category', $book->category) }}" 
                           required 
                           class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('category') border-red-500 @enderror">
                    @error('category')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stok Eksemplar -->
            <div>
                <label for="stock" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Jumlah Stok Fisik
                </label>
                <input type="number" 
                       name="stock" 
                       id="stock" 
                       value="{{ old('stock', $book->stock) }}" 
                       min="0" 
                       required 
                       class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('stock') border-red-500 @enderror">
                @error('stock')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Pengarsipan (BR-05) -->
            <div class="pt-2">
                <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                    <input type="checkbox" 
                           name="is_archived" 
                           value="1" 
                           {{ old('is_archived', $book->is_archived) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    <span class="font-medium">Arsipkan buku ini (sembunyikan dari katalog publik tanpa menghapus riwayat)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('admin.books.index') }}" class="text-xs font-medium text-slate-600 hover:underline">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-xs">
                    <x-heroicon-o-check class="w-4 h-4 mr-2" />
                    Perbarui Data Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
