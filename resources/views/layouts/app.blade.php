@php
    $siteName = \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, 'SorteioSaaS');
    $logoPath = \App\Models\Setting::get(\App\Models\Setting::SITE_LOGO_PATH);
    $seoTitle = trim(($__env->yieldContent('seo_title')) ?: \App\Models\Setting::get(\App\Models\Setting::SEO_TITLE, $siteName));
    $seoDescription = trim(($__env->yieldContent('seo_description')) ?: \App\Models\Setting::get(\App\Models\Setting::SEO_DESCRIPTION, ''));
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $seoTitle)</title>

    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / redes sociais --}}
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($logoPath)
        <meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    @stack('head')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="border-b border-ink/10 bg-card px-6 py-4 flex justify-between items-center">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-semibold text-lg tracking-tight">
            @if ($logoPath)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-8 w-auto">
            @else
                {{ $siteName }}
            @endif
        </a>
        <div class="flex gap-5 items-center text-sm">
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-ink/60 hover:text-ink">Admin</a>
                @else
                    <a href="{{ route('dashboard') }}" class="text-ink/60 hover:text-ink">Meu painel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-ink/60 hover:text-coral">Sair</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-ink/60 hover:text-ink">Entrar</a>
                <a href="{{ route('register') }}" class="btn-primary">Criar conta</a>
            @endauth
        </div>
    </nav>

    @auth
        @if (auth()->user()->isAdmin() && request()->routeIs('admin.*'))
            <div class="bg-ink text-white/70 px-6 py-2 text-sm flex gap-6">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white {{ request()->routeIs('admin.dashboard') ? 'text-white font-medium' : '' }}">Painel</a>
                <a href="{{ route('admin.users.index') }}" class="hover:text-white {{ request()->routeIs('admin.users.*') ? 'text-white font-medium' : '' }}">Usuários</a>
                <a href="{{ route('admin.reports.sales') }}" class="hover:text-white {{ request()->routeIs('admin.reports.*') ? 'text-white font-medium' : '' }}">Vendas</a>
                <a href="{{ route('admin.settings.edit') }}" class="hover:text-white {{ request()->routeIs('admin.settings.*') ? 'text-white font-medium' : '' }}">Configurações</a>
            </div>
        @endif
    @endauth

    <main class="max-w-5xl mx-auto px-6 py-10">
        @if (session('sucesso'))
            <div class="mb-4 p-3 bg-teal-light text-teal-dark rounded-lg text-sm">{{ session('sucesso') }}</div>
        @endif
        @if (session('info'))
            <div class="mb-4 p-3 bg-gold-light text-gold-dark rounded-lg text-sm">{{ session('info') }}</div>
        @endif
        @if (session('erro'))
            <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">{{ session('erro') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="max-w-5xl mx-auto px-6 py-10 text-xs text-ink/40 border-t border-ink/10 mt-16">
        {{ $siteName }} — sorteios de Instagram com auditoria pública.
    </footer>

    @stack('scripts')
</body>
</html>
