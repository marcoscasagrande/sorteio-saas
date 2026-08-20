@extends('layouts.app')

@section('title', 'Comprovação de sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))
@section('seo_title', 'Comprovação pública de sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))
@section('seo_description', 'Confira a prova de auditoria deste sorteio: vencedor, comentário sorteado e hash público SHA-256.')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <span class="badge-teal mb-4 inline-block">✓ Sorteio verificado</span>

    <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-xl font-display font-semibold mb-6">Comprovação pública</h1>

    <div class="p-5 bg-teal-light border border-teal/20 rounded-lg text-center mb-6">
        <p class="text-xs text-teal-dark/70 uppercase tracking-wide">Vencedor</p>
        <p class="text-xl font-display font-semibold text-teal-dark mt-1">{{ $giveaway->winner_username }}</p>
        @if ($giveaway->winner_comment)
            <p class="text-sm text-teal-dark/80 mt-2 italic">&ldquo;{{ $giveaway->winner_comment }}&rdquo;</p>
        @endif
    </div>

    <div class="space-y-2 text-sm text-ink/60 mb-6">
        <div class="flex justify-between"><span>Post sorteado</span><span class="break-all text-right">{{ $giveaway->instagram_post_url }}</span></div>
        <div class="flex justify-between"><span>Comentários</span><span>{{ $giveaway->comments_count }}</span></div>
        <div class="flex justify-between"><span>Sorteado em</span><span>{{ $giveaway->drawn_at?->format('d/m/Y \à\s H:i') }}</span></div>
    </div>

    <div class="pt-4 border-t border-dashed border-ink/15">
        <p class="text-xs text-ink/40 mb-1">Hash de auditoria (SHA-256)</p>
        <p class="font-mono text-xs text-ink/70 break-all">{{ $giveaway->result_hash }}</p>
        <p class="text-xs text-ink/40 mt-2">
            Este hash é uma assinatura digital única do resultado. Ninguém — nem o organizador — pode alterar o vencedor depois do sorteio sem que o hash mude.
        </p>
    </div>
</div>
@endsection
