@extends('layouts.app')

@section('title', 'Configurações')

@section('content')
<h1 class="text-2xl font-display font-semibold mb-6">Configurações</h1>

@if ($errors->any())
    <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
    @csrf

    <section class="bg-card rounded-xl border border-ink/10 p-6">
        <h2 class="font-display font-semibold mb-1">Identidade do site</h2>
        <p class="text-sm text-ink/50 mb-5">Nome, chamada e logo exibidos no menu e nas telas públicas.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nome do site</label>
                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Chamada (tagline)</label>
                <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}"
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Logo</label>
            @if ($settings['site_logo_path'])
                <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['site_logo_path']) }}" alt="Logo atual" class="h-10 mb-2">
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-ink/40 mt-1">PNG ou SVG com fundo transparente, até 1MB.</p>
        </div>
    </section>

    <section class="bg-card rounded-xl border border-ink/10 p-6">
        <h2 class="font-display font-semibold mb-1">SEO</h2>
        <p class="text-sm text-ink/50 mb-5">Título e descrição padrão usados nos motores de busca e ao compartilhar o link.</p>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Título SEO</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $settings['seo_title']) }}" required maxlength="160"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descrição SEO</label>
            <textarea name="seo_description" rows="3" required maxlength="300"
                      class="w-full border border-ink/15 rounded-lg px-3 py-2">{{ old('seo_description', $settings['seo_description']) }}</textarea>
        </div>
    </section>

    <section class="bg-card rounded-xl border border-ink/10 p-6">
        <h2 class="font-display font-semibold mb-1">Cobrança do sorteio avulso</h2>
        <p class="text-sm text-ink/50 mb-5">
            Valores usados quando o organizador não tem um plano — cobrança avulsa por Pix.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Limite grátis (comentários)</label>
                <input type="number" name="free_comment_limit" min="1" value="{{ old('free_comment_limit', $settings['free_comment_limit']) }}" required
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
                <p class="text-xs text-ink/40 mt-1">Acima deste número, o sorteio precisa ser pago para ser liberado.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Valor cobrado (R$)</label>
                <input type="number" step="0.01" min="0" name="price_per_giveaway" value="{{ old('price_per_giveaway', $settings['price_per_giveaway']) }}" required
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
            </div>
        </div>
    </section>

    <section class="bg-card rounded-xl border border-ink/10 p-6">
        <h2 class="font-display font-semibold mb-1">Mercado Pago</h2>
        <p class="text-sm text-ink/50 mb-5">
            Chaves de produção da sua conta Mercado Pago, usadas para gerar as cobranças Pix.
            Encontre em <span class="font-mono text-xs">Suas integrações &rarr; Credenciais de produção</span>.
        </p>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Access Token</label>
            <input type="password" name="mercadopago_access_token" value="{{ old('mercadopago_access_token', $settings['mercadopago_access_token']) }}"
                   placeholder="APP_USR-..." class="w-full border border-ink/15 rounded-lg px-3 py-2 font-mono text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Public Key</label>
            <input type="text" name="mercadopago_public_key" value="{{ old('mercadopago_public_key', $settings['mercadopago_public_key']) }}"
                   placeholder="APP_USR-..." class="w-full border border-ink/15 rounded-lg px-3 py-2 font-mono text-sm">
        </div>
    </section>

    <button type="submit" class="btn-primary">Salvar configurações</button>
</form>
@endsection
