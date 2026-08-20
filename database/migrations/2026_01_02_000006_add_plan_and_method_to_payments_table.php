<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Um pagamento é OU a liberação de um sorteio avulso (giveaway_id)
            // OU a compra de um plano (plan_id) — nunca os dois.
            $table->foreignId('plan_id')->nullable()->after('giveaway_id')
                ->constrained()->nullOnDelete();

            // 'pix' = cobrança normal via Mercado Pago
            // 'credit' = sorteio liberado gastando 1 moeda do saldo do usuário
            //            (amount fica 0, não passa pelo Mercado Pago)
            $table->string('method')->default('pix')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn('method');
        });
    }
};
