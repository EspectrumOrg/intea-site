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
            $table->string('cpf')->unique();
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
            $table->string('foto')->nullable(); //foto perfil
            $table->string('descricao')->nullable(); //descrição perfil
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
        Schema::dropIfExists('tb_fone_usuario');
        Schema::dropIfExists('tb_admin');
        Schema::dropIfExists('tb_autista');
        Schema::dropIfExists('tb_comunidade');
        Schema::dropIfExists('tb_profissional_saude');
        Schema::dropIfExists('tb_responsavel');
        Schema::dropIfExists('tb_postagem');
        Schema::dropIfExists('tb_comentario_postagem');
        Schema::dropIfExists('tb_curtida_postagem');
        Schema::dropIfExists('tb_denuncia_postagem');
<<<<<<< HEAD
        Schema::dropIfExists('tb_follows');
=======
        Schema::dropIfExists('follows');
>>>>>>> 47cbf5876a81f62f6e7cf69fb0fd3707a32b815e
        Schema::dropIfExists('tb_usuario');
        
    }
};
