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
    public function up()
    {
        Schema::create('tb_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nomeUsuario');
            $table->string('emailUsuario');
            $table->string('senhaUsuario');
            $table->integer('cpfUsuario'); 
            $table->string('generoUsuario');
            $table->date('dataNascUsuario');
            $table->string('cepUsuario')->nullable();;
            $table->string('logradouroUsuario')->nullable();;
            $table->string('enderecoUsuario')->nullable();;
            $table->string('ruaUsuario')->nullable();;
            $table->string('bairroUsuario')->nullable();;
            $table->integer('numeroUsuario')->nullable();;
            $table->string('cidadeUsuario')->nullable();;
            $table->string('estadoUsuario',)->nullable();;
            $table->string('complementoUsuario')->nullable();;   
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
        Schema::dropIfExists('tb_usuario');
    }
};
