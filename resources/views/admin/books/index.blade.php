@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Admin -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Katalog Buku</h1>
            <p class="text-sm text-slate-500 mt-1">Panel pengelolaan koleksi buku, ketersediaan stok, dan pengarsipan</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.books.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-xs">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Tambah Buku Baru
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian Admin -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.books.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-8 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $searchQuery }}" 
                       placeholder="Cari judul, penulis, atau ISBN..."
                       class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="archived" {{ $statusFilter === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full h-full flex justify-center items-center py-2 px-3 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Data Buku -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Judul & Penulis</th>
                        <th class="px-6 py-3.5">ISBN</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Stok</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($books as $book)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">#{{ $book->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $book->title }}</div>
                                <div class="text-xs text-slate-500">{{ $book->author }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $book->isbn }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 text-xs rounded bg-sky-50 text-sky-700 border border-sky-200">
                                    {{ $book->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $book->stock }}</td>
                            <td class="px-6 py-4">
                                @if($book->is_archived)
                                    <x-status-badge status="archived" label="Diarsipkan" />
                                @else
                                    <x-status-badge status="available" label="Aktif" />
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.books.edit', $book->id) }}" 
                                   class="inline-flex items-center text-xs font-semibold text-slate-700 hover:text-slate-900 underline">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.books.archive', $book->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin mengubah status arsip buku ini?')"
                                            class="inline-flex items-center text-xs font-semibold {{ $book->is_archived ? 'text-emerald-700 hover:text-emerald-900' : 'text-amber-700 hover:text-amber-900' }} underline">
                                        {{ $book->is_archived ? 'Aktifkan' : 'Arsipkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-xs text-slate-500">
                                Tidak ada data buku yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
