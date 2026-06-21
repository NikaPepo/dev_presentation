<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('developer.name')) — {{ config('developer.title') }}</title>
    <meta name="description" content="@yield('description', config('developer.tagline'))">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @fonts

    <style>[x-cloak] { display: none !important; }</style>

    @stack('head')
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased font-sans">
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="absolute top-1/3 -left-40 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 h-96 w-96 rounded-full bg-purple-600/10 blur-3xl"></div>
    </div>

    @include('components.nav')

    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-12">
        @yield('content')
    </main>

    @include('components.footer')

    @stack('scripts')
</body>
</html>
