<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\MercadoPagoService;

class PlanPurchaseController extends Controller
{
    // Cria (ou reaproveita) a cobrança Pix pra compra do plano
    public function checkout(Plan $plan, MercadoPagoService $mercadoPago)
    {
        if (! $plan->active) {
            abort(404);
        }

        $user = auth()->user();

        // Reaproveita uma cobrança pendente recente do mesmo plano, se existir,
        // pra não gerar QR Code novo a cada refresh da página.
        $payment = Payment::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $payment) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'method' => 'pix',
                'status' => 'pending',
            ]);

            $mercadoPago->criarCobrancaPix($payment);
        }

        return view('plans.checkout', compact('plan', 'payment'));
    }
}
