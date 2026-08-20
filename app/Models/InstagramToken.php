<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramToken extends Model
{
    protected $fillable = [
        'user_id', 'instagram_user_id', 'username', 'access_token', 'expires_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted', // nunca fica em texto puro no banco
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
