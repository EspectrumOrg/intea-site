<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_autista', function (Blueprint $table) {

            // Remover FK primeiro
            if (Schema::hasColumn('tb_autista', 'responsavel_id')) {
                $table->dropForeign(['responsavel_id']);
                $table->dropColumn('responsavel_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tb_autista', function (Blueprint $table) {
            $table->unsignedBigInteger('responsavel_id')->nullable();
            $table->foreign('responsavel_id')->references('id')->on('tb_responsavel');
        });
    }
};
