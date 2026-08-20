@extends('layouts.app')

@section('title', 'Comprovação de sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))
@section('seo_title', 'Comprovação pública de sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))
@section('seo_description', 'Confira a prova de auditoria deste sorteio: vencedor, comentário sorteado e hash público SHA-256.')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <span class="badge-teal mb-4 inline-block">✓ Sorteio verificado</span>

    <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-xl font-display font-semibold mb-6">Comprovação pública</h1>

    <div class="p-5 bg-teal-light border border-teal/20 rounded-lg mb-6">
        <p class="text-xs text-teal-dark/70 uppercase tracking-wide text-center">
            {{ count($giveaway->winners ?? []) > 1 ? 'Vencedores' : 'Vencedor' }}
        </p>
        <div class="mt-2 space-y-3">
            @forelse (($giveaway->winners ?: [['username' => $giveaway->winner_username, 'text' => $giveaway->winner_comment]]) as $vencedor)
                <div class="text-center">
                    <p class="text-lg font-display font-semibold text-teal-dark">{{ $vencedor['username'] }}</p>
                    @if (!empty($vencedor['text']))
                        <p class="text-sm text-teal-dark/80 italic">&ldquo;{{ $vencedor['text'] }}&rdquo;</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <div class="space-y-2 text-sm text-ink/60 mb-6">
        <div class="flex justify-between"><span>Post sorteado</span><span class="break-all text-right">{{ $giveaway->instagram_post_url }}</span></div>
        <div class="flex justify-between"><span>Comentários</span><span>{{ $giveaway->comments_count }}</span></div>
        <div class="flex justify-between"><span>Sorteado em</span><span>{{ $giveaway->drawn_at?->format('d/m/Y \à\s H:i') }}</span></div>
        @if (!empty($giveaway->redraw_history))
            <div class="flex justify-between"><span>Re-sorteios anteriores</span><span>{{ count($giveaway->redraw_history) }}</span></div>
        @endif
    </div>

    <div class="pt-4 border-t border-dashed border-ink/15">
        <p class="text-xs text-ink/40 mb-1">Hash de auditoria (SHA-256)</p>
        <p class="font-mono text-xs text-ink/70 break-all">{{ $giveaway->result_hash }}</p>
        <p class="text-xs text-ink/40 mt-2">
            Este hash é uma assinatura digital única do resultado. Ninguém — nem o organizador — pode alterar o vencedor depois do sorteio sem que o hash mude.
        </p>
    </div>

    @if (!empty($giveaway->redraw_history))
        <details class="mt-6 text-sm">
            <summary class="cursor-pointer text-ink/50">Ver resultados anteriores deste sorteio</summary>
            <div class="mt-2 space-y-3">
                @foreach (array_reverse($giveaway->redraw_history) as $anterior)
                    <div class="bg-ink/5 rounded-lg p-3 text-xs">
                        @foreach (($anterior['winners'] ?? []) as $v)
                            <p><span class="font-medium">{{ $v['username'] }}</span> — {{ $v['text'] ?? '' }}</p>
                        @endforeach
                        <p class="font-mono text-ink/40 mt-1 break-all">hash: {{ $anterior['result_hash'] }}</p>
                    </div>
                @endforeach
            </div>
        </details>
    @endif
</div>
@endsection
