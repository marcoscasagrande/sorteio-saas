@extends('layouts.app')

@php
    $siteName = \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, 'SorteioSaaS');
    $tagline = \App\Models\Setting::get(\App\Models\Setting::SITE_TAGLINE, 'Sorteios no Instagram com transparência');
@endphp

@section('title', $siteName.' — Sorteie comentários do Instagram com prova pública')
@section('seo_title', $siteName.' — Sorteie comentários do Instagram com prova pública')
@section('seo_description', 'Conecte sua conta do Instagram, cole o link do post e sorteie entre os comentários em segundos. Cada resultado sai com hash de auditoria pública — sua audiência confirma sozinha que foi justo. Grátis até 100 participantes.')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center py-10">
    <div>
        <span class="badge-gold mb-4 inline-block">SORTEIO Nº 000-482-19</span>
        <h1 class="text-4xl md:text-5xl font-display font-semibold leading-tight mb-5">
            Seu sorteio,<br>com prova que<br>ninguém contesta.
        </h1>
        <p class="text-ink/60 mb-8 leading-relaxed">
            Cole o link do post, puxe todos os comentários automaticamente e sorteie
            em segundos. O resultado sai com hash público de auditoria — qualquer
            seguidor pode conferir que ninguém manipulou o sorteio.
            Grátis até 100 participantes; acima disso, libere via Pix por R$ 9,99.
        </p>
        <div class="flex gap-3">
            <a href="{{ route('register') }}" class="btn-primary">Criar meu primeiro sorteio</a>
            <a href="{{ route('login') }}" class="btn-secondary">Já tenho conta</a>
        </div>
        <p class="text-xs text-ink/40 mt-4">Sem cartão de crédito. Leva menos de 2 minutos.</p>
    </div>

    <div class="ticket p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-xs text-ink/40 uppercase tracking-wide mb-1">Resultado do sorteio</p>
                <p class="font-display text-xl font-semibold">@usuaria.exemplo</p>
            </div>
            <span class="badge-teal">verificado</span>
        </div>
        <div class="space-y-2 text-sm text-ink/60 mb-6">
            <div class="flex justify-between"><span>Post</span><span>instagram.com/p/DQx4f2</span></div>
            <div class="flex justify-between"><span>Comentários</span><span>2.481</span></div>
            <div class="flex justify-between"><span>Sorteado em</span><span>19/08/2026 14:32</span></div>
        </div>
        <div class="pt-4 border-t border-dashed border-ink/15">
            <p class="text-xs text-ink/40 mb-1">Hash de auditoria (SHA-256)</p>
            <p class="font-mono text-xs text-ink/70 break-all">a13f9c2b7e...4d091e082b4</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
    <div class="bg-card p-6 rounded-xl border border-ink/10">
        <span class="font-mono text-xs text-teal">01</span>
        <h3 class="font-display font-semibold mt-2 mb-2">Conecte o Instagram</h3>
        <p class="text-sm text-ink/60">Autorize sua conta Business ou Creator com o login oficial da Meta — sem compartilhar senha.</p>
    </div>
    <div class="bg-card p-6 rounded-xl border border-ink/10">
        <span class="font-mono text-xs text-teal">02</span>
        <h3 class="font-display font-semibold mt-2 mb-2">Cole o link do post</h3>
        <p class="text-sm text-ink/60">Buscamos automaticamente todos os comentários elegíveis, sem contagem manual.</p>
    </div>
    <div class="bg-card p-6 rounded-xl border border-ink/10">
        <span class="font-mono text-xs text-teal">03</span>
        <h3 class="font-display font-semibold mt-2 mb-2">Sorteie e comprove</h3>
        <p class="text-sm text-ink/60">Resultado com hash público de auditoria — sua audiência confirma sozinha que foi justo.</p>
    </div>
</div>

<div class="mt-16 ticket p-8 text-center">
    <h2 class="text-2xl font-display font-semibold mb-2">Pronto para o próximo sorteio?</h2>
    <p class="text-ink/60 mb-6">Crie sua conta e conecte o Instagram — o primeiro sorteio é grátis.</p>
    <a href="{{ route('register') }}" class="btn-primary inline-block">Começar agora</a>
</div>
@endsection
