@props(['type' => 'info', 'title' => null])

@php
    $styles = [
        'success' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-200',
        'error'   => 'bg-rose-500/10 border-rose-500/30 text-rose-200',
        'warning' => 'bg-amber-500/10 border-amber-500/30 text-amber-200',
        'info'    => 'bg-indigo-500/10 border-indigo-500/30 text-indigo-200',
    ];
    $cls = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border p-4 {$cls}"]) }}>
    @if($title)
        <p class="font-medium mb-1">{{ $title }}</p>
    @endif
    <div class="text-sm">{{ $slot }}</div>
</div>
