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
        // media_id e contar os comentários do post. Deixei mockado com um
        // valor de exemplo — troque pela chamada real ao endpoint /{media-id}/comments.
        $commentsCount = $this->buscarQuantidadeDeComentarios($request->instagram_post_url, $user);

        $giveaway = Giveaway::create([
            'user_id' => $user->id,
            'instagram_post_url' => $request->instagram_post_url,
            'comments_count' => $commentsCount,
            'status' => $commentsCount > 100 ? 'pending_payment' : 'ready',
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
        $this->authorize('view', $giveaway); // ver AuthServiceProvider / Policy
        $payment = $giveaway->payment()->latest()->first();

        return view('giveaways.pay', compact('giveaway', 'payment'));
    }

    public function show(Giveaway $giveaway)
    {
        return view('giveaways.show', compact('giveaway'));
    }

    // Executa o sorteio em si (só permitido se status = ready)
    public function draw(Giveaway $giveaway)
    {
        if ($giveaway->needsPayment()) {
            abort(403, 'Pagamento pendente para liberar este sorteio.');
        }

        // Lógica real: buscar lista de comentários via Graph API, aplicar
        // filtros (menção obrigatória, remover duplicados/bots), sortear
        // aleatoriamente e gerar hash público de auditoria.
        $vencedor = '@exemplo_vencedor';
        $hash = hash('sha256', $giveaway->id.$vencedor.now());

        $giveaway->update([
            'winner_username' => $vencedor,
            'result_hash' => $hash,
            'status' => 'completed',
            'drawn_at' => now(),
        ]);

        return redirect()->route('giveaways.show', $giveaway);
    }

    private function buscarQuantidadeDeComentarios(string $url, $user): int
    {
        // TODO: substituir por chamada real à Instagram Graph API
        // usando o access_token salvo em $user->instagramTokens
        return 0;
    }
}
