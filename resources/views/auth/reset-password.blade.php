@extends('layouts.app')

@section('title', 'Redefinir senha')

@section('content')
<div class="max-w-md mx-auto ticket p-8">
    <h1 class="text-xl font-display font-semibold mb-6">Redefinir senha</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-medium mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nova senha</label>
            <input type="password" name="password" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirmar nova senha</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="w-full btn-primary justify-center flex">Redefinir senha</button>
    </form>
</div>
@endsection
