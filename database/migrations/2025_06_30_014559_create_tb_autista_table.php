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
        // tbAutista

    public function up()
    {
        // nesse  codigo um autista so pode ter 1 cuidador
        Schema::create('tb_autista', function (Blueprint $table) {
        $table->id();
        $table->string('cipteia_autista');
        $table->string('rg_autista');
        $table->string('status_cipteia_autista');
        $table->unsignedBigInteger('usuario_id');
        $table->foreign('usuario_id')->references('id')->on('tb_usuario');
        $table->unsignedBigInteger('responsavel_id')->nullable();
        $table->foreign('responsavel_id')->references('id')->on('tb_responsavel');
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
