<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('palavras_proibidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->string('palavra');
            $table->enum('tipo', ['exata', 'parcial'])->default('parcial');
            $table->boolean('ativo')->default(true);
            $table->foreignId('adicionado_por')->constrained('tb_usuario')->onDelete('cascade');
            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->unique(['interesse_id', 'palavra']);
            $table->index('palavra');
        });

        Schema::create('alertas_moderacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->foreignId('postagem_id')->nullable()->constrained('tb_postagem')->onDelete('set null');
            $table->text('motivo');
            $table->enum('gravidade', ['leve', 'moderado', 'grave'])->default('leve');
            $table->foreignId('moderador_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->timestamp('expiracao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['usuario_id', 'interesse_id']);
            $table->index('expiracao');
        });

        Schema::create('interesse_expulsoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('interesse_id')->constrained()->onDelete('cascade');
            $table->text('motivo');
            $table->foreignId('moderador_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->timestamp('expulso_ate')->nullable();
            $table->boolean('permanente')->default(false);
            $table->timestamps();

            $table->unique(['usuario_id', 'interesse_id']);
            $table->index('expulso_ate');
        });
    }

    public function down()
    {
        // Remover na ordem inversa para evitar problemas de foreign key
        Schema::dropIfExists('interesse_expulsoes');
        Schema::dropIfExists('alertas_moderacao');
        Schema::dropIfExists('palavras_proibidas');
    }
};