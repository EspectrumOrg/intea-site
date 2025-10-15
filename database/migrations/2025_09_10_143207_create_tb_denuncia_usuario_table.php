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
        Schema::create('tb_denuncia_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario_denunciado')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('id_usuario_denunciante')->constrained('tb_usuario')->onDelete('cascade');
            $table->string('motivo_denuncia');
            $table->text('texto_denuncia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_denuncia_usuario');
    }
};
