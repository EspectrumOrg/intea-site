<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_postagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario')->onDelete('cascade');
            $table->text('texto_postagem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() //tirar tabelas filhas primeiro
    {
        Schema::dropIfExists('tb_curtida_postagem');
        Schema::dropIfExists('tb_comentario_postagem');
        Schema::dropIfExists('tb_imagem_postagem');
        Schema::dropIfExists('tb_postagem');
    }
};
