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
        Schema::create('tb_comentario', function (Blueprint $table) {
            $table->id();
            // Comentário pertence a uma postagem (opcional)
            $table->foreignId('id_postagem')
                    ->nullable()
                    ->constrained('tb_postagem')
                    ->onDelete('cascade');
            // Comentário pertence a um outro comentário
            $table->foreignId('id_comentario_pai')
                    ->nullable()
                    ->constrained('tb_comentario')
                    ->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->text('comentario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_comentario');
    }
};
