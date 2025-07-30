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
    // tbUsuario  principal do site

    public function up()
    {
        Schema::create('tb_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('user');
            $table->string('apelido')->nullable();
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('cpf');
            $table->integer('genero'); //FK
            $table->date('data_nascimento');
            $table->string('imagem')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('endereco')->nullable();
            $table->string('rua')->nullable();
            $table->string('bairro')->nullable();
            $table->string('numero')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('complemento')->nullable();
            $table->integer('tipo_usuario'); //FK
            $table->string('status_conta');
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
