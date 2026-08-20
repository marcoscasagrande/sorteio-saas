<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Giveaway;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchGiveawayComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 15;

    public function __construct(public Giveaway $giveaway)
    {
    }

    public function handle(): void
    {
        // TODO: substituir por chamada real à Instagram Graph API
        // GET /{media-id}/comments, com paginação até esgotar os comentários,
        // usando o token salvo em $this->giveaway->user->instagramTokens.
        // Formato esperado por item: ['username', 'text', 'mentions', 'is_follower']
        $comentarios = [];

        $limite = (int) Setting::get(Setting::FREE_COMMENT_LIMIT, '100');

        $this->giveaway->update([
            'comments_cache' => $comentarios,
            'comments_count' => count($comentarios),
            'status' => count($comentarios) > $limite ? 'pending_payment' : 'ready',
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->giveaway->update(['status' => 'ready']);

        AuditLog::record(
            'sorteio.busca_comentarios_falhou',
            "Sorteio #{$this->giveaway->id}: {$exception->getMessage()}",
            $this->giveaway->user
        );
    }
}
