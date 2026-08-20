@extends('layouts.app')

@section('title', 'Planos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-display font-semibold">Planos</h1>
    <a href="{{ route('admin.plans.create') }}" class="btn-primary">+ Novo plano</a>
</div>

<div class="bg-card rounded-xl border border-ink/10 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 text-left text-ink/50 font-mono text-xs uppercase">
            <tr>
                <th class="p-3">Nome</th>
                <th class="p-3">Tipo</th>
                <th class="p-3">Preço</th>
                <th class="p-3">Detalhe</th>
                <th class="p-3">Status</th>
                <th class="p-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink/10">
            @forelse ($planos as $plano)
                <tr>
                    <td class="p-3 font-medium">{{ $plano->name }} @if($plano->is_featured)<span class="badge-gold ml-1">destaque</span>@endif</td>
                    <td class="p-3 text-ink/50">{{ $plano->isCoins() ? 'Moedas' : 'Ilimitado' }}</td>
                    <td class="p-3">R$ {{ number_format($plano->price, 2, ',', '.') }}</td>
                    <td class="p-3 text-ink/50">{{ $plano->limiteDescricao() }}</td>
                    <td class="p-3">
                        <span @class(['badge-teal' => $plano->active, 'bg-ink/5 text-ink/40 text-xs font-mono px-2 py-1 rounded' => !$plano->active])>
                            {{ $plano->active ? 'ativo' : 'inativo' }}
                        </span>
                    </td>
                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.plans.edit', $plano) }}" class="text-teal font-medium">Editar</a>
                        <form method="POST" action="{{ route('admin.plans.destroy', $plano) }}" class="inline"
                              onsubmit="return confirm('Remover este plano?');">
                            @csrf @method('DELETE')
                            <button class="text-coral font-medium">Remover</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-ink/40">Nenhum plano cadastrado ainda.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
