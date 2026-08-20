@extends('layouts.app')

@section('title', 'Sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-lg mx-auto ticket p-8" id="ticket-sorteio">
    <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-xl font-display font-semibold mb-2">
        @if ($giveaway->status === 'completed')
            Resultado
        @else
            Pronto para sortear
        @endif
    </h1>
    <p class="text-sm text-ink/50 mb-6 break-all">{{ $giveaway->instagram_post_url }}</p>

    <p class="text-sm mb-6">
        <span class="font-medium">{{ $giveaway->comments_count }}</span> comentários encontrados neste post.
    </p>

    @if ($giveaway->status === 'completed')
        <div id="card-vencedor" class="p-5 bg-teal-light border border-teal/20 rounded-lg text-center @if (session('just_drawn')) opacity-0 @endif" style="transition: opacity .4s ease, transform .4s ease;">
            <p class="text-xs text-teal-dark/70 uppercase tracking-wide">Vencedor</p>
            <p class="text-xl font-display font-semibold text-teal-dark mt-1">{{ $giveaway->winner_username }}</p>
            @if ($giveaway->winner_comment)
                <p class="text-sm text-teal-dark/80 mt-2 italic">&ldquo;{{ $giveaway->winner_comment }}&rdquo;</p>
            @endif
            <p class="font-mono text-xs text-teal-dark/60 mt-4 break-all">hash: {{ $giveaway->result_hash }}</p>
        </div>

        <div class="mt-4 flex gap-2">
            <input type="text" readonly value="{{ $giveaway->verificationUrl() }}"
                   class="flex-1 bg-ink/5 border-0 rounded-lg px-3 py-2 text-xs font-mono text-ink/60"
                   onclick="this.select()">
            <button type="button" onclick="navigator.clipboard.writeText('{{ $giveaway->verificationUrl() }}')"
                    class="btn-secondary text-xs px-3">Copiar link</button>
        </div>
        <p class="text-xs text-ink/40 mt-2">
            Compartilhe este link com sua audiência — qualquer pessoa pode conferir o resultado, sem precisar de login.
        </p>
    @elseif ($giveaway->needsPayment())
        <a href="{{ route('giveaways.pay', $giveaway) }}" class="btn-primary w-full justify-center flex" style="background-color: #C6941F">
            Pagar Pix para liberar
        </a>
    @else
        <form method="POST" action="{{ route('giveaways.draw', $giveaway) }}">
            @csrf
            <button class="w-full btn-primary justify-center flex text-base py-3">
                🎉 Sortear agora
            </button>
        </form>
    @endif
</div>

@if (session('just_drawn'))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.comemorarSorteio();

                const card = document.getElementById('card-vencedor');
                if (card) {
                    requestAnimationFrame(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1.02)';
                        setTimeout(() => { card.style.transform = 'scale(1)'; }, 300);
                    });
                }
            });
        </script>
    @endpush
@endif
@endsection
