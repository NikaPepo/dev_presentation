@extends('layouts.app')

@section('title', 'Metrics')
@section('description', 'Live application metrics — recent events and 24h summary.')

@section('content')
    <div x-data="{ tab: 'recent' }">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-400 font-medium mb-2">Observability</p>
                <h1 class="text-3xl font-bold text-white">Metrics</h1>
                <p class="mt-2 text-slate-400 text-sm">Application-level counters and gauges recorded by <code class="font-mono">MetricsService</code>.</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-8">
            @foreach($summary as $name => $agg)
                <div class="rounded-xl border border-white/10 bg-slate-900/40 p-4">
                    <p class="text-xs uppercase tracking-widest text-slate-500 mb-1 truncate" title="{{ $name }}">{{ $name }}</p>
                    <p class="text-2xl font-bold text-white">{{ $agg['count'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        avg {{ number_format($agg['avg'], 2) }} · max {{ number_format($agg['max'], 2) }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-1 border-b border-white/5 mb-4">
            <button @click="tab = 'recent'"
                    :class="tab === 'recent' ? 'text-indigo-300 border-b-2 border-indigo-400' : 'text-slate-400 hover:text-slate-200'"
                    class="px-4 py-2 text-sm font-medium transition">Recent ({{ $recent->count() }})</button>
            <button @click="tab = 'summary'"
                    :class="tab === 'summary' ? 'text-indigo-300 border-b-2 border-indigo-400' : 'text-slate-400 hover:text-slate-200'"
                    class="px-4 py-2 text-sm font-medium transition">24h summary</button>
        </div>

        <div x-show="tab === 'recent'" x-cloak>
            @if($recent->isEmpty())
                <div class="rounded-xl border border-dashed border-white/10 p-8 text-center text-slate-500">
                    No metrics recorded yet. Submit a contact form or hit <code class="font-mono">POST /api/contact</code>.
                </div>
            @else
                <div class="rounded-xl border border-white/10 bg-slate-900/40 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-950/50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="text-left px-4 py-2.5">When</th>
                                <th class="text-left px-4 py-2.5">Name</th>
                                <th class="text-left px-4 py-2.5">Type</th>
                                <th class="text-right px-4 py-2.5">Value</th>
                                <th class="text-left px-4 py-2.5">Tags</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($recent as $m)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-2.5 text-slate-400 text-xs font-mono">{{ $m->occurred_at->diffForHumans() }}</td>
                                    <td class="px-4 py-2.5 text-slate-200 font-mono text-xs">{{ $m->name }}</td>
                                    <td class="px-4 py-2.5"><span class="text-xs px-2 py-0.5 rounded bg-white/5 text-slate-400">{{ $m->type }}</span></td>
                                    <td class="px-4 py-2.5 text-right font-mono">{{ number_format((float)$m->value, 4) }}</td>
                                    <td class="px-4 py-2.5 text-xs text-slate-500 font-mono">{{ $m->tags ? json_encode($m->tags) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div x-show="tab === 'summary'" x-cloak>
            <div class="rounded-xl border border-white/10 bg-slate-900/40 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-950/50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="text-left px-4 py-2.5">Metric</th>
                            <th class="text-right px-4 py-2.5">Count</th>
                            <th class="text-right px-4 py-2.5">Sum</th>
                            <th class="text-right px-4 py-2.5">Avg</th>
                            <th class="text-right px-4 py-2.5">Min</th>
                            <th class="text-right px-4 py-2.5">Max</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($summary as $name => $agg)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-2.5 font-mono text-xs text-slate-200">{{ $name }}</td>
                                <td class="px-4 py-2.5 text-right font-mono">{{ $agg['count'] }}</td>
                                <td class="px-4 py-2.5 text-right font-mono">{{ number_format($agg['sum'], 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono">{{ number_format($agg['avg'], 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono">{{ number_format($agg['min'], 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono">{{ number_format($agg['max'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <style>[x-cloak] { display: none !important; }</style>
@endpush
