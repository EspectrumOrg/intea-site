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
            $table->string('user');
            $table->string('apelido')->nullable();
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('cpf')->unique()->nullable();
            $table->unsignedBigInteger('genero');  
            $table->foreign('genero')->references('id')->on('tb_genero')->onDelete('cascade');
            $table->date('data_nascimento');
            $table->string('foto'); //foto perfil
            $table->string('descricao')->nullable(); //descrição perfil
            $table->integer('visibilidade')->default(1); // publica ou privada
            $table->integer('tipo_usuario'); //FK
            $table->string('status_conta');
            $table->rememberToken();
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
        Schema::dropIfExists('tb_fone_usuario');
        Schema::dropIfExists('tb_admin');
        Schema::dropIfExists('tb_autista');
        Schema::dropIfExists('tb_comunidade');
        Schema::dropIfExists('tb_profissional_saude');
        Schema::dropIfExists('tb_responsavel');
        Schema::dropIfExists('tb_postagem');
        Schema::dropIfExists('tb_comentario');
        Schema::dropIfExists('tb_curtida');
        Schema::dropIfExists('tb_denuncia');
        Schema::dropIfExists('tb_seguir');
        Schema::dropIfExists('tb_usuario');
        
    }
};
