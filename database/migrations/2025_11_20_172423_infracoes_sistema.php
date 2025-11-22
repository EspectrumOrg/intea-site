<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('infracoes_sistema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario');
            $table->string('tipo');
            $table->text('descricao');
            $table->text('conteudo_original')->nullable();
            $table->foreignId('postagem_id')->nullable()->constrained('tb_postagem'); 
            $table->foreignId('interesse_id')->nullable()->constrained('interesses');
            $table->foreignId('reportado_por')->nullable()->constrained('tb_usuario');
            $table->foreignId('moderador_id')->nullable()->constrained('tb_usuario');
            $table->boolean('verificada')->default(false);
            $table->timestamp('verificada_em')->nullable();
            $table->timestamps();
            
            $table->index(['usuario_id', 'verificada']);
            $table->index(['tipo', 'verificada']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('infracoes_sistema');
    }
};