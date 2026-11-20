@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Halaman Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-900 text-white">
                    Administrator Panel
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <x-heroicon-o-lock-open class="w-3.5 h-3.5 mr-1" />
                    AES-256-GCM Authorized Decryption
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center">
                <x-heroicon-o-users class="w-7 h-7 mr-2.5 text-slate-800" />
                Data Anggota & Pengawasan Sirkulasi (Admin)
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Daftar anggota perpustakaan dengan data nomor telepon terdekripsi otomatis untuk pengawasan sirkulasi resmi (FR-010, FR-011).
            </p>
        </div>
        <div>
            <a href="{{ route('admin.books.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-xs">
                <x-heroicon-o-book-open class="w-4 h-4 mr-1.5" />
                Kelola Katalog Buku
            </a>
        </div>
    </div>

    <!-- Tabel Data Anggota -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Anggota</th>
                        <th class="px-6 py-3.5">Username</th>
                        <th class="px-6 py-3.5">Nomor Telepon (Didekripsi)</th>
                        <th class="px-6 py-3.5">Alamat</th>
                        <th class="px-6 py-3.5">Status Pinjaman</th>
                        <th class="px-6 py-3.5">Tanggal Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $member->full_name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $member->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-700">
                                {{ $member->user->username }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs font-semibold text-slate-900 bg-slate-100 px-2.5 py-1 rounded border border-slate-200 inline-flex items-center">
                                    <x-heroicon-o-phone class="w-3.5 h-3.5 mr-1 text-slate-500" />
                                    {{ $member->phone_encrypted }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $member->address ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $activeCount = $member->user->borrowings->count();
                                @endphp
                                @if($activeCount > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $activeCount }} Buku Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Tidak Ada Pinjaman
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $member->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-500 italic">
                                Belum ada data anggota yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
