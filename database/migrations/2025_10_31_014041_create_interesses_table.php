<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interesses', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('icone')->nullable();
            $table->string('cor')->default('#3B82F6');
            $table->text('descricao')->nullable();
            $table->text('sobre')->nullable();
            $table->string('banner')->nullable();
            $table->integer('contador_membros')->default(0);
            $table->integer('contador_postagens')->default(0);
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->boolean('moderacao_ativa')->default(true);
            $table->integer('limite_alertas_ban')->default(3);
            $table->integer('dias_expiracao_alerta')->default(30);
            $table->timestamps();
        });

        Schema::create('interesse_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->boolean('notificacoes')->default(true);
            $table->timestamp('seguindo_desde')->useCurrent();
            $table->timestamps();

            $table->unique(['usuario_id', 'interesse_id']);
        });

        Schema::create('interesse_moderadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->string('cargo')->default('moderador');
            $table->timestamps();

            $table->unique(['interesse_id', 'usuario_id']);
        });

        Schema::table('tb_usuario', function (Blueprint $table) {
            $table->boolean('onboarding_concluido')->default(false);
            $table->timestamp('onboarding_concluido_em')->nullable();
        });
    }

    public function down()
    {
        // Remover na ordem inversa para evitar problemas de foreign key
        Schema::dropIfExists('interesse_moderadores');
        Schema::dropIfExists('interesse_usuario');
        Schema::dropIfExists('interesses');
        
        Schema::table('tb_usuario', function (Blueprint $table) {
            $table->dropColumn(['onboarding_concluido', 'onboarding_concluido_em']);
        });
    }
};