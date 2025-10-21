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
        Schema::create('tb_curtida', function (Blueprint $table) {
            $table->id();
            // Curtida pertence a uma postagem (opcional)
            $table->foreignId('id_postagem')
                ->nullable()
                ->constrained('tb_postagem')
                ->onDelete('cascade');
            // Curtida pertence a um comentário (opcional)
            $table->foreignId('id_comentario')
                ->nullable()
                ->constrained('tb_comentario')
                ->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            // evita curtir duas vezes
            $table->index(['id_postagem', 'id_usuario']);
            $table->index(['id_comentario', 'id_usuario']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_curtida');
    }
};
