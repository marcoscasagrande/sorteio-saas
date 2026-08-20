<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            // Filtros de participação configuráveis pelo organizador
            $table->unsignedTinyInteger('require_mention_count')->default(0)->after('instagram_media_id');
            $table->string('require_hashtag')->nullable()->after('require_mention_count');
            $table->boolean('require_follow')->default(false)->after('require_hashtag');

            // Múltiplos vencedores
            $table->unsignedTinyInteger('winners_count')->default(1)->after('require_follow');
            $table->json('winners')->nullable()->after('winner_comment'); // [{username, text}, ...]

            // Cache dos comentários já buscados na Graph API — evita re-consultar
            // a cada ação (sortear, re-sortear) e permite mostrar preview antes de sortear.
            $table->json('comments_cache')->nullable()->after('winners');

            // Histórico de re-sorteios (cada entrada guarda o resultado anterior
            // antes de ser substituído, com hash e timestamp próprios)
            $table->json('redraw_history')->nullable()->after('comments_cache');

            // Status intermediário: comentários sendo buscados em fila
            // (o enum antigo vira string livre pra caber o novo valor sem dor de cabeça)
        });

        // Troca o enum de status por string livre, pra caber o novo valor
        // 'fetching_comments' sem precisar do pacote doctrine/dbal (usado
        // pelo ->change() do Schema Builder). SQL direto, compatível com
        // o MySQL do aaPanel.
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE giveaways MODIFY status VARCHAR(30) NOT NULL DEFAULT 'ready'"
        );
    }

    public function down(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            $table->dropColumn([
                'require_mention_count', 'require_hashtag', 'require_follow',
                'winners_count', 'winners', 'comments_cache', 'redraw_history',
            ]);
        });
    }
};
