<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'giveaway_id', 'mp_payment_id', 'amount', 'status',
        'qr_code', 'qr_code_base64', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giveaway()
    {
        return $this->belongsTo(Giveaway::class);
    }
}
