<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected string $baseUrl = 'https://api.mercadopago.com';
    protected ?string $accessToken;

    public function __construct()
    {
        // Prioridade: chave cadastrada no admin (banco) > .env como fallback.
        // Isso permite trocar a chave sem precisar mexer no servidor.
        $this->accessToken = Setting::get(Setting::MP_ACCESS_TOKEN)
            ?: config('services.mercadopago.access_token');
    }

    public function estaConfigurado(): bool
    {
        return filled($this->accessToken);
    }

    /**
     * Cria uma cobrança Pix e devolve o QR Code (copia e cola + imagem base64).
     */
    public function criarCobrancaPix(Payment $payment): array
    {
        if (! $this->estaConfigurado()) {
            throw new \RuntimeException('Access Token do Mercado Pago não configurado. Cadastre em Admin > Configurações.');
        }

        $idempotencyKey = (string) Str::uuid();

        $descricao = $payment->isPlanPurchase()
            ? "Compra do plano: {$payment->plan->name}"
            : "Liberação de sorteio #{$payment->giveaway_id}";

        $response = Http::withToken($this->accessToken)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post("{$this->baseUrl}/v1/payments", [
                'transaction_amount' => (float) $payment->amount,
                'description' => $descricao,
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
