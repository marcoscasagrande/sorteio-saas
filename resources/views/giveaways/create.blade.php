@extends('layouts.app')

@section('title', 'Novo sorteio')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <span class="badge-gold mb-4 inline-block">NOVO BILHETE</span>
    <h1 class="text-xl font-display font-semibold mb-6">Criar sorteio</h1>

    <form method="POST" action="{{ route('giveaways.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Link do post do Instagram</label>
            <input type="url" name="instagram_post_url" placeholder="https://instagram.com/p/..."
                   required class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>
        <p class="text-xs text-ink/50">
            Grátis até 100 comentários. Acima disso, você paga R$ 9,99 via Pix para liberar.
        </p>
        <button type="submit" class="w-full btn-primary justify-center flex">
            Buscar comentários
        </button>
    </form>
</div>
@endsection
