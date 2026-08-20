<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected string $baseUrl = 'https://api.mercadopago.com';
    protected string $accessToken;

    public function __construct()
    {
        // Defina MERCADOPAGO_ACCESS_TOKEN no seu .env (token de PRODUÇÃO, não o de teste)
        $this->accessToken = config('services.mercadopago.access_token');
    }

    /**
     * Cria uma cobrança Pix e devolve o QR Code (copia e cola + imagem base64).
     */
    public function criarCobrancaPix(Payment $payment): array
    {
        $idempotencyKey = (string) Str::uuid();

        $response = Http::withToken($this->accessToken)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post("{$this->baseUrl}/v1/payments", [
                'transaction_amount' => (float) $payment->amount,
                'description' => "Liberação de sorteio #{$payment->giveaway_id}",
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $payment->user->email,
                    'first_name' => $payment->user->name,
                ],
                // notification_url é chamada pelo MP quando o status do pagamento muda
                'notification_url' => route('webhooks.mercadopago'),
                'external_reference' => (string) $payment->id,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Falha ao criar cobrança Pix: '.$response->body());
        }

        $data = $response->json();
        $txData = $data['point_of_interaction']['transaction_data'] ?? [];

        $payment->update([
            'mp_payment_id' => $data['id'],
            'qr_code' => $txData['qr_code'] ?? null,
            'qr_code_base64' => $txData['qr_code_base64'] ?? null,
        ]);

        return $data;
    }

    /**
     * Consulta o status atual de um pagamento direto na API do MP.
     * Use isso no webhook em vez de confiar cegamente no payload recebido.
     */
    public function consultarPagamento(string $mpPaymentId): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$mpPaymentId}");

        if ($response->failed()) {
            throw new \RuntimeException('Falha ao consultar pagamento: '.$response->body());
        }

        return $response->json();
    }
}
