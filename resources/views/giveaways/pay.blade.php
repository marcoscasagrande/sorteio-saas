@extends('layouts.app')

@section('title', 'Pagar Pix')

@section('content')
<div class="max-w-md mx-auto ticket p-8 text-center">
    <span class="badge-gold mb-4 inline-block">LIBERAÇÃO PENDENTE</span>
    <h1 class="text-xl font-display font-semibold mb-2">Libere seu sorteio</h1>
    <p class="text-sm text-ink/60 mb-6">
        Este post tem {{ $giveaway->comments_count }} comentários. Pague R$ {{ number_format($payment->amount, 2, ',', '.') }}
        via Pix para liberar o sorteio.
    </p>

    @if ($payment->qr_code_base64)
        <img src="data:image/png;base64,{{ $payment->qr_code_base64 }}" alt="QR Code Pix" class="mx-auto mb-4 w-48 h-48 rounded-lg border border-ink/10">
    @endif

    @if ($payment->qr_code)
        <div class="bg-ink/5 p-3 rounded-lg text-xs font-mono break-all mb-4">{{ $payment->qr_code }}</div>
        <button onclick="navigator.clipboard.writeText('{{ $payment->qr_code }}')"
                class="text-teal text-sm font-medium">Copiar código Pix</button>
    @endif

    <p class="text-xs text-ink/40 mt-6">
        Assim que o pagamento for aprovado, o sorteio é liberado automaticamente — pode voltar aqui em alguns segundos.
    </p>
</div>
@endsection
