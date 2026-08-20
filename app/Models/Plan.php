<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'plan_type', 'coins_amount', 'period',
        'is_featured', 'active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'active' => 'boolean',
    ];

    public function scopeAtivos($query)
    {
        return $query->where('active', true)->orderBy('sort_order');
    }

    public function isCoins(): bool
    {
        return $this->plan_type === 'coins';
    }

    public function isUnlimited(): bool
    {
        return $this->plan_type === 'unlimited';
    }

    // Quantos dias de acesso ilimitado esse plano concede — só faz sentido
    // para plan_type = 'unlimited'.
    public function duracaoEmDias(): int
    {
        return match ($this->period) {
            'mensal' => 30,
            'anual' => 365,
            default => 0,
        };
    }

    public function limiteDescricao(): string
    {
        if ($this->isCoins()) {
            return "{$this->coins_amount} moedas — 1 sorteio = 1 moeda";
        }

        return 'Sorteios ilimitados por '.$this->periodoLabel();
    }

    public function periodoLabel(): string
    {
        return ['unico' => 'pagamento único', 'mensal' => 'mês', 'anual' => 'ano'][$this->period] ?? $this->period;
    }
}
