@extends('layouts.app')

@section('title', $plano->exists ? 'Editar plano' : 'Novo plano')

@section('content')
<div class="max-w-lg mx-auto ticket p-8">
    <h1 class="text-xl font-display font-semibold mb-6">{{ $plano->exists ? 'Editar plano' : 'Novo plano' }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-coral-light text-coral rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $plano->exists ? route('admin.plans.update', $plano) : route('admin.plans.store') }}" class="space-y-4">
        @csrf
        @if ($plano->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Nome do plano</label>
            <input type="text" name="name" value="{{ old('name', $plano->name) }}" required
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Descrição</label>
            <textarea name="description" rows="2" class="w-full border border-ink/15 rounded-lg px-3 py-2">{{ old('description', $plano->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tipo de plano</label>
            <select name="plan_type" id="plan_type" class="w-full border border-ink/15 rounded-lg px-3 py-2"
                    onchange="document.getElementById('campo-moedas').classList.toggle('hidden', this.value !== 'coins')">
                <option value="coins" @selected(old('plan_type', $plano->plan_type ?? 'coins') === 'coins')>Moedas (1 sorteio = 1 moeda)</option>
                <option value="unlimited" @selected(old('plan_type', $plano->plan_type ?? 'coins') === 'unlimited')>Uso ilimitado</option>
            </select>
        </div>

        <div id="campo-moedas" class="{{ old('plan_type', $plano->plan_type ?? 'coins') === 'unlimited' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium mb-1">Quantidade de moedas no pacote</label>
            <input type="number" min="1" name="coins_amount" value="{{ old('coins_amount', $plano->coins_amount) }}"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Preço (R$)</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plano->price) }}" required
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Período</label>
                <select name="period" class="w-full border border-ink/15 rounded-lg px-3 py-2">
                    @foreach (['unico' => 'Pagamento único', 'mensal' => 'Mensal (30 dias)', 'anual' => 'Anual (365 dias)'] as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('period', $plano->period ?? 'unico') === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-ink/40 mt-1">Só importa para planos de uso ilimitado — define a duração do acesso.</p>
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plano->is_featured) ? 'checked' : '' }}>
                Destacar como "mais popular"
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="active" value="1" {{ old('active', $plano->exists ? $plano->active : true) ? 'checked' : '' }}>
                Ativo (visível na página pública)
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Ordem de exibição</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $plano->sort_order ?? 0) }}"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
        </div>

        <button type="submit" class="w-full btn-primary justify-center flex">Salvar plano</button>
    </form>
</div>
@endsection
