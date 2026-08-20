<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $de = $request->filled('de')
            ? Carbon::parse($request->de)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $ate = $request->filled('ate')
            ? Carbon::parse($request->ate)->endOfDay()
            : now()->endOfDay();

        $pagamentosAprovados = Payment::where('status', 'approved')
            ->whereBetween('paid_at', [$de, $ate]);

        $resumo = [
            'faturamento' => (clone $pagamentosAprovados)->sum('amount'),
            'quantidade' => (clone $pagamentosAprovados)->count(),
            'ticket_medio' => (clone $pagamentosAprovados)->avg('amount') ?? 0,
        ];

        // Série diária pro gráfico — dia => total faturado
        $porDia = (clone $pagamentosAprovados)
            ->selectRaw('DATE(paid_at) as dia, SUM(amount) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        // Preenche os dias sem venda com zero, pra o gráfico não pular
        $periodo = collect();
        $cursor = $de->copy();
        while ($cursor->lte($ate)) {
            $chave = $cursor->format('Y-m-d');
            $periodo->put($chave, (float) ($porDia[$chave] ?? 0));
            $cursor->addDay();
        }

        $ultimosPagamentos = (clone $pagamentosAprovados)
            ->with(['user', 'giveaway'])
            ->latest('paid_at')
            ->paginate(20);

        return view('admin.reports.sales', [
            'resumo' => $resumo,
            'grafico' => $periodo,
            'pagamentos' => $ultimosPagamentos,
            'de' => $de->format('Y-m-d'),
            'ate' => $ate->format('Y-m-d'),
        ]);
    }
}
