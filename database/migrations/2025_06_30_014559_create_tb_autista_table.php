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
        // nesse  codigo um autista so pode ter 1 cuidador
        Schema::create('tb_autista', function (Blueprint $table) {
        $table->id();
        $table->string('cipteiaAutista');
        $table->string('rgAutista');
        $table->string('statusCipteiaAutista');
        $table->unsignedBigInteger('idusuario');
        $table->foreign('idusuario')->references('id')->on('tb_usuario');
        $table->unsignedBigInteger('idCuidador')->nullable();
        $table->foreign('idCuidador')->references('id')->on('tb_cuidador');
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
        Schema::dropIfExists('tb_autista');
    }
};
