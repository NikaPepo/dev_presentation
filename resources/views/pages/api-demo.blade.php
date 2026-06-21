@extends('layouts.app')

@section('title', 'API Demo')
@section('description', 'Interactive demo of every public API endpoint on this site.')

@section('content')
    <x-section-heading
        eyebrow="Hands-on"
        title="API playground"
        description="Click a button — your browser calls the endpoint, the JSON response is rendered below. Everything is live; everything is wired through the same code as the production API." />

    <div x-data="{
        endpoints: [
            { method: 'POST', path: '/api/contact', label: 'Submit a contact form', body: { name: 'Jane Doe', email: 'jane@example.com', phone: '+15555550100', message: 'I would like to discuss a partnership opportunity.', category: 'sales' } },
            { method: 'POST', path: '/api/analyze', label: 'AI analyze (no save)', body: { message: 'I have been waiting for a refund for three weeks. This is unacceptable.' } },
            { method: 'GET',  path: '/api/health',  label: 'Check service health',  body: null },
            { method: 'GET',  path: '/api/metrics',  label: 'Read recent metrics',    body: null },
            { method: 'GET',  path: '/api/metrics/summary', label: 'Aggregated metrics',  body: null },
        ],
        loading: null,
        response: null,
        async call(ep) {
            this.loading = ep.path;
            this.response = null;
            try {
                const r = await fetch(ep.path, {
                    method: ep.method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: ep.body ? JSON.stringify(ep.body) : undefined,
                });
                const text = await r.text();
                let parsed; try { parsed = JSON.parse(text); } catch { parsed = text; }
                this.response = { status: r.status, ok: r.ok, headers: Object.fromEntries(r.headers), body: parsed };
            } catch (e) {
                this.response = { status: 0, ok: false, body: e.message };
            } finally {
                this.loading = null;
            }
        }
     }" class="space-y-8">

        <div class="grid sm:grid-cols-2 gap-3">
            <template x-for="ep in endpoints" :key="ep.path">
                <button @click="call(ep)" :disabled="loading === ep.path"
                        class="text-left p-4 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 hover:border-indigo-500/40 disabled:opacity-50 transition group">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded"
                              :class="{
                                'bg-emerald-500/20 text-emerald-300': ep.method === 'GET',
                                'bg-indigo-500/20 text-indigo-300': ep.method === 'POST',
                                'bg-amber-500/20 text-amber-300': ep.method === 'PUT',
                                'bg-rose-500/20 text-rose-300': ep.method === 'DELETE',
                              }"
                              x-text="ep.method"></span>
                        <code class="text-sm text-slate-200 font-mono" x-text="ep.path"></code>
                    </div>
                    <p class="text-sm text-slate-400" x-text="ep.label"></p>
                </button>
            </template>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-white/5 bg-slate-950/40">
                <p class="text-sm font-medium text-slate-200">
                    <span x-show="!loading && !response" class="text-slate-500">Click an endpoint to see the response</span>
                    <span x-show="loading" class="text-indigo-300">Calling…</span>
                    <span x-show="response" class="flex items-center gap-2">
                        Response
                        <span :class="response?.ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'"
                              class="text-xs font-mono px-2 py-0.5 rounded"
                              x-text="response?.status"></span>
                    </span>
                </p>
                <button x-show="response"
                        x-copy="JSON.stringify(response?.body, null, 2)"
                        class="text-xs px-3 py-1 rounded border border-white/10 hover:bg-white/5 text-slate-300 transition">
                    Copy JSON
                </button>
            </div>
            <pre class="p-5 text-xs font-mono leading-relaxed text-slate-300 overflow-x-auto min-h-[200px] max-h-[500px]"><code x-text="response ? JSON.stringify(response.body, null, 2) : ''"></code></pre>
        </div>

        <x-alert type="info" title="About this playground">
            Each click fires a real request to <code class="font-mono">localhost</code>. The <strong>POST /api/contact</strong> endpoint is rate-limited
            (<code class="font-mono">5 / minute / IP</code>) — hit it six times and you'll see a <strong>429</strong>.
        </x-alert>
    </div>
@endsection
