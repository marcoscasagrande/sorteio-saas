@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
<div class="max-w-md mx-auto ticket p-8">
    <h1 class="text-xl font-display font-semibold mb-6">Entrar</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Senha</label>
            <input type="password" name="password" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink/60">
            <input type="checkbox" name="remember"> Lembrar de mim
        </label>
        <button type="submit" class="w-full btn-primary justify-center flex">
            Entrar
        </button>
    </form>

    <p class="text-sm text-ink/50 mt-4">
        <a href="{{ route('password.request') }}" class="text-teal font-medium">Esqueceu a senha?</a>
    </p>
    <p class="text-sm text-ink/50 mt-1">
        Não tem conta? <a href="{{ route('register') }}" class="text-teal font-medium">Criar agora</a>
    </p>
</div>
@endsection
