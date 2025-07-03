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
        // criacao da tb grupo
        Schema::create('tb_gruposdacomunidade', function (Blueprint $table) {
            $table->id();
            $table->string('nomeGrupo');
            $table->string('descGrupo');
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
