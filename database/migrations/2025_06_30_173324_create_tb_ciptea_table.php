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
        Schema::create('tb_ciptea', function (Blueprint $table) {
            $table->id();
            $table->string('nomeBeneficiario');
            $table->string('cpf', 14)->unique();
            $table->date('dataNascimento');
            $table->enum('genero', ['Masculino', 'Feminino', 'Outro']);
            $table->string('rg')->unique();
            $table->string('StatusCiptea');
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_ciptea');
    }
};
