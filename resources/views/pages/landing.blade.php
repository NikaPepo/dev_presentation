@extends('layouts.app')

@section('title', config('developer.name'))
@section('description', config('developer.tagline'))

@section('content')
    {{-- Hero --}}
    <section class="relative pt-8 pb-16 sm:pt-16 sm:pb-24">
        <div class="grid lg:grid-cols-5 gap-8 items-center">
            <div class="lg:col-span-3">
                <p class="text-xs uppercase tracking-widest text-indigo-400 font-medium mb-4">
                    {{ config('developer.location') }} · {{ config('developer.title') }}
                </p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight tracking-tight">
                    Hi, I'm <span class="bg-gradient-to-r from-indigo-400 via-cyan-400 to-emerald-400 bg-clip-text text-transparent">{{ config('developer.name') }}</span>.
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-2xl leading-relaxed">
                    {{ config('developer.tagline') }}
                </p>
                <p class="mt-4 text-slate-400 max-w-2xl leading-relaxed">
                    {{ config('developer.bio') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#contact"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white font-medium transition shadow-lg shadow-indigo-500/20">
                        Get in touch
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="/api-demo"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium transition">
                        Live API demo
                    </a>
                    @if(config('developer.resume_url'))
                        <a href="{{ config('developer.resume_url') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium transition">
                            Résumé
                        </a>
                    @endif
                </div>
            </div>

            {{-- Live API status card --}}
            <div class="lg:col-span-2">
                @include('partials.api-status', ['health' => $health])
            </div>
        </div>
    </section>

    {{-- About + Skills --}}
    <section class="py-16 border-t border-white/5">
        @include('partials.skills')
    </section>

    {{-- AI Demo --}}
    <section class="py-16 border-t border-white/5">
        @include('partials.ai-demo')
    </section>

    {{-- Contact --}}
    <section id="contact" class="py-16 border-t border-white/5">
        @include('partials.contact-form')
    </section>

    {{-- Projects --}}
    @if(!empty($developer['projects']))
        <section class="py-16 border-t border-white/5">
            <x-section-heading eyebrow="Things I've built" title="Projects" description="A few things worth showing off." />
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($developer['projects'] as $project)
                    <a href="{{ $project['url'] }}" class="block p-5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition group">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-white group-hover:text-indigo-300 transition">{{ $project['name'] }}</h3>
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-300 transition flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </div>
                        <p class="mt-2 text-sm text-slate-400 leading-relaxed">{{ $project['description'] }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($project['tags'] ?? [] as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
