<?php

namespace App\Http\Controllers;

use App\Models\Giveaway;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class GiveawayController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        if (! $user->instagramTokens()->exists()) {
            return redirect()->route('instagram.connect')
                ->with('info', 'Conecte sua conta do Instagram antes de criar um sorteio.');
        }

        return view('giveaways.create');
    }

    public function store(Request $request, MercadoPagoService $mercadoPago)
    {
        $request->validate([
            'instagram_post_url' => ['required', 'url'],
        ]);

        $user = $request->user();

        // Aqui entraria a chamada real à Graph API do Instagram para buscar
        // media_id e a lista de comentários. Deixei mockado — troque pela
        // chamada real ao endpoint /{media-id}/comments (ver método abaixo).
        $comentarios = $this->buscarComentarios($request->instagram_post_url, $user);

        $giveaway = Giveaway::create([
            'user_id' => $user->id,
            'instagram_post_url' => $request->instagram_post_url,
            'comments_count' => count($comentarios),
            'status' => count($comentarios) > 100 ? 'pending_payment' : 'ready',
        ]);

        if ($giveaway->needsPayment()) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'giveaway_id' => $giveaway->id,
                'amount' => 9.99,
                'status' => 'pending',
            ]);

            $mercadoPago->criarCobrancaPix($payment);

            return redirect()->route('giveaways.pay', $giveaway)
                ->with('info', 'Este sorteio tem mais de 100 comentários. Pague o Pix para liberar.');
        }

        return redirect()->route('giveaways.show', $giveaway);
    }

    public function pay(Giveaway $giveaway)
    {
        $this->autorizarDono($giveaway);
        $payment = $giveaway->payment()->latest()->first();

        return view('giveaways.pay', compact('giveaway', 'payment'));
    }

    public function show(Giveaway $giveaway)
    {
        $this->autorizarDono($giveaway);

        return view('giveaways.show', compact('giveaway'));
    }

    // Executa o sorteio em si (só permitido se status = ready)
    public function draw(Giveaway $giveaway)
    {
        $this->autorizarDono($giveaway);

        if ($giveaway->needsPayment()) {
            abort(403, 'Pagamento pendente para liberar este sorteio.');
        }

        if ($giveaway->status === 'completed') {
            return redirect()->route('giveaways.show', $giveaway);
        }

        // Lógica real: buscar a lista de comentários já salva/cacheada deste
        // sorteio via Graph API, aplicar filtros (menção obrigatória, remover
        // duplicados/bots) e sortear aleatoriamente entre os elegíveis.
        $comentarios = $this->buscarComentarios($giveaway->instagram_post_url, $giveaway->user);
        $sorteado = ! empty($comentarios)
            ? $comentarios[array_rand($comentarios)]
            : ['username' => '@participante_exemplo', 'text' => 'Eu quero! 🎉'];

        // Hash público de auditoria: qualquer pessoa pode recalcular esse
        // SHA-256 e confirmar que o resultado não foi alterado depois do sorteio.
        $payload = $giveaway->id.'|'.$sorteado['username'].'|'.$sorteado['text'].'|'.now()->toIso8601String();
        $hash = hash('sha256', $payload);

        $giveaway->update([
            'winner_username' => $sorteado['username'],
            'winner_comment' => $sorteado['text'],
            'result_hash' => $hash,
            'status' => 'completed',
            'drawn_at' => now(),
        ]);

        return redirect()->route('giveaways.show', $giveaway)
            ->with('just_drawn', true);
    }

    // Página pública de comprovação — não exige login. É o link que o
    // organizador compartilha com a audiência pra provar que o sorteio
    // não foi manipulado.
    public function verify(string $hash)
    {
        $giveaway = Giveaway::where('result_hash', $hash)
            ->where('status', 'completed')
            ->firstOrFail();

        return view('giveaways.verify', compact('giveaway'));
    }

    private function autorizarDono(Giveaway $giveaway): void
    {
        if ($giveaway->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    /**
     * TODO: substituir por chamada real à Instagram Graph API
     * GET /{media-id}/comments usando o access_token salvo em
     * $user->instagramTokens, com paginação até esgotar os comentários.
     * Retorna um array de ['username' => ..., 'text' => ...].
     */
    private function buscarComentarios(string $url, $user): array
    {
        return [];
    }
}
