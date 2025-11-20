<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('palavras_proibidas_globais', function (Blueprint $table) {
            $table->id();
            $table->string('palavra');
            $table->enum('tipo', ['exata', 'parcial']);
            $table->text('motivo')->nullable();
            $table->foreignId('adicionado_por')->constrained('tb_usuario');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['palavra', 'ativo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('palavras_proibidas_globais');
    }
};