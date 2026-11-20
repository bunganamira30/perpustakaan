<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Autentikasi' }} - SIP Pustaka Aman</title>

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-900 bg-slate-50 antialiased justify-between">
    <!-- Header Guest -->
    <div class="pt-8 sm:pt-12 text-center px-4">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 text-slate-900 hover:text-slate-700">
            <span class="p-2.5 bg-slate-900 text-white rounded-xl shadow-xs">
                <x-heroicon-o-book-open class="w-8 h-8" />
            </span>
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">SIP Pustaka Aman</h1>
                <p class="text-xs text-slate-500 font-medium">Sistem Informasi Perpustakaan Mini Aman</p>
            </div>
        </a>
    </div>

    <!-- Kontainer Form Kartu Terpusat -->
    <main class="w-full max-w-md mx-auto px-4 py-6">
        <div class="mb-4">
            <x-alert />
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-xs border border-slate-200">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    <!-- Footer Guest -->
    <footer class="py-6 text-center text-xs text-slate-500">
        <div class="inline-flex items-center space-x-1.5 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-xs">
            <x-heroicon-o-shield-check class="w-3.5 h-3.5 text-emerald-600" />
            <span class="font-mono text-slate-700">Operator: bunga0120 | Enkripsi AES-256-GCM Aktif</span>
        </div>
    </footer>
</body>
</html>
