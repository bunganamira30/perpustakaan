@extends('vuln.layout')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-300 p-6 shadow-xs">
        <div class="flex items-center space-x-2 mb-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                SEC-08 Target Uji: SQL Injection (Vulnerable)
            </span>
        </div>
        <h1 class="text-xl font-bold text-slate-900">Katalog Buku Versi Rentan (SQL Injection Demo)</h1>
        <p class="text-xs text-slate-600 mt-1">
            Fitur pencarian di bawah menyusun kueri SQL menggunakan penggabungan string langsung tanpa prepared statements.
        </p>

        <!-- Form Pencarian Rentan -->
        <form method="GET" action="{{ route('vuln.books.index') }}" class="mt-4 flex gap-2">
            <div class="relative flex-grow">
                <input type="text" name="q" value="{{ $searchTerm }}" 
                       placeholder="Coba payload: ' OR '1'='1 --"
                       class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold text-xs rounded-lg shadow-xs">
                Cari (Query Rentan)
            </button>
        </form>

        <!-- Kueri SQL Mentah yang Dieksekusi -->
        @if($executedQuery)
            <div class="mt-4 p-3 bg-slate-900 text-red-400 font-mono text-xs rounded-lg overflow-x-auto border border-slate-700">
                <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Kueri SQL Mentah yang Dieksekusi:</span>
                <code>{{ $executedQuery }}</code>
            </div>
        @endif
    </div>

    <!-- Hasil Daftar Buku -->
    <div class="bg-white rounded-xl border border-slate-300 shadow-xs overflow-hidden">
        <div class="px-6 py-3.5 bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700 uppercase">
            Hasil Pencarian: {{ count($books) }} Buku Ditemukan
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($books as $b)
                <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="font-bold text-slate-900 text-sm">
                                <a href="{{ route('vuln.books.show', $b->id) }}" class="hover:underline">
                                    {{ $b->title }}
                                </a>
                            </h2>
                            @if($b->is_archived)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    Buku Arsip Bocor via SQLi
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Penulis: {{ $b->author }} • ISBN: {{ $b->isbn }} • Stok: {{ $b->stock }}</p>
                    </div>
                    <div>
                        <a href="{{ route('vuln.books.show', $b->id) }}" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 rounded border border-slate-300">
                            Lihat & Ulas (XSS)
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-500 italic">
                    Buku tidak ditemukan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
