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
            $table->unsignedBigInteger('idPsicologo');
            $table->foreign('idPsicologo')->references('id')->on('tb_profsaude');
            $table->unsignedBigInteger('idAutista');
            $table->foreign('idAutista')->references('id')->on('tb_autista');
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
