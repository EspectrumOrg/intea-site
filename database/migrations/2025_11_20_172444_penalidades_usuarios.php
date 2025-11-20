<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penalidades_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('tb_usuario');
            $table->enum('tipo', ['sistema', 'interesse']);
            $table->foreignId('interesse_id')->nullable()->constrained('interesses');
            $table->text('motivo');
            $table->integer('peso');
            $table->foreignId('aplicado_por')->constrained('tb_usuario');
            $table->timestamp('expira_em')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
            
            $table->index(['usuario_id', 'ativa', 'tipo']);
            $table->index(['interesse_id', 'ativa']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('penalidades_usuarios');
    }
};