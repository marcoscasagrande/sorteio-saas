<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    protected $fillable = [
        'user_id', 'instagram_post_url', 'instagram_media_id', 'comments_count',
        'require_mention_count', 'require_hashtag', 'require_follow', 'winners_count',
        'winner_username', 'winner_comment', 'winners', 'comments_cache', 'redraw_history',
        'result_hash', 'status', 'drawn_at',
    ];

    protected $casts = [
        'drawn_at' => 'datetime',
        'winners' => 'array',
        'comments_cache' => 'array',
        'redraw_history' => 'array',
        'require_follow' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Regra de negócio central: acima do limite gratuito precisa de pagamento aprovado.
    // O limite e o valor são configuráveis pelo admin (Setting), com fallback padrão.
    public function needsPayment(): bool
    {
        $limite = (int) Setting::get(Setting::FREE_COMMENT_LIMIT, '100');

        return $this->comments_count > $limite
            && ! $this->payment()->where('status', 'approved')->exists();
    }

    // URL pública de comprovação (não exige login) — o hash SHA-256 funciona
    // como prova de que o resultado não foi alterado depois do sorteio.
    public function verificationUrl(): ?string
    {
        return $this->result_hash
            ? route('giveaways.verify', $this->result_hash)
            : null;
    }

    /**
     * Aplica os filtros de participação configurados (menção, hashtag, seguir)
     * sobre a lista de comentários em cache e devolve só os elegíveis.
     * Cada comentário no cache tem o formato:
     * ['username' => ..., 'text' => ..., 'mentions' => int, 'is_follower' => bool]
     */
    public function comentariosElegiveis(): array
    {
        $comentarios = $this->comments_cache ?? [];

        return array_values(array_filter($comentarios, function ($c) {
            if ($this->require_mention_count > 0 && ($c['mentions'] ?? 0) < $this->require_mention_count) {
                return false;
            }
            if ($this->require_hashtag && ! str_contains(strtolower($c['text'] ?? ''), strtolower($this->require_hashtag))) {
                return false;
            }
            if ($this->require_follow && empty($c['is_follower'])) {
                return false;
            }

            return true;
        }));
    }
}
