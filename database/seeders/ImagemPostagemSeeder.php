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
            'caminho_imagem' => 'arquivos/postagens/postagem-01.jpg',
        ]);

        //mais 9 postagens
        ImagemPostagem::factory()->create([
            'id_postagem' => 2,
            'caminho_imagem' => 'arquivos/postagens/postagem-02.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 3,
            'caminho_imagem' => 'arquivos/postagens/postagem-03.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 4,
            'caminho_imagem' => 'arquivos/postagens/postagem-04.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 5,
            'caminho_imagem' => 'arquivos/postagens/postagem-05.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 6,
            'caminho_imagem' => 'arquivos/postagens/postagem-06.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 7,
            'caminho_imagem' => 'arquivos/postagens/postagem-07.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 8,
            'caminho_imagem' => 'arquivos/postagens/postagem-08.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 9,
            'caminho_imagem' => 'arquivos/postagens/postagem-09.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 10,
            'caminho_imagem' => 'arquivos/postagens/postagem-10.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 11,
            'caminho_imagem' => 'arquivos/postagens/postagem-11.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 12,
            'caminho_imagem' => 'arquivos/postagens/postagem-12.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 13,
            'caminho_imagem' => 'arquivos/postagens/postagem-13.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 14,
            'caminho_imagem' => 'arquivos/postagens/postagem-14.jpg',
        ]);

        ImagemPostagem::factory()->create([
            'id_postagem' => 15,
            'caminho_imagem' => 'arquivos/postagens/postagem-15.jpg',
        ]);

         $this->command->info('✅ Imagens para as postagens feitas!');
    }
}
