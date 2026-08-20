@extends('layouts.app')

@section('title', 'Esqueci minha senha')

@section('content')
<div class="max-w-md mx-auto ticket p-8">
    <h1 class="text-xl font-display font-semibold mb-2">Esqueci minha senha</h1>
    <p class="text-sm text-ink/60 mb-6">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <button type="submit" class="w-full btn-primary justify-center flex">Enviar link</button>
    </form>

    <p class="text-sm text-ink/50 mt-4">
        <a href="{{ route('login') }}" class="text-teal font-medium">&larr; Voltar para o login</a>
    </p>
</div>
@endsection
