<footer class="mt-24 border-t border-white/5">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-400">
                © {{ date('Y') }} {{ config('developer.name') }}.
                Built with Laravel {{ app()->version() }} + Alpine.js + Tailwind.
            </p>
            <div class="flex items-center gap-4">
                @php
                    $socials = array_filter(config('developer.social', []));
                @endphp
                @foreach($socials as $key => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="text-slate-400 hover:text-white transition text-sm">
                        {{ ucfirst($key) }}
                    </a>
                @endforeach
                <a href="mailto:{{ config('developer.email') }}"
                   class="text-slate-400 hover:text-white transition text-sm">
                    Email
                </a>
            </div>
        </div>
    </div>
</footer>
