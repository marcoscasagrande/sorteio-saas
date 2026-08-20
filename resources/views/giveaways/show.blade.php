@extends('layouts.app')

@section('title', 'Sorteio')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-xl font-display font-semibold mb-2">Resultado</h1>
    <p class="text-sm text-ink/50 mb-6">{{ $giveaway->instagram_post_url }}</p>

    <p class="text-sm mb-4">{{ $giveaway->comments_count }} comentários encontrados.</p>

    @if ($giveaway->status === 'completed')
        <div class="p-5 bg-teal-light border border-teal/20 rounded-lg text-center">
            <p class="text-xs text-teal-dark/70 uppercase tracking-wide">Vencedor</p>
            <p class="text-xl font-display font-semibold text-teal-dark mt-1">{{ $giveaway->winner_username }}</p>
            <p class="font-mono text-xs text-teal-dark/60 mt-3 break-all">hash: {{ $giveaway->result_hash }}</p>
        </div>
    @elseif ($giveaway->needsPayment())
        <a href="{{ route('giveaways.pay', $giveaway) }}" class="btn-primary w-full justify-center flex" style="background-color: #C6941F">
            Pagar Pix para liberar
        </a>
    @else
        <form method="POST" action="{{ route('giveaways.draw', $giveaway) }}">
            @csrf
            <button class="w-full btn-primary justify-center flex">Sortear agora</button>
        </form>
    @endif
</div>
@endsection
