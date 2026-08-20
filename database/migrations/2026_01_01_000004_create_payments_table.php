<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('giveaway_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mp_payment_id')->nullable()->unique(); // id retornado pelo Mercado Pago
            $table->decimal('amount', 8, 2)->default(9.99);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->text('qr_code')->nullable();       // copia e cola do Pix
            $table->text('qr_code_base64')->nullable(); // imagem do QR Code
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
