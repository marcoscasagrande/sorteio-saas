@extends('layouts.app')

@section('title', 'Admin')

@section('content')
<h1 class="text-2xl font-display font-semibold mb-6">Painel administrativo</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-card p-4 rounded-xl border border-ink/10">
        <p class="text-xs text-ink/40 font-mono">USUÁRIOS</p>
        <p class="text-2xl font-display font-semibold mt-1">{{ $metrics['total_usuarios'] }}</p>
    </div>
    <div class="bg-card p-4 rounded-xl border border-ink/10">
        <p class="text-xs text-ink/40 font-mono">CADASTROS HOJE</p>
        <p class="text-2xl font-display font-semibold mt-1">{{ $metrics['cadastros_hoje'] }}</p>
    </div>
    <div class="bg-card p-4 rounded-xl border border-ink/10">
        <p class="text-xs text-ink/40 font-mono">AGUARDANDO PIX</p>
        <p class="text-2xl font-display font-semibold mt-1">{{ $metrics['sorteios_aguardando_pagamento'] }}</p>
    </div>
    <div class="bg-teal text-white p-4 rounded-xl">
        <p class="text-xs text-white/70 font-mono">FATURAMENTO (MÊS)</p>
        <p class="text-2xl font-display font-semibold mt-1">R$ {{ number_format($metrics['faturamento_mes'], 2, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-card rounded-xl border border-ink/10">
        <div class="p-4 border-b border-ink/10 flex justify-between items-center">
            <h2 class="font-display font-semibold">Últimos cadastros</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-teal font-medium">Ver todos</a>
        </div>
        <div class="divide-y divide-ink/10">
            @foreach ($ultimosUsuarios as $u)
                <div class="p-3 flex justify-between text-sm">
                    <span>{{ $u->name }}</span>
                    <span class="text-ink/40">{{ $u->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-card rounded-xl border border-ink/10">
        <div class="p-4 border-b border-ink/10">
            <h2 class="font-display font-semibold">Últimos pagamentos</h2>
        </div>
        <div class="divide-y divide-ink/10">
            @foreach ($ultimosPagamentos as $p)
                <div class="p-3 flex justify-between text-sm">
                    <span>{{ $p->user->name }}</span>
                    <span @class([
                        'font-mono text-xs px-2 py-1 rounded',
                        'badge-teal' => $p->status === 'approved',
                        'badge-gold' => $p->status === 'pending',
                        'badge-coral' => $p->status === 'rejected',
                    ])>
                        R$ {{ number_format($p->amount, 2, ',', '.') }} · {{ $p->status }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
