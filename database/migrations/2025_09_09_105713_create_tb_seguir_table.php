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
        Schema::create('tb_seguir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('segue_id'); // Quem segue
            $table->unsignedBigInteger('seguindo_id'); // Quem está sendo seguido
            $table->timestamps();
            $table->foreign('segue_id')->references('id')->on('tb_usuario')->onDelete('cascade');
            $table->foreign('seguindo_id')->references('id')->on('tb_usuario')->onDelete('cascade');
            $table->unique(['segue_id', 'seguindo_id']); // Impede duplicatas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguir');
    }
};
    