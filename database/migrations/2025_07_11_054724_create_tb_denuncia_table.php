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
        Schema::create('tb_denuncia', function (Blueprint $table) {
            $table->id();

            // Quem fez a denúncia
            $table->foreignId('id_usuario_denunciante')->constrained('tb_usuario')->onDelete('cascade');

            // O que foi denunciado (usuário, postagem ou comentario)
            $table->foreignId('id_usuario_denunciado')
                ->nullable()
                ->constrained('tb_usuario')
                ->onDelete('cascade');
            
            $table->foreignId('id_postagem')
                ->nullable()
                ->constrained('tb_postagem')
                ->onDelete('cascade');
            
            $table->foreignId('id_comentario')
                ->nullable()
                ->constrained('tb_comentario')
                ->onDelete('cascade');

            // Informações denúncia
            $table->string('motivo_denuncia');
            $table->text('texto_denuncia')->nullable();
            $table->enum('status_denuncia', ['pendente', 'resolvida'])->default('pendente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_denuncia');
    }
};
