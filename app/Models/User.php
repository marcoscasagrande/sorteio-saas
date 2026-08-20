<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token', 'two_factor_code'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_expires_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function instagramTokens()
    {
        return $this->hasMany(InstagramToken::class);
    }

    public function giveaways()
    {
        return $this->hasMany(Giveaway::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
