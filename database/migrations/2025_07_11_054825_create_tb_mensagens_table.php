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
        Schema::create('tb_mensagens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversa_id');
            $table->foreign('conversa_id')->references('id')->on('tb_chatprivado')->onDelete('cascade');
            $table->unsignedBigInteger('remetente_id');
            $table->foreign('remetente_id')->references('id')->on('tb_usuario')->onDelete('cascade');   
            $table->text('texto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_mensagens');
    }
};
