<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

       /**
     * esse forma de usuario1_id e  usuario2_id depois vai ser  feita uma verificao para que o usuairo 1 seja 
     * sempre o menor id nao da pra usar o campo cpf porque cpf e um campo sensivel e nome nao e 
     * um campo unico
     */
    public function up(): void
    {
        Schema::create('tb_chatprivado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario1_id');
            $table->foreign('usuario1_id')->references('id')->on('tb_usuario')->onDelete('cascade');
            $table->unsignedBigInteger('usuario2_id');
            $table->foreign('usuario2_id')->references('id')->on('tb_usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_chatprivado');
    }
};
