<?php

namespace Database\Seeders;

use App\Models\Postagem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostagemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // criar postagem padrão
        $postagem_sobre = Postagem::factory()->create([
            'titulo_postagem' => 'Bem Vindo Ao Intea',
            'texto_postagem' => 'O site Intea foi criado com o intuido de criar um ambiente seguro para comunidade autista e providenciar uma forma de contato entre os membros. Além de facilitar as formas de acesso a profissionais de saúde diversos, sendo também uma ferramenta de acesso a coisas ai. Perdi a vontade de escrever',
            'usuario_id' => 1,
        ]);
    }
}
