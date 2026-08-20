<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SorteioSaaS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="border-b border-ink/10 bg-card px-6 py-4 flex justify-between items-center">
        <a href="{{ route('home') }}" class="font-display font-semibold text-lg tracking-tight">
            Sorteio<span class="text-teal">SaaS</span>
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
        SorteioSaaS — sorteios de Instagram com auditoria pública.
    </footer>
</body>
</html>
