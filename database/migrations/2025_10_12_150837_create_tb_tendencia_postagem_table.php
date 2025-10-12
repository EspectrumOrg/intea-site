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
         Schema::create('tb_tendencia_postagem', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tendencia_id')->constrained('tb_tendencias')->onDelete('cascade');
        $table->foreignId('postagem_id')->constrained('tb_postagem')->onDelete('cascade');
        $table->timestamps();
        
        $table->unique(['tendencia_id', 'postagem_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tendencias_postagens');
    }
};
