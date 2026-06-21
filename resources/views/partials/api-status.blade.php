<div x-data="{
        status: @js($health['status']),
        components: @js($health['components']),
        async refresh() {
            try {
                const r = await fetch('/api/health', { headers: { 'Accept': 'application/json' } });
                const data = await r.json();
                this.status = data.status;
                this.components = data.components;
            } catch (e) { /* ignore */ }
        }
     }"
     x-init="setInterval(() => refresh(), 30000)"
     class="rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur p-6 shadow-2xl shadow-indigo-500/5">

    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500 font-medium">System status</p>
            <p class="mt-1 text-sm text-slate-400">Live · auto-refresh every 30s</p>
        </div>
        <x-status-badge :status="$health['status']" />
    </div>

    <div class="space-y-2.5">
        <template x-for="(value, name) in components" :key="name">
            <div class="flex items-center justify-between text-sm py-1.5 px-3 rounded-lg bg-slate-950/50">
                <span class="text-slate-300 capitalize" x-text="name"></span>
                <span :class="value === 'ok' ? 'text-emerald-400' : value === 'fail' ? 'text-rose-400' : 'text-amber-400'"
                      class="font-mono text-xs uppercase tracking-wide"
                      x-text="value"></span>
            </div>
        </template>
    </div>

    <a href="/health" class="mt-4 inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-indigo-300 transition">
        Detailed health →
    </a>
</div>
