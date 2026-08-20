<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'period',
        'giveaways_per_period', 'is_featured', 'active', 'sort_order',
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

    public function limiteDescricao(): string
    {
        return $this->giveaways_per_period
            ? "{$this->giveaways_per_period} sorteios/".$this->periodoLabel()
            : 'Sorteios ilimitados';
    }

    public function periodoLabel(): string
    {
        return ['unico' => 'pagamento único', 'mensal' => 'mês', 'anual' => 'ano'][$this->period] ?? $this->period;
    }
}
