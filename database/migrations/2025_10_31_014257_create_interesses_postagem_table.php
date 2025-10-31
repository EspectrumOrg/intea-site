<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interesse_postagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->foreignId('postagem_id')->constrained('tb_postagem')->onDelete('cascade');
            $table->enum('tipo', ['automático', 'manual', 'sugerido'])->default('automático');
            $table->foreignId('categorizado_por')->nullable()->constrained('tb_usuario')->onDelete('set null');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['interesse_id', 'postagem_id']);
            $table->index('tipo');
        });

        Schema::table('tb_postagem', function (Blueprint $table) {
            $table->enum('visibilidade_interesse', ['publico', 'seguidores', 'privado'])->default('publico');
            $table->boolean('bloqueada_auto')->default(false);
            $table->boolean('removida_manual')->default(false);
            $table->text('motivo_remocao')->nullable();
            $table->timestamp('removida_em')->nullable();
            $table->foreignId('removida_por')->nullable()->constrained('tb_usuario')->onDelete('set null');
        });
    }

    public function down()
    {
        // Remover a tabela de relacionamento primeiro
        Schema::dropIfExists('interesse_postagem');
        
        // Remover as colunas da tabela postagem SEM as constraints primeiro
        Schema::table('tb_postagem', function (Blueprint $table) {
            // Remover a foreign key constraint primeiro
            $table->dropForeign(['removida_por']);
            
            // Agora remover as colunas
            $table->dropColumn([
                'visibilidade_interesse',
                'bloqueada_auto', 
                'removida_manual',
                'motivo_remocao',
                'removida_em',
                'removida_por'
            ]);
        });
    }
};