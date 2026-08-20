@extends('layouts.app')

@section('title', 'Sorteio #'.str_pad($giveaway->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-lg mx-auto ticket p-8" id="ticket-sorteio">
    <p class="font-mono text-xs text-ink/40 mb-1">SORTEIO #{{ str_pad($giveaway->id, 6, '0', STR_PAD_LEFT) }}</p>

    @if ($giveaway->status === 'fetching_comments')
        {{-- Ainda buscando na fila — recarrega sozinho a cada 4s até mudar de status --}}
        <meta http-equiv="refresh" content="4">
        <h1 class="text-xl font-display font-semibold mb-2">Buscando comentários…</h1>
        <p class="text-sm text-ink/50 mb-6 break-all">{{ $giveaway->instagram_post_url }}</p>
        <div class="flex items-center gap-3 text-sm text-ink/60">
            <span class="inline-block w-2 h-2 rounded-full bg-teal animate-pulse"></span>
            Isso pode levar alguns segundos em posts com muitos comentários.
        </div>
        <p class="text-xs text-ink/30 mt-4">Esta página atualiza sozinha.</p>
    @else
        <h1 class="text-xl font-display font-semibold mb-2">
            @if ($giveaway->status === 'completed')
                Resultado
            @else
                Pronto para sortear
            @endif
        </h1>
        <p class="text-sm text-ink/50 mb-6 break-all">{{ $giveaway->instagram_post_url }}</p>

        <p class="text-sm mb-2">
            <span class="font-medium">{{ $giveaway->comments_count }}</span> comentários encontrados neste post.
            @if ($giveaway->require_mention_count || $giveaway->require_hashtag || $giveaway->require_follow)
                <span class="text-ink/40">({{ count($giveaway->comentariosElegiveis()) }} elegíveis pelos filtros)</span>
            @endif
        </p>

        @if ($giveaway->status === 'ready' && count($giveaway->comentariosElegiveis()) > 0)
            <details class="mb-6 text-sm">
                <summary class="cursor-pointer text-teal font-medium">Ver comentários elegíveis</summary>
                <div class="mt-2 max-h-48 overflow-y-auto space-y-1 bg-ink/5 rounded-lg p-3">
                    @foreach (array_slice($giveaway->comentariosElegiveis(), 0, 50) as $c)
                        <p class="text-xs text-ink/60"><span class="font-medium">{{ $c['username'] }}</span> — {{ $c['text'] }}</p>
                    @endforeach
                </div>
            </details>
        @endif

        @if ($giveaway->status === 'completed')
            <div id="card-vencedor" class="p-5 bg-teal-light border border-teal/20 rounded-lg @if (session('just_drawn')) opacity-0 @endif" style="transition: opacity .4s ease, transform .4s ease;">
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
                <p class="font-mono text-xs text-teal-dark/60 mt-4 break-all text-center">hash: {{ $giveaway->result_hash }}</p>
            </div>

            <div class="mt-4 flex gap-2">
                <input type="text" readonly value="{{ $giveaway->verificationUrl() }}"
                       class="flex-1 bg-ink/5 border-0 rounded-lg px-3 py-2 text-xs font-mono text-ink/60"
                       onclick="this.select()">
                <button type="button" onclick="navigator.clipboard.writeText('{{ $giveaway->verificationUrl() }}')"
                        class="btn-secondary text-xs px-3">Copiar link</button>
            </div>
            <p class="text-xs text-ink/40 mt-2 mb-6">
                Compartilhe este link com sua audiência — qualquer pessoa pode conferir o resultado, sem precisar de login.
            </p>

            <details class="mb-4 text-sm">
                <summary class="cursor-pointer text-ink/50">Selo para incorporar no seu site</summary>
                <div class="mt-2">
                    <textarea readonly onclick="this.select()" rows="3"
                              class="w-full bg-ink/5 rounded-lg p-2 text-xs font-mono text-ink/60">&lt;a href="{{ $giveaway->verificationUrl() }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;background:#E4F1EC;color:#164F41;font-family:sans-serif;font-size:12px;text-decoration:none;"&gt;✓ Sorteio verificado&lt;/a&gt;</textarea>
                </div>
            </details>

            @if (!empty($giveaway->redraw_history))
                <p class="text-xs text-ink/40 mb-4">Este sorteio já foi refeito {{ count($giveaway->redraw_history) }}x. O resultado anterior fica registrado na comprovação.</p>
            @endif

            <form method="POST" action="{{ route('giveaways.redraw', $giveaway) }}"
                  onsubmit="return confirm('Sortear novamente substitui o resultado atual (o anterior fica salvo no histórico). Confirmar?');">
                @csrf
                <button class="w-full btn-secondary justify-center flex">🔁 Sortear novamente</button>
            </form>
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
