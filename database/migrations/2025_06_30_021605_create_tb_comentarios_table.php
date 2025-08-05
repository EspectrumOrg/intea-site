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
        Schema::create('tb_comentarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPostagem');
            $table->foreign('idPostagem')->references('id')->on('tb_postagem');
            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('tb_usuario');
            $table->text('comentario');
            $table->boolean('is_autor')->default(false);
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
        Schema::dropIfExists('tb_comentarios');
    }
};
