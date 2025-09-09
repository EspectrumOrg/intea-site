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
            'texto_postagem' => 'Bem Vindo Ao Intea Aqui é um espaço virtual voltado para o apoio e comunicação de pessoas do espectro autista e a comunidade em geral',
            'usuario_id' => 1,
        ]);

        // mais 14 postagens
        Postagem::factory()->create([
            'usuario_id' => 2,
            'texto_postagem' => 'Hoje levamos meu filho ao parque e ele adorou o balanço! Pequenas vitórias que fazem a diferença.',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 3,
            'texto_postagem' => 'Alguém recomenda atividades sensoriais simples para fazer em casa?',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 4,
            'texto_postagem' => 'Sou psicólogo especializado em TEA e estou aberto para dúvidas sobre desenvolvimento infantil.',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 5,
            'texto_postagem' => 'Descobri recentemente o diagnóstico do meu sobrinho e quero aprender mais para apoiá-lo melhor.',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 6,
            'texto_postagem' => 'Recomendo um documentário incrível sobre inclusão escolar. Vale a pena assistir!',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 7,
            'texto_postagem' => 'Hoje testamos jogos educativos no tablet e foi um sucesso. O aprendizado pode ser divertido!',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 8,
            'texto_postagem' => 'Como vocês lidam com situações de sobrecarga sensorial em lugares barulhentos?',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 9,
            'texto_postagem' => 'Orgulho do meu irmão, que hoje conseguiu pedir comida sozinho no restaurante! 💙',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 10,
            'texto_postagem' => 'Acabei de ler um artigo científico sobre novas abordagens terapêuticas no autismo. Fascinante!',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 11,
            'texto_postagem' => 'Estamos planejando um encontro presencial do grupo da região Leste. Quem anima participar?',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 12,
            'texto_postagem' => 'Fizemos uma oficina de culinária adaptada e todos amaram preparar biscoitos juntos!',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 13,
            'texto_postagem' => 'Meu filho começou a usar comunicação alternativa com cartões visuais. Um passo importante!',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 14,
            'texto_postagem' => 'Gostaria de saber quais aplicativos vocês usam para apoiar a rotina diária.',
        ]);

        Postagem::factory()->create([
            'usuario_id' => 15,
            'texto_postagem' => 'Estou muito feliz em ver tantos avanços na aceitação e inclusão social nos últimos anos.',
        ]);
    }
}
