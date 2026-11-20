@extends('vuln.layout')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-300 p-6 shadow-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-300 mb-1">
                    SEC-09 Target Uji: Stored XSS (Vulnerable)
                </span>
                <h1 class="text-2xl font-bold text-slate-900">{{ $book->title }}</h1>
                <p class="text-xs text-slate-500 mt-1">Penulis: {{ $book->author }} • ISBN: {{ $book->isbn }}</p>
            </div>
            <div>
                <a href="{{ route('vuln.books.index') }}" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 rounded border border-slate-300">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Tambah Ulasan Rentan -->
        <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">
                Kirim Ulasan Rentan (Stored XSS Test)
            </h2>
            <form method="POST" action="{{ route('vuln.reviews.store', $book->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Rating:</label>
                    <select name="rating" class="text-xs border border-slate-300 rounded px-2 py-1">
                        <option value="5">5 Bintang</option>
                        <option value="4">4 Bintang</option>
                        <option value="3">3 Bintang</option>
                        <option value="2">2 Bintang</option>
                        <option value="1">1 Bintang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Komentar / Payload XSS:</label>
                    <textarea name="comment" rows="2" 
                              placeholder="Masukkan payload: <script>alert('XSS-Terkonfirmasi-Bunga0120')</script>"
                              class="w-full px-3 py-2 text-xs border border-slate-300 rounded focus:outline-none focus:ring-1 focus:ring-red-600 font-mono"></textarea>
                </div>
                <button type="submit" class="px-3.5 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-semibold rounded shadow-xs">
                    Simpan Ulasan Mentah
                </button>
            </form>
        </div>

        <!-- Daftar Ulasan Rentan (UNESCAPED BLADE RAW OUTPUT) -->
        <div class="mt-6">
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                <span>Daftar Ulasan (Rendered via {!! '$review->comment' !!})</span>
            </h2>

            <div class="space-y-3">
                @forelse($reviews as $rev)
                    <div class="p-3 bg-red-50/50 rounded-lg border border-red-200">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-slate-900">{{ $rev->user_name }}</span>
                            <span class="text-amber-500 font-semibold">{{ $rev->rating }} ★</span>
                        </div>
                        <!-- VULNERABLE STORED XSS: Unescaped raw output without htmlspecialchars -->
                        <div class="text-xs text-slate-800 font-sans user-comment">
                            {!! $rev->comment !!}
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">Belum ada ulasan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
