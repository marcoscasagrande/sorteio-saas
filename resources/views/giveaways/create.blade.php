@extends('layouts.app')

@php
    $limiteGratis = \App\Models\Setting::get(\App\Models\Setting::FREE_COMMENT_LIMIT, '100');
    $precoAvulso = \App\Models\Setting::get(\App\Models\Setting::PRICE_PER_GIVEAWAY, '9.99');
@endphp

@section('title', 'Novo sorteio')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <span class="badge-gold mb-4 inline-block">NOVO BILHETE</span>
    <h1 class="text-xl font-display font-semibold mb-6">Criar sorteio</h1>

    <form method="POST" action="{{ route('giveaways.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Link do post do Instagram</label>
            <input type="url" name="instagram_post_url" placeholder="https://instagram.com/p/..."
                   required value="{{ old('instagram_post_url') }}"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal/40">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Quantos vencedores?</label>
            <input type="number" name="winners_count" min="1" max="20" value="{{ old('winners_count', 1) }}"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>

        <fieldset class="border border-ink/10 rounded-lg p-4">
            <legend class="text-sm font-medium px-1">Filtros de participação (opcional)</legend>

            <div class="space-y-3 mt-2">
                <div>
                    <label class="block text-xs text-ink/50 mb-1">Exigir menção a quantos amigos?</label>
                    <input type="number" name="require_mention_count" min="0" max="10" value="{{ old('require_mention_count', 0) }}"
                           class="w-full border border-ink/15 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-ink/50 mb-1">Exigir hashtag no comentário</label>
                    <input type="text" name="require_hashtag" placeholder="#meuhashtag" value="{{ old('require_hashtag') }}"
                           class="w-full border border-ink/15 rounded-lg px-3 py-2 text-sm">
                </div>
                <label class="flex items-center gap-2 text-sm text-ink/70">
                    <input type="checkbox" name="require_follow" value="1" {{ old('require_follow') ? 'checked' : '' }}>
                    Exigir que o participante siga a conta
                </label>
            </div>
        </fieldset>

        <p class="text-xs text-ink/50">
            Grátis até {{ $limiteGratis }} comentários. Acima disso, você paga
            R$ {{ number_format((float) $precoAvulso, 2, ',', '.') }} via Pix para liberar.
        </p>
        <button type="submit" class="w-full btn-primary justify-center flex">
            Buscar comentários
        </button>
    </form>
</div>
@endsection
