@extends('layouts.app')

@section('title', 'Health')
@section('description', 'Service health check — DB, cache, AI.')

@section('content')
    <div x-data="{
        result: @js($result),
        loading: false,
        async refresh() {
            this.loading = true;
            try {
                const r = await fetch('/api/health', { headers: { 'Accept': 'application/json' } });
                this.result = await r.json();
            } finally { this.loading = false; }
        }
     }" x-init="setInterval(() => refresh(), 15000)">

        <div class="flex items-start justify-between gap-4 mb-8">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-400 font-medium mb-2">Observability</p>
                <h1 class="text-3xl font-bold text-white">Service health</h1>
                <p class="mt-2 text-slate-400 text-sm">Auto-refreshes every 15 seconds. Last checked just now.</p>
            </div>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$result['status']" />
                <button @click="refresh()" :disabled="loading"
                        class="text-sm px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-slate-200 transition disabled:opacity-50">
                    <span x-text="loading ? 'Checking…' : 'Refresh'"></span>
                </button>
            </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-4 mb-6">
            <template x-for="(value, name) in result.components" :key="name">
                <div class="rounded-xl border border-white/10 bg-slate-900/40 p-5">
                    <p class="text-xs uppercase tracking-widest text-slate-500 mb-2" x-text="name"></p>
                    <p class="text-2xl font-bold"
                       :class="value === 'ok' ? 'text-emerald-300' : value === 'fail' ? 'text-rose-300' : 'text-amber-300'"
                       x-text="value"></p>
                </div>
            </template>
        </div>

        <x-alert :type="$result['status'] === 'ok' ? 'success' : ($result['status'] === 'degraded' ? 'warning' : 'error')">
            @if($result['status'] === 'ok')
                All components are operational. The service is fully functional.
            @elseif($result['status'] === 'degraded')
                The service is partially functional — some non-critical components are degraded. The API still accepts requests.
            @else
                Critical component failure. The service cannot reliably handle requests right now.
            @endif
        </x-alert>

        <div class="mt-8 text-sm text-slate-400">
            <p>Powered by <code class="font-mono text-slate-300">App\Services\HealthService</code> · backend returns 200 on degraded, 503 on fail.</p>
        </div>
    </div>
@endsection
