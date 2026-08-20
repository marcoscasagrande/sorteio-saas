<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);

            // 'coins'     — pacote pago uma vez, cada sorteio consome 1 moeda
            // 'unlimited' — acesso ilimitado enquanto a assinatura estiver ativa
            $table->enum('plan_type', ['coins', 'unlimited'])->default('coins');

            // Usado só quando plan_type = 'coins'
            $table->unsignedInteger('coins_amount')->nullable();

            // Usado só quando plan_type = 'unlimited' — define a duração do acesso
            $table->enum('period', ['unico', 'mensal', 'anual'])->default('unico');

            $table->boolean('is_featured')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
