@extends('layouts.app')

@section('title', 'Comprar '.$plan->name)

@section('content')
<div class="max-w-md mx-auto ticket p-8 text-center">
    <span class="badge-gold mb-4 inline-block">{{ $plan->isCoins() ? 'PACOTE DE MOEDAS' : 'ASSINATURA' }}</span>
    <h1 class="text-xl font-display font-semibold mb-2">{{ $plan->name }}</h1>
    <p class="text-sm text-ink/60 mb-6">{{ $plan->limiteDescricao() }}</p>

    <p class="text-2xl font-display font-semibold mb-6">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>

    @if ($payment->qr_code_base64)
        <img src="data:image/png;base64,{{ $payment->qr_code_base64 }}" alt="QR Code Pix" class="mx-auto mb-4 w-48 h-48 rounded-lg border border-ink/10">
    @endif

    @if ($payment->qr_code)
        <div class="bg-ink/5 p-3 rounded-lg text-xs font-mono break-all mb-4">{{ $payment->qr_code }}</div>
        <button onclick="navigator.clipboard.writeText('{{ $payment->qr_code }}')"
                class="text-teal text-sm font-medium">Copiar código Pix</button>
    @endif

    <p class="text-xs text-ink/40 mt-6">
        @if ($plan->isCoins())
            Assim que o pagamento for aprovado, as moedas caem automaticamente no seu saldo.
        @else
            Assim que o pagamento for aprovado, seu acesso ilimitado é ativado automaticamente.
        @endif
        Pode voltar aqui em alguns segundos pra conferir.
    </p>

    <a href="{{ route('dashboard') }}" class="text-sm text-ink/50 mt-4 inline-block">&larr; Voltar ao painel</a>
</div>
@endsection
