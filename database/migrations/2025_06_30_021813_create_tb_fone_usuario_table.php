<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tb_fone_usuario')) {
            Schema::create('tb_fone_usuario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')
                      ->constrained('tb_usuario')
                      ->onDelete('cascade');
                $table->string('numero_telefone');
                $table->enum('tipo_telefone', ['celular', 'residencial', 'comercial', 'whatsapp'])
                      ->default('celular');
                $table->boolean('is_principal')->default(false);
                $table->timestamps();
                
                // Índice para melhor performance
                $table->index(['usuario_id', 'is_principal']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tb_fone_usuario');
    }
};