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
        Schema::create('tb_banimento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('tb_usuario')->onDelete('cascade');
            $table->text('infracao');
            $table->text('motivo');
            $table->foreignId('id_postagem')->nullable()->constrained('tb_postagem')->onDelete('set null');
            $table->foreignId('id_comentario')->nullable()->constrained('tb_comentario')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_banimento');
    }
};
