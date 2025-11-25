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
        Schema::create('tb_banimento_reconsideracao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('tb_usuario')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('tb_usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_banimento_reconsideracao');
    }
};
