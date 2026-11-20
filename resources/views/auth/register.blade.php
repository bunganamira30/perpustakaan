@extends('layouts.guest')

@section('content')
<div class="mb-5 text-center">
    <h2 class="text-xl font-bold text-slate-900">Pendaftaran Anggota Baru</h2>
    <p class="text-xs text-slate-500 mt-1">Lengkapi data diri untuk mengakses fasilitas perpustakaan</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-3.5">
    @csrf

    <!-- Nama Lengkap -->
    <div>
        <label for="full_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
            Nama Lengkap
        </label>
        <input type="text" 
               name="full_name" 
               id="full_name" 
               value="{{ old('full_name') }}" 
               required 
               autofocus
               placeholder="Contoh: Budi Santoso"
               class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('full_name') border-red-500 @enderror">
        @error('full_name')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Username & Email (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Username
            </label>
            <input type="text" 
                   name="username" 
                   id="username" 
                   value="{{ old('username') }}" 
                   required 
                   placeholder="budi_santoso"
                   class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('username') border-red-500 @enderror">
            @error('username')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Email
            </label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email') }}" 
                   required 
                   placeholder="budi@contoh.test"
                   class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Nomor Telepon (Sensitif - Terenkripsi AES-256-GCM) -->
    <div>
        <div class="flex items-center justify-between mb-1">
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                Nomor Telepon
            </label>
            <span class="inline-flex items-center text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                <x-heroicon-o-shield-check class="w-3 h-3 mr-1 text-emerald-600" />
                Enkripsi AES-256-GCM
            </span>
        </div>
        <input type="tel" 
               name="phone" 
               id="phone" 
               value="{{ old('phone') }}" 
               required 
               placeholder="081234567890"
               class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('phone') border-red-500 @enderror">
        @error('phone')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Alamat Tinggal -->
    <div>
        <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
            Alamat (Opsional)
        </label>
        <textarea name="address" 
                  id="address" 
                  rows="2"
                  placeholder="Alamat domisili fiktif"
                  class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
        @error('address')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password & Konfirmasi (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Kata Sandi
            </label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   required 
                   maxlength="72"
                   placeholder="Min 8, maks 72"
                   class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Ulangi Sandi
            </label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   required 
                   maxlength="72"
                   placeholder="Konfirmasi sandi"
                   class="block w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button type="submit" 
                class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 shadow-xs">
            <x-heroicon-o-shield-check class="w-4 h-4 mr-2" />
            Daftar Anggota Sekarang
        </button>
    </div>
</form>

<div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
    <span>Sudah terdaftar sebagai anggota?</span>
    <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline ml-1">
        Masuk di Sini
    </a>
</div>
@endsection
