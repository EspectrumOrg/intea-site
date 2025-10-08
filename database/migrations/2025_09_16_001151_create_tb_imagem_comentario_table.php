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
        Schema::create('tb_imagem_comentario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_comentario')->constrained('tb_comentario')->onDelete('cascade');
            $table->string('caminho_imagem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_imagem_comentario');
    }
};
