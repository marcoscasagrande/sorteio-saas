<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request, MercadoPagoService $mercadoPago)
    {
        // O Mercado Pago manda o id do pagamento; NUNCA confie no status vindo
        // do payload — sempre re-consulte a API do MP pra confirmar.
        $mpPaymentId = $request->input('data.id') ?? $request->query('id');

        if (! $mpPaymentId) {
            return response()->noContent(Response::HTTP_OK);
        }

        $dadosPagamento = $mercadoPago->consultarPagamento($mpPaymentId);

        $payment = Payment::where('mp_payment_id', $mpPaymentId)->first();

        if (! $payment) {
            return response()->noContent(Response::HTTP_OK);
        }

        $status = $dadosPagamento['status'] ?? null; // approved | pending | rejected

        if ($status === 'approved' && $payment->status !== 'approved') {
            $payment->update([
                'status' => 'approved',
                'paid_at' => now(),
            ]);

            // Libera o sorteio automaticamente
            $payment->giveaway?->update(['status' => 'ready']);

            AuditLog::record(
                'pagamento.aprovado',
                "Pagamento #{$payment->id} aprovado via Pix (R$ {$payment->amount})",
                $payment->user
            );
        } elseif (in_array($status, ['rejected', 'cancelled'])) {
            $payment->update(['status' => 'rejected']);
        }

        return response()->noContent(Response::HTTP_OK);
    }
}
