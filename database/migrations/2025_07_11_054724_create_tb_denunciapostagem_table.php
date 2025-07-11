<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_denunciapostagem', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPostagem');
            $table->foreign('idPostagem')->references('id')->on('tb_postagem');
            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('tb_usuario');
            $table->string('mensagemDenuncia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_denunciapostagem');
    }
};
