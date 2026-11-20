<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SIP-Aman' }} - Sistem Informasi Perpustakaan</title>

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-900 bg-slate-50 antialiased">
    <!-- Navbar Atas -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Sisi Kiri: Brand & Menu Utama -->
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 text-slate-900 font-bold text-lg tracking-tight hover:text-slate-700">
                        <span class="p-1.5 bg-slate-900 text-white rounded-lg">
                            <x-heroicon-o-book-open class="w-5 h-5" />
                        </span>
                        <span>SIP Pustaka Aman</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">v1.0</span>
                    </a>

                    <!-- Navigasi Desktop -->
                    <nav class="hidden md:flex space-x-4">
                        <a href="{{ url('/books') }}" 
                           class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->is('books*') && !request()->is('admin*') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <x-heroicon-o-book-open class="w-4 h-4 mr-1.5" />
                            Katalog Buku
                        </a>

                        @auth
                            <a href="{{ url('/my-borrowings') }}" 
                               class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->is('my-borrowings*') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                                Pinjaman Saya
                            </a>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ url('/admin/books') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->is('admin*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <x-heroicon-o-shield-check class="w-4 h-4 mr-1.5" />
                                    Kelola Buku (Admin)
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Sisi Kanan: Status Pengguna & Autentikasi -->
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                            <x-heroicon-o-user class="w-4 h-4 text-slate-500" />
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <span class="text-xs uppercase px-2 py-0.5 rounded font-semibold {{ auth()->user()->isAdmin() ? 'bg-slate-900 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ auth()->user()->role }}
                            </span>
                        </div>

                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 bg-white hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-1.5 text-slate-500" />
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900">
                            Masuk
                        </a>
                        <a href="{{ url('/register') }}" 
                           class="inline-flex items-center px-3.5 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                            Daftar Anggota
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Kontainer Pesan Flash / Notifikasi Global -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <x-alert />
    </div>

    <!-- Konten Utama Halaman -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer Akademik Baku -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <div>
                <span class="font-semibold text-slate-700">Tugas Pengganti UTS: Keamanan Sistem Komputer</span>
                <span class="mx-1.5">•</span>
                <span>Bunga Namira (NIM: 230170120)</span>
            </div>
            <div class="flex items-center space-x-2 bg-slate-100 px-2.5 py-1 rounded border border-slate-200">
                <x-heroicon-o-shield-check class="w-3.5 h-3.5 text-emerald-600" />
                <span class="font-mono text-slate-700">Operator: bunga0120 | Enkripsi AES-256-GCM Aktif</span>
            </div>
        </div>
    </footer>
</body>
</html>
