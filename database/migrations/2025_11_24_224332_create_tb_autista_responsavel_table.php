<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_autista_responsavel', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('autista_id');
            $table->unsignedBigInteger('responsavel_id');

            $table->foreign('autista_id')
                  ->references('id')->on('tb_autista')
                  ->onDelete('cascade');

            $table->foreign('responsavel_id')
                  ->references('id')->on('tb_responsavel')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_autista_responsavel');
    }
};
