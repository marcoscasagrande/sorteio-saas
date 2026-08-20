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

            if ($payment->isPlanPurchase()) {
                $this->aplicarPlanoComprado($payment);
            } else {
                // Libera o sorteio avulso automaticamente
                $payment->giveaway?->update(['status' => 'ready']);
            }

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

    private function aplicarPlanoComprado(Payment $payment): void
    {
        $plano = $payment->plan;
        $user = $payment->user;

        if (! $plano) {
            return;
        }

        if ($plano->isCoins()) {
            $user->increment('coin_balance', $plano->coins_amount ?? 0);

            AuditLog::record(
                'plano.moedas_creditadas',
                "+{$plano->coins_amount} moedas do plano \"{$plano->name}\" (pagamento #{$payment->id})",
                $user
            );

            return;
        }

        // Plano ilimitado: estende a partir de agora, ou a partir do fim do
        // período atual se o usuário já tiver acesso ativo (não perde tempo pago).
        $base = $user->temAcessoIlimitado() ? $user->unlimited_until : now();
        $novaData = $base->copy()->addDays($plano->duracaoEmDias());

        $user->update(['unlimited_until' => $novaData]);

        AuditLog::record(
            'plano.acesso_ilimitado_estendido',
            "Acesso ilimitado do plano \"{$plano->name}\" até {$novaData->format('d/m/Y')} (pagamento #{$payment->id})",
            $user
        );
    }
}
