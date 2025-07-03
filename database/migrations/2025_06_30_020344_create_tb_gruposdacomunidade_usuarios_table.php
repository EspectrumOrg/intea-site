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
        // tb muitos para muitos um usuario pode estar em muitos grupos e um grupo pode ter muitos usuarios
        Schema::create('tb_gruposdacomunidade_usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('tb_usuario');
            $table->unsignedBigInteger('idGruposComunidade');
            $table->foreign('idGruposComunidade')->references('id')->on('tb_gruposdacomunidade');
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
        Schema::dropIfExists('tb_gruposdacomunidade_usuarios');
    }
};
