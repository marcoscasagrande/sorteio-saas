@extends('layouts.app')

@section('title', 'Relatório de vendas')

@section('content')
<h1 class="text-2xl font-display font-semibold mb-6">Relatório de vendas</h1>

<form method="GET" class="flex flex-wrap gap-3 items-end mb-6">
    <div>
        <label class="block text-xs text-ink/50 mb-1">De</label>
        <input type="date" name="de" value="{{ $de }}" class="border border-ink/15 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-ink/50 mb-1">Até</label>
        <input type="date" name="ate" value="{{ $ate }}" class="border border-ink/15 rounded-lg px-3 py-2 text-sm">
    </div>
    <button type="submit" class="btn-secondary">Filtrar</button>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-teal text-white p-4 rounded-xl">
        <p class="text-xs text-white/70 font-mono">FATURAMENTO NO PERÍODO</p>
        <p class="text-2xl font-display font-semibold mt-1">R$ {{ number_format($resumo['faturamento'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-card p-4 rounded-xl border border-ink/10">
        <p class="text-xs text-ink/40 font-mono">PAGAMENTOS APROVADOS</p>
        <p class="text-2xl font-display font-semibold mt-1">{{ $resumo['quantidade'] }}</p>
    </div>
    <div class="bg-card p-4 rounded-xl border border-ink/10">
        <p class="text-xs text-ink/40 font-mono">TICKET MÉDIO</p>
        <p class="text-2xl font-display font-semibold mt-1">R$ {{ number_format($resumo['ticket_medio'], 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-card rounded-xl border border-ink/10 p-6 mb-8">
    <h2 class="font-display font-semibold mb-4">Faturamento por dia</h2>
    <canvas id="grafico-vendas" height="90"></canvas>
</div>

<div class="bg-card rounded-xl border border-ink/10 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 text-left text-ink/50 font-mono text-xs uppercase">
            <tr>
                <th class="p-3">Usuário</th>
                <th class="p-3">Sorteio</th>
                <th class="p-3">Valor</th>
                <th class="p-3">Pago em</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink/10">
            @forelse ($pagamentos as $p)
                <tr>
                    <td class="p-3">{{ $p->user->name }}</td>
                    <td class="p-3 text-ink/50">#{{ $p->giveaway_id ? str_pad($p->giveaway_id, 6, '0', STR_PAD_LEFT) : '—' }}</td>
                    <td class="p-3 font-medium">R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                    <td class="p-3 text-ink/40">{{ $p->paid_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-ink/40">Nenhuma venda no período selecionado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pagamentos->links() }}</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dados = @json($grafico);
        const ctx = document.getElementById('grafico-vendas');

        new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dados).map(d => {
                    const [ano, mes, dia] = d.split('-');
                    return `${dia}/${mes}`;
                }),
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: Object.values(dados),
                    backgroundColor: '#1F6F5C',
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => 'R$ ' + v } },
                },
            },
        });
    });
</script>
@endpush
@endsection
