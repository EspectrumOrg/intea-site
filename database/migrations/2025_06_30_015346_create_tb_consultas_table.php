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
        Schema::create('tb_consultas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('tb_profissional_saude');
            $table->foreign('usuario_id')->references('id')->on('tb_usuario');
            $table->dateTime('dataAgendamento');
            $table->dateTime('dataRealizacao');
            $table->string('status_consulta');
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
        Schema::dropIfExists('tb_consultas');
    }
};
