<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        /**
     * aqui eu estou criando o grupo e dps na tabela gruposcomunidade usuario estou relacionando 
     * um grupo a  muitos usuarios
    */  
    public function up()
    {
   
        Schema::create('tb_gruposdacomunidade', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idLider');
            $table->foreign('idLider')->references('id')->on('tb_usuario')->onDelete('cascade');            
            $table->string('nomeGrupo');
            $table->string('descGrupo');
            $table->string('imagemGrupo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_gruposdacomunidade');
    }
};
