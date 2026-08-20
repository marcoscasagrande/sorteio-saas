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

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Preço (R$)</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plano->price) }}" required
                       class="w-full border border-ink/15 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Período</label>
                <select name="period" class="w-full border border-ink/15 rounded-lg px-3 py-2">
                    @foreach (['unico' => 'Pagamento único', 'mensal' => 'Mensal', 'anual' => 'Anual'] as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('period', $plano->period) === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Limite de sorteios por período</label>
            <input type="number" min="1" name="giveaways_per_period" value="{{ old('giveaways_per_period', $plano->giveaways_per_period) }}"
                   placeholder="Deixe em branco para ilimitado"
                   class="w-full border border-ink/15 rounded-lg px-3 py-2">
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
