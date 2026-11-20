@props([
    'book',
])

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between h-full">
    <div>
        <!-- Kategori & Status Stok -->
        <div class="flex items-center justify-between mb-3 gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200">
                {{ $book->category }}
            </span>
            @if($book->is_archived)
                <x-status-badge status="archived" label="Diarsipkan" />
            @else
                <x-status-badge :status="$book->stock > 0 ? 'available' : 'unavailable'" 
                                :label="$book->stock > 0 ? 'Stok: ' . $book->stock : 'Stok Habis'" />
            @endif
        </div>

        <!-- Judul & Penulis -->
        <h3 class="text-base font-semibold text-slate-900 leading-snug mb-1 line-clamp-2">
            {{ $book->title }}
        </h3>
        <p class="text-xs text-slate-500 mb-3">
            Oleh: <span class="text-slate-700 font-medium">{{ $book->author }}</span>
        </p>

        <!-- Meta ISBN -->
        <div class="flex items-center text-xs text-slate-500 mb-4 bg-slate-50 px-2.5 py-1.5 rounded border border-slate-200 font-mono">
            <x-heroicon-o-book-open class="w-3.5 h-3.5 mr-1.5 text-slate-400" />
            <span>ISBN: {{ $book->isbn }}</span>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <a href="{{ url('/books/' . $book->id) }}" 
           class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
            Lihat Detail
        </a>
        @auth
            @if(!$book->is_archived && $book->stock > 0 && !auth()->user()->isAdmin())
                <form method="POST" action="{{ url('/borrowings') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <button type="submit" 
                            class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                        <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 mr-1" />
                        Pinjam
                    </button>
                </form>
            @endif
        @endauth
    </div>
</div>
