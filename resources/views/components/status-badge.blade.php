@props([
    'status' => 'available',
    'label' => null,
])

@php
    $configs = [
        'available' => [
            'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'icon' => 'heroicon-o-check-circle',
            'defaultLabel' => 'Tersedia',
        ],
        'unavailable' => [
            'class' => 'bg-red-50 text-red-800 border-red-200',
            'icon' => 'heroicon-o-x-circle',
            'defaultLabel' => 'Stok Habis',
        ],
        'borrowed' => [
            'class' => 'bg-amber-50 text-amber-900 border-amber-200',
            'icon' => 'heroicon-o-clock',
            'defaultLabel' => 'Dipinjam',
        ],
        'returned' => [
            'class' => 'bg-sky-50 text-sky-800 border-sky-200',
            'icon' => 'heroicon-o-arrow-path',
            'defaultLabel' => 'Dikembalikan',
        ],
        'archived' => [
            'class' => 'bg-slate-100 text-slate-700 border-slate-300',
            'icon' => 'heroicon-o-archive-box',
            'defaultLabel' => 'Diarsipkan',
        ],
    ];

    $cfg = $configs[$status] ?? [
        'class' => 'bg-slate-100 text-slate-700 border-slate-300',
        'icon' => 'heroicon-o-information-circle',
        'defaultLabel' => ucfirst($status),
    ];

    $displayLabel = $label ?? $cfg['defaultLabel'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $cfg['class'] }}">
    <x-dynamic-component :component="$cfg['icon']" class="w-3.5 h-3.5 mr-1 flex-shrink-0" />
    {{ $displayLabel }}
</span>
