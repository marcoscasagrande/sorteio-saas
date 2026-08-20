<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    protected $fillable = [
        'user_id', 'instagram_post_url', 'instagram_media_id', 'comments_count',
        'winner_username', 'result_hash', 'status', 'drawn_at',
    ];

    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Regra de negócio central: acima de 100 comentários precisa de pagamento aprovado
    public function needsPayment(): bool
    {
        return $this->comments_count > 100
            && ! $this->payment()->where('status', 'approved')->exists();
    }
}
