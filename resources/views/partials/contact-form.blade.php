<div x-data="{
        form: { name: '', email: '', phone: '', message: '' },
        errors: {},
        loading: false,
        success: null,
        warnings: [],
        async submit() {
            this.loading = true; this.errors = {}; this.success = null; this.warnings = [];
            try {
                const r = await fetch('/api/contact', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '' },
                    body: JSON.stringify(this.form),
                });
                const data = await r.json();
                if (r.status === 422) {
                    this.errors = data.errors || {};
                } else if (r.status === 429) {
                    this.errors = { _rate: ['Too many requests. Please wait a minute and try again.'] };
                } else if (!r.ok) {
                    this.errors = { _general: [data.message || ('HTTP ' + r.status)] };
                } else {
                    this.success = data;
                    this.form = { name: '', email: '', phone: '', message: '' };
                    this.warnings = data.warnings || [];
                }
            } catch (e) {
                this.errors = { _general: [e.message] };
            } finally {
                this.loading = false;
            }
        }
     }">

    <x-section-heading
        eyebrow="Contact"
        title="Drop me a line" />

    <div class="grid lg:grid-cols-5 gap-6">
        <form @submit.prevent="submit()" class="lg:col-span-3 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-field name="name" label="Name" required :error="null"
                              x-bind:value="form.name" @input="form.name = $event.target.value" />
                <x-form-field name="email" label="Email" type="email" required
                              x-bind:value="form.email" @input="form.email = $event.target.value" />
            </div>
            <x-form-field name="phone" label="Phone" required placeholder="+7 495 999 99 99"
                          x-bind:value="form.phone" @input="form.phone = $event.target.value" />

            <x-form-field name="message" label="Message" type="textarea" required
                          placeholder="What's on your mind?"
                          x-bind:value="form.message" @input="form.message = $event.target.value" />

            <div class="flex items-center justify-between gap-3 pt-2">
                <p class="text-xs text-slate-500">All fields are required.</p>
                <button type="submit" :disabled="loading"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium transition">
                    <span x-text="loading ? 'Sending…' : 'Send message'"></span>
                </button>
            </div>
        </form>

        <div class="lg:col-span-2 space-y-3">
            <div x-show="success" x-cloak>
                <x-alert type="success" title="Message sent!">
                    Saved with ID <span class="font-mono" x-text="success?.id"></span>.
                    <template x-if="success?.aiSummary">
                        <p class="mt-2 text-sm"><strong class="text-emerald-200">AI:</strong> <span x-text="success?.aiSummary"></span></p>
                    </template>
                </x-alert>
            </div>

            <template x-if="warnings && warnings.length">
                <x-alert type="warning" title="Heads up">
                    <ul class="list-disc list-inside">
                        <template x-for="w in warnings" :key="w">
                            <li x-text="w"></li>
                        </template>
                    </ul>
                </x-alert>
            </template>

            <template x-if="errors._rate">
                <x-alert type="warning" title="Slow down">
                    <span x-text="errors._rate?.[0]"></span>
                </x-alert>
            </template>

            <template x-if="errors._general">
                <x-alert type="error" title="Something went wrong">
                    <span x-text="errors._general?.[0]"></span>
                </x-alert>
            </template>

            <template x-if="errors && !errors._rate && !errors._general && Object.keys(errors).length">
                <x-alert type="error" title="Please fix the errors">
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        <template x-for="(msgs, field) in errors" :key="field">
                            <li><strong x-text="field" class="capitalize"></strong>: <span x-text="msgs.join(', ')"></span></li>
                        </template>
                    </ul>
                </x-alert>
            </template>

            <div x-show="!success && !Object.keys(errors).length" class="rounded-xl border border-dashed border-white/10 p-5 text-sm text-slate-400">
                <p class="font-medium text-slate-300 mb-1">What happens next?</p>
                <ol class="list-decimal list-inside space-y-1 text-xs">
                    <li>The message is sent to OpenAI for analysis.</li>
                    <li>An email notification is sent to the site owner.</li>
                    <li>You receive a confirmation copy.</li>
                    <li>Metrics record the contact (visible at <a href="/metrics" class="text-indigo-300 hover:underline">/metrics</a>).</li>
                </ol>
            </div>
        </div>
    </div>
</div>
