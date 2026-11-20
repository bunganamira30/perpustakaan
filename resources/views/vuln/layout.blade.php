<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIP-Aman (Vulnerable Demo)' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-900 bg-slate-100 antialiased">
    <!-- Banner Peringatan Celah Rentan Terisolasi -->
    <div class="bg-red-700 text-white px-4 py-2.5 text-xs font-bold text-center tracking-wide flex items-center justify-center space-x-2 shadow-md">
        <span class="px-2 py-0.5 rounded bg-white text-red-800 uppercase font-black text-[10px]">Peringatan Keamanan</span>
        <span>MODUL DEMO CELAH RENTAN TERISOLASI (HANYA LOCALHOST 127.0.0.1:8001 - DILARANG DIUNGGAH KE INTERNET)</span>
    </div>

    <!-- Header Demo Rentan -->
    <header class="bg-white border-b border-slate-300 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('vuln.books.index') }}" class="flex items-center space-x-2 text-slate-900 font-bold text-base">
                    <span class="p-1.5 bg-red-600 text-white rounded-lg">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    </span>
                    <span>SIP Demo Rentan</span>
                    <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded border border-red-200">v0.1-VULN</span>
                </a>
                <nav class="flex space-x-2 text-xs">
                    <a href="{{ route('vuln.books.index') }}" class="px-3 py-1.5 rounded-md font-semibold text-slate-700 hover:bg-slate-100">
                        Katalog Rentan (SQLi)
                    </a>
                </nav>
            </div>
            <div class="flex items-center space-x-2 text-xs">
                <span class="text-slate-500 font-mono">Cookie: sip_vuln_session | Port: 8001 (Loopback)</span>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer Demo Rentan -->
    <footer class="bg-white border-t border-slate-300 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            Lingkungan Laboratorium Uji Komparatif Keamanan Komputer • Operator: bunga0120 (Host-Only 127.0.0.1)
        </div>
    </footer>
</body>
</html>
