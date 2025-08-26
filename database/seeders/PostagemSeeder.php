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
            'texto_postagem' => 'Bem Vindo Ao Intea
            
            Aqui é um espaço virtual voltado para o apoio e comunicação de pessoas do espectro autista. Além familiares, profissionais de saúde voltados para a área psicologica e alísticos que possuem interesse em aprender mais sobre o espectro. Esse ambiente foi desenvolvido pela Espectrum, em colaboração com grupos como os Azuis da Leste.
            Ademais, o site também possibilita uma forma de facilitar o acesso a profissionais da área para pessoas do espectro ou responsáveis desses. Com uma área exclusiva para o contato e consulta com esses médicos',
            'usuario_id' => 1,
        ]);
    }
}
