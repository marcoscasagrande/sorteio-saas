<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('instagram_post_url');
            $table->string('instagram_media_id')->nullable();
            $table->unsignedInteger('comments_count')->default(0);
            $table->string('winner_username')->nullable();
            $table->string('result_hash')->nullable(); // hash público de auditoria
            $table->enum('status', [
                'pending_payment', // >100 comentários, aguardando pix
                'ready',           // liberado, pode sortear
                'completed',       // já sorteado
            ])->default('ready');
            $table->timestamp('drawn_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giveaways');
    }
};
