@props(['eyebrow' => null, 'title', 'description' => null])

<div class="mb-8">
    @if($eyebrow)
        <p class="text-xs uppercase tracking-widest text-indigo-400 font-medium mb-2">{{ $eyebrow }}</p>
    @endif
    <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $title }}</h2>
    @if($description)
        <p class="mt-2 text-slate-400 max-w-2xl">{{ $description }}</p>
    @endif
</div>
