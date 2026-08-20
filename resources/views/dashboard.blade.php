@extends('layouts.app')

@section('title', 'Meu painel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-display font-semibold">Meus sorteios</h1>
    <a href="{{ route('giveaways.create') }}" class="btn-primary">+ Novo sorteio</a>
</div>

@unless($temInstagramConectado)
    <div class="mb-6 p-4 bg-gold-light border border-gold/30 rounded-lg flex justify-between items-center">
        <span class="text-sm text-gold-dark">Conecte sua conta do Instagram para começar a sortear.</span>
        <a href="{{ route('instagram.connect') }}" class="badge-gold font-medium">Conectar</a>
    </div>
@endunless

<div class="space-y-3">
    @forelse ($giveaways as $giveaway)
        <a href="{{ route('giveaways.show', $giveaway) }}" class="ticket flex justify-between items-center p-5 hover:shadow-sm transition-shadow">
            <div>
                <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="font-medium">{{ $giveaway->instagram_post_url }}</p>
                <p class="text-sm text-ink/50">{{ $giveaway->comments_count }} comentários</p>
            </div>
            <span @class([
                'text-xs font-mono px-2 py-1 rounded',
                'badge-gold' => $giveaway->status === 'pending_payment',
                'badge-teal' => $giveaway->status === 'ready',
                'bg-ink/5 text-ink/50' => $giveaway->status === 'completed',
            ])>
                {{ ['pending_payment' => 'aguardando pix', 'ready' => 'pronto', 'completed' => 'concluído'][$giveaway->status] }}
            </span>
        </a>
    @empty
        <div class="ticket p-8 text-center text-ink/50">Você ainda não criou nenhum sorteio.</div>
    @endforelse
</div>

<div class="mt-6">{{ $giveaways->links() }}</div>
@endsection
