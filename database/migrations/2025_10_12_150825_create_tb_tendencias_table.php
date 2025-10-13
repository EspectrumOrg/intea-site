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
       Schema::create('tb_tendencias', function (Blueprint $table) {
        $table->id();
        $table->string('hashtag')->unique(); // #laravel, #php
        $table->string('slug')->unique(); // laravel, php
        $table->integer('contador_uso')->default(0);
        $table->timestamp('ultimo_uso')->nullable();
        $table->timestamps();
        
        $table->index('contador_uso');
        $table->index('ultimo_uso');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                Schema::dropIfExists('tb_tendencia_postagem');
                Schema::dropIfExists('tb_tendencias');

    }
};
