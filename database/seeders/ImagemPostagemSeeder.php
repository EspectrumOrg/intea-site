<?php

namespace Database\Seeders;

use App\Models\ImagemPostagem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImagemPostagemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Imagem padrão
        ImagemPostagem::factory()->create([
            'id_postagem' => 1,
            'caminho_imagem' => 'arquivos/postagens/post-01-comunnity.jpg',
        ]);
    }
}
