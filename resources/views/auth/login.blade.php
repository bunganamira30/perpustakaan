@extends('layouts.guest')

@section('content')
<div class="mb-6 text-center">
    <h2 class="text-xl font-bold text-slate-900">Masuk ke Akun Anda</h2>
    <p class="text-xs text-slate-500 mt-1">Gunakan username atau email dan kata sandi Anda</p>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <!-- Username / Email -->
    <div>
        <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
            Username / Email
        </label>
        <div class="relative rounded-md shadow-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-heroicon-o-user class="w-4 h-4" />
            </div>
            <input type="text" 
                   name="username" 
                   id="username" 
                   value="{{ old('username') }}" 
                   required 
                   autofocus
                   autocomplete="username"
                   placeholder="Contoh: budi_santoso"
                   class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 @error('username') border-red-500 @enderror">
        </div>
        @error('username')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
            Kata Sandi
        </label>
        <div class="relative rounded-md shadow-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-heroicon-o-lock-closed class="w-4 h-4" />
            </div>
            <input type="password" 
                   name="password" 
                   id="password" 
                   required 
                   maxlength="72"
                   autocomplete="current-password"
                   placeholder="Minimal 8 karakter"
                   class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 @error('password') border-red-500 @enderror">
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center justify-between text-xs pt-1">
        <label class="flex items-center space-x-2 cursor-pointer text-slate-600">
            <input type="checkbox" 
                   name="remember" 
                   id="remember" 
                   {{ old('remember') ? 'checked' : '' }}
                   class="rounded border-slate-300 text-slate-900 focus:ring-slate-900">
            <span>Ingat sesi saya di peramban ini</span>
        </label>
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button type="submit" 
                class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 shadow-xs">
            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
            Masuk ke Sistem
        </button>
    </div>
</form>

<div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
    <span>Belum memiliki akun anggota?</span>
    <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline ml-1">
        Daftar Sekarang
    </a>
</div>
@endsection
