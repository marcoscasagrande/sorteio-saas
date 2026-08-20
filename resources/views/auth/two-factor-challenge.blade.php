@extends('layouts.app')

@section('title', 'Verificação em duas etapas')

@section('content')
<div class="max-w-md mx-auto ticket p-8">
    <span class="badge-gold mb-4 inline-block">SEGURANÇA</span>
    <h1 class="text-xl font-display font-semibold mb-2">Verificação em duas etapas</h1>
    <p class="text-sm text-ink/60 mb-6">Enviamos um código de 6 dígitos para o seu e-mail. Ele expira em 10 minutos.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Código</label>
            <input type="text" name="code" inputmode="numeric" maxlength="6" autofocus required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 text-center text-2xl font-mono tracking-widest">
        </div>
        <button type="submit" class="w-full btn-primary justify-center flex">Confirmar</button>
    </form>
</div>
@endsection
