@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-4">
        <a href="{{ route('books.index') }}" class="hover:text-slate-900">Katalog Buku</a>
        <span>/</span>
        <span class="text-slate-900 font-medium truncate">{{ $book->title }}</span>
    </nav>

    <!-- Kartu Rincian Buku -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200 mb-2">
                    {{ $book->category }}
                </span>
                <h1 class="text-2xl font-bold text-slate-900">{{ $book->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Penulis: <span class="font-medium text-slate-800">{{ $book->author }}</span>
                </p>
            </div>
            <div>
                @if($book->is_archived)
                    <x-status-badge status="archived" label="Diarsipkan" />
                @else
                    <x-status-badge :status="$book->stock > 0 ? 'available' : 'unavailable'" 
                                    :label="$book->stock > 0 ? 'Tersedia: ' . $book->stock . ' Eksemplar' : 'Stok Habis'" />
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 mb-6">
            <div>
                <span class="font-medium text-slate-500">Nomor ISBN:</span>
                <p class="font-mono text-slate-900 font-semibold text-sm mt-0.5">{{ $book->isbn }}</p>
            </div>
            <div>
                <span class="font-medium text-slate-500">Status Sirkulasi:</span>
                <p class="text-slate-900 font-semibold text-sm mt-0.5">
                    {{ $book->stock > 0 ? 'Dapat Dipinjam' : 'Menunggu Pengembalian' }}
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Sirkulasi -->
        <div class="flex flex-wrap items-center gap-3">
            @auth
                @if(!$book->is_archived)
                    @php
                        $isAlreadyBorrowed = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('book_id', $book->id)
                            ->where('status', 'borrowed')
                            ->exists();
                    @endphp

                    @if($isAlreadyBorrowed)
                        <div class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            <x-heroicon-o-clock class="w-4 h-4 mr-2 text-amber-600" />
                            Buku Sedang Anda Pinjam
                        </div>
                    @elseif($book->stock > 0)
                        <form method="POST" action="{{ route('books.borrow', $book->id) }}">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-xs transition-colors">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                                Ajukan Peminjaman
                            </button>
                        </form>
                    @else
                        <button type="button" disabled
                                class="inline-flex items-center px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-400 bg-slate-100 cursor-not-allowed">
                            <x-heroicon-o-no-symbol class="w-4 h-4 mr-2 text-slate-400" />
                            Stok Tidak Tersedia
                        </button>
                    @endif
                @else
                    <div class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                        <x-heroicon-o-archive-box class="w-4 h-4 mr-2 text-rose-600" />
                        Buku ini telah diarsipkan
                    </div>
                @endif
            @else
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 shadow-xs">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
                    Masuk untuk Meminjam
                </a>
            @endauth

            <a href="{{ route('books.index') }}" 
               class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-100">
                Kembali ke Katalog
            </a>
        </div>
    </div>

    <!-- Bagian Ulasan Pembaca (Contextual Escaping Anti-XSS SEC-09 Safe) -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 flex items-center">
                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 mr-2 text-slate-500" />
                Ulasan Pembaca ({{ $book->reviews->count() }})
            </h2>
        </div>

        <!-- Form Kirim Ulasan Buku Baru (FR-009) -->
        @auth
            <div class="mb-8 p-5 bg-slate-50 rounded-xl border border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-1.5 text-slate-700" />
                    Tulis Ulasan Buku
                </h3>
                <form method="POST" action="{{ route('reviews.store', $book->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Penilaian / Rating Bintang:</label>
                        <div class="flex items-center space-x-4">
                            @for($i = 5; $i >= 1; $i--)
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }}
                                           class="w-4 h-4 text-slate-900 focus:ring-slate-900 border-slate-300">
                                    <span class="ml-1 text-xs font-semibold text-slate-700">{{ $i }} ★</span>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="comment" class="block text-xs font-medium text-slate-700 mb-1.5">Komentar / Ulasan Pembaca (Kolom Teks Bebas):</label>
                        <textarea id="comment" name="comment" rows="3" required
                                  placeholder="Tulis ulasan Anda mengenai buku ini..."
                                  class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-slate-500 flex items-center">
                            <x-heroicon-o-shield-check class="w-3.5 h-3.5 mr-1 text-emerald-600" />
                            SEC-09 Safe: Dilindungi Contextual HTML Escaping & CSP
                        </span>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-xs focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors">
                            <x-heroicon-o-paper-airplane class="w-3.5 h-3.5 mr-1.5" />
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200 text-center text-xs text-slate-600">
                <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">Masuk ke akun Anda</a> untuk memberikan ulasan dan rating pada buku ini.
            </div>
        @endauth

        @if($book->reviews->count() > 0)
            <div class="space-y-4">
                @foreach($book->reviews as $review)
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-xs text-slate-900">{{ $review->user->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        <!-- SEC-09 Safe: Blade contextual escaping -->
                        <p class="text-sm text-slate-700 leading-relaxed break-words">
                            {{ $review->comment }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500 italic py-2">Belum ada ulasan untuk buku ini.</p>
        @endif
    </div>
</div>
@endsection
