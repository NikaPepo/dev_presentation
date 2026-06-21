@props(['health' => null])

<nav class="sticky top-0 z-30 backdrop-blur-md bg-slate-950/70 border-b border-white/5">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-500 text-white font-bold">
                    {{ substr(config('developer.name'), 0, 1) }}
                </span>
                <span class="hidden sm:block font-medium group-hover:text-indigo-400 transition">
                    {{ config('developer.name') }}
                </span>
            </a>

            <div class="flex items-center gap-1 sm:gap-2">
                @php
                    $links = [
                        ['href' => '/', 'label' => 'Home'],
                        ['href' => '/api-demo', 'label' => 'API Demo'],
                        ['href' => '/health', 'label' => 'Health'],
                        ['href' => '/metrics', 'label' => 'Metrics'],
                        ['href' => '/docs', 'label' => 'Docs'],
                    ];
                    $current = request()->path();
                @endphp
                @foreach($links as $link)
                    <a href="{{ $link['href'] }}"
                       class="px-2.5 py-1.5 text-sm rounded-md transition
                              {{ $current === ltrim($link['href'], '/')
                                    ? 'text-indigo-300 bg-white/5'
                                    : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>
