@extends('vuln.layout')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-300 p-6 shadow-xs">
        <div class="flex items-center space-x-2 mb-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                SEC-10 Target Uji: IDOR (Insecure Direct Object Reference)
            </span>
        </div>
        <h1 class="text-xl font-bold text-slate-900">Detail Peminjaman Versi Rentan (IDOR Demo)</h1>
        <p class="text-xs text-slate-600 mt-1">
            Data peminjaman ini diakses langsung berdasarkan ID di URL tanpa pemeriksaan otorisasi atau policy kepemilikan.
        </p>

        <div class="mt-6 p-4 rounded-lg bg-red-50 border border-red-200 space-y-3 text-xs">
            <div class="flex justify-between pb-2 border-b border-red-200">
                <span class="font-bold text-red-900">ID Transaksi Peminjaman:</span>
                <span class="font-mono font-bold text-red-900">#{{ $borrowing->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Buku yang Dipinjam:</span>
                <span class="font-semibold text-slate-900">{{ $borrowing->book_title }} ({{ $borrowing->book_isbn }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Nama Peminjam (Korban IDOR):</span>
                <span class="font-semibold text-slate-900">{{ $borrowing->user_name }} ({{ $borrowing->user_email }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Tanggal Pinjam:</span>
                <span class="font-semibold text-slate-900">{{ $borrowing->borrowed_at }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Batas Waktu Pengembalian:</span>
                <span class="font-semibold text-slate-900">{{ $borrowing->due_date }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Status Peminjaman:</span>
                <span class="font-bold uppercase text-red-700">{{ $borrowing->status }}</span>
            </div>
        </div>

        <div class="mt-4 p-3 bg-slate-900 text-slate-300 font-mono text-[11px] rounded-lg">
            <span class="text-amber-400 block font-bold mb-1">Catatan Audit Keamanan:</span>
            <span>Rute ini tidak memanggil Gate::authorize('view', $borrowing). Pengguna mana pun (bahkan tamu tanpa login) dapat melihat riwayat pembacaan anggota lain hanya dengan mengganti parameter URL /vuln/borrowings/{id}.</span>
        </div>
    </div>
</div>
@endsection
