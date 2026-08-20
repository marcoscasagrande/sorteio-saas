@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
<div class="max-w-md mx-auto ticket p-8">
    <h1 class="text-xl font-display font-semibold mb-6">Criar conta</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Senha</label>
            <input type="password" name="password" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirmar senha</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <button type="submit" class="w-full btn-primary justify-center flex">
            Criar conta
        </button>
    </form>

    <p class="text-sm text-ink/50 mt-4">
        Já tem conta? <a href="{{ route('login') }}" class="text-teal font-medium">Entrar</a>
    </p>
</div>
@endsection
