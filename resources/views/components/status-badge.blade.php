@props(['status' => 'ok'])

@php
    $map = [
        'ok'       => ['label' => 'Operational', 'dot' => 'bg-emerald-400', 'text' => 'text-emerald-300', 'bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20'],
        'degraded' => ['label' => 'Degraded',    'dot' => 'bg-amber-400',   'text' => 'text-amber-300',   'bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20'],
        'fail'     => ['label' => 'Down',        'dot' => 'bg-rose-400',    'text' => 'text-rose-300',    'bg' => 'bg-rose-500/10',    'border' => 'border-rose-500/20'],
    ];
    $s = $map[$status] ?? $map['ok'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium border {$s['bg']} {$s['border']} {$s['text']}"]) }}>
    <span class="relative flex h-2 w-2">
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $s['dot'] }} opacity-75"></span>
        <span class="relative inline-flex h-2 w-2 rounded-full {{ $s['dot'] }}"></span>
    </span>
    {{ $s['label'] }}
</span>
