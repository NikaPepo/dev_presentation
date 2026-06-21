<div x-data="{
        message: '',
        loading: false,
        result: null,
        error: null,
        async analyze() {
            if (!this.message || this.message.length < 10) { this.error = 'Please enter at least 10 characters.'; return; }
            this.loading = true; this.error = null; this.result = null;
            try {
                const r = await fetch('/api/analyze', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ message: this.message }),
                });
                const data = await r.json();
                if (!r.ok) {
                    this.error = data.message || data.error || ('HTTP ' + r.status);
                } else {
                    this.result = data;
                }
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        }
     }">

    <x-section-heading
        eyebrow="AI integration"
        title="Try the AI analyzer"
        description="Type any message — I'll send it to OpenAI and show you the live analysis (sentiment, summary). Powered by the very API that backs this site." />

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-white/10 bg-slate-900/40 p-5">
            <label for="ai-input" class="block text-sm font-medium text-slate-200 mb-2">Your message</label>
            <textarea id="ai-input" x-model="message" rows="6"
                      placeholder="Type your message here..."
                      class="w-full rounded-lg bg-slate-950/70 border border-white/10 px-3 py-2 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none transition"></textarea>

            <div class="mt-3 flex items-center justify-between gap-3">
                <p class="text-xs text-slate-500">No data is saved — this hits <code class="font-mono">POST /api/analyze</code> live.</p>
                <button @click="analyze()" :disabled="loading"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium transition">
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    <span x-text="loading ? 'Analyzing…' : 'Analyze'"></span>
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-slate-900/40 p-5 min-h-[260px]">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-medium mb-3">Result</p>

            <div x-show="error" x-cloak>
                <x-alert type="error" title="Request failed">
                    <span x-text="error"></span>
                </x-alert>
            </div>

            <div x-show="!result && !error && !loading" x-cloak class="flex flex-col items-center justify-center h-48 text-slate-500">
                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <p class="text-sm">Send a message to see the analysis</p>
            </div>

            <div x-show="loading" x-cloak class="flex items-center justify-center h-48">
                <div class="animate-pulse text-slate-400 text-sm">Talking to OpenAI…</div>
            </div>

            <div x-show="result" x-cloak class="space-y-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-400">Sentiment:</span>
                    <span class="font-mono px-2 py-0.5 rounded text-xs"
                          :class="{
                              'bg-emerald-500/20 text-emerald-300': result?.sentiment === 'positive',
                              'bg-amber-500/20 text-amber-300': result?.sentiment === 'neutral',
                              'bg-rose-500/20 text-rose-300': result?.sentiment === 'negative',
                          }"
                          x-text="result?.sentiment || 'unknown'"></span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Summary</p>
                    <p class="text-slate-200 leading-relaxed" x-text="result?.summary || 'No summary returned'"></p>
                </div>
                <div class="text-xs text-slate-500 pt-2 border-t border-white/5">
                    Category: <span class="font-mono text-slate-400" x-text="result?.category"></span>
                    · Confidence: <span class="font-mono text-slate-400" x-text="result?.confidence"></span>
                </div>
            </div>
        </div>
    </div>
</div>
