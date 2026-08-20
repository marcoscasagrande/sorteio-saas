<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            // Texto do comentário sorteado — exibido junto com o vencedor
            // na página pública de comprovação.
            $table->text('winner_comment')->nullable()->after('winner_username');
        });
    }

    public function down(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            $table->dropColumn('winner_comment');
        });
    }
};
