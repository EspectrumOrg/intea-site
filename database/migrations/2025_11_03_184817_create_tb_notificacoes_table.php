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
        Schema::create('tb_notificacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitante_id'); // quem enviou a solicitação
            $table->unsignedBigInteger('alvo_id');        // o dono da conta privada
            $table->string('tipo')->default('seguir');    // tipo da notificação
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('solicitante_id')->references('id')->on('tb_usuario')->onDelete('cascade');
            $table->foreign('alvo_id')->references('id')->on('tb_usuario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_notificacoes');
    }
};
