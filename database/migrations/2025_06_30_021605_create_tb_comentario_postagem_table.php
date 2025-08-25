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
        Schema::create('tb_comentario_postagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_postagem')->constrained('tb_postagem')->onDelete('cascade');
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
        Schema::dropIfExists('tb_comentario_postagem');
    }
};
