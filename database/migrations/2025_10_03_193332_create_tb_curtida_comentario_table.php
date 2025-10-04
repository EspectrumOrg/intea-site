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
        Schema::create('tb_curtida_comentario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_comentario')->constrained('tb_comentario')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->unique(['id_comentario', 'id_usuario']); // evita curtir duas vezes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_curtida_comentario');
    }
};
