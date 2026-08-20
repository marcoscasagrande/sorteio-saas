<?php

namespace App\Http\Controllers;

use App\Jobs\FetchGiveawayComments;
use App\Models\AuditLog;
use App\Models\Giveaway;
use App\Models\Payment;
use App\Models\Setting;
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

    public function store(Request $request)
    {
        $request->validate([
            'instagram_post_url' => ['required', 'url'],
            'winners_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'require_mention_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'require_hashtag' => ['nullable', 'string', 'max:50'],
            'require_follow' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        $giveaway = Giveaway::create([
            'user_id' => $user->id,
            'instagram_post_url' => $request->instagram_post_url,
            'winners_count' => $request->integer('winners_count', 1),
            'require_mention_count' => $request->integer('require_mention_count', 0),
            'require_hashtag' => $request->filled('require_hashtag') ? ltrim($request->require_hashtag, '#') : null,
            'require_follow' => $request->boolean('require_follow'),
            'status' => 'fetching_comments',
        ]);

        // Busca dos comentários roda em fila — evita travar a requisição
        // esperando a Graph API responder (posts grandes podem demorar).
        FetchGiveawayComments::dispatch($giveaway);

        return redirect()->route('giveaways.show', $giveaway)
            ->with('info', 'Buscando os comentários do post — isso leva alguns segundos.');
    }

    public function pay(Giveaway $giveaway, MercadoPagoService $mercadoPago)
    {
        $this->autorizarDono($giveaway);

        if (! $giveaway->needsPayment()) {
            return redirect()->route('giveaways.show', $giveaway);
        }

        $payment = $giveaway->payment()->latest()->first();

        // Cria a cobrança na hora, se ainda não existir uma (ex: sorteio
        // ficou pending_payment depois que a fila terminou de contar).
        if (! $payment) {
            $payment = Payment::create([
                'user_id' => $giveaway->user_id,
                'giveaway_id' => $giveaway->id,
                'amount' => (float) Setting::get(Setting::PRICE_PER_GIVEAWAY, '9.99'),
                'status' => 'pending',
            ]);

            $mercadoPago->criarCobrancaPix($payment);
        }

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

        $this->executarSorteio($giveaway);

        AuditLog::record('sorteio.sortear', "Sorteio #{$giveaway->id} realizado", $giveaway->user);

        return redirect()->route('giveaways.show', $giveaway)->with('just_drawn', true);
    }

    // Re-sorteio: guarda o resultado atual no histórico e sorteia de novo.
    // Útil quando o vencedor não responde e o organizador precisa trocar.
    public function redraw(Giveaway $giveaway)
    {
        $this->autorizarDono($giveaway);

        if ($giveaway->status !== 'completed') {
            abort(403, 'Este sorteio ainda não foi realizado.');
        }

        $historico = $giveaway->redraw_history ?? [];
        $historico[] = [
            'winners' => $giveaway->winners ?? [['username' => $giveaway->winner_username, 'text' => $giveaway->winner_comment]],
            'result_hash' => $giveaway->result_hash,
            'drawn_at' => $giveaway->drawn_at?->toIso8601String(),
        ];
        $giveaway->update(['redraw_history' => $historico]);

        $this->executarSorteio($giveaway);

        AuditLog::record('sorteio.resortear', "Sorteio #{$giveaway->id} refeito", $giveaway->user);

        return redirect()->route('giveaways.show', $giveaway)->with('just_drawn', true);
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

    private function executarSorteio(Giveaway $giveaway): void
    {
        $elegiveis = $giveaway->comentariosElegiveis();

        if (empty($elegiveis)) {
            $elegiveis = [['username' => '@participante_exemplo', 'text' => 'Eu quero! 🎉']];
        }

        shuffle($elegiveis);
        $sorteados = array_slice($elegiveis, 0, max(1, $giveaway->winners_count));

        // Hash público de auditoria: qualquer pessoa pode recalcular esse
        // SHA-256 e confirmar que o resultado não foi alterado depois do sorteio.
        $payload = $giveaway->id.'|'.json_encode($sorteados).'|'.now()->toIso8601String();
        $hash = hash('sha256', $payload);

        $giveaway->update([
            'winners' => $sorteados,
            'winner_username' => $sorteados[0]['username'],
            'winner_comment' => $sorteados[0]['text'] ?? null,
            'result_hash' => $hash,
            'status' => 'completed',
            'drawn_at' => now(),
        ]);
    }

    private function autorizarDono(Giveaway $giveaway): void
    {
        if ($giveaway->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
