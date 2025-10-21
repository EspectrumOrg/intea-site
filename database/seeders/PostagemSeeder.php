<?php

namespace Database\Seeders;

use App\Models\Postagem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostagemSeeder extends Seeder
{
    public function run(): void
    {
        // === 1️⃣ Postagem do Admin ===
        $postagemAdmin = Postagem::factory()->create([
            'usuario_id' => 1,
            'texto_postagem' => 'Bem Vindo Ao Intea! Aqui é um espaço virtual voltado para o apoio e comunicação de pessoas do espectro autista e a comunidade em geral. #BoasVindas #Inclusao #Autismo',
        ]);

        // Processar hashtags
        $postagemAdmin->processarHashtags($postagemAdmin->texto_postagem);

        // Todos os 14 usuários curtem a postagem do admin
        $usuarios = range(2, 15);
        foreach ($usuarios as $usuarioId) {
            DB::table('tb_curtida')->insert([
                'id_postagem' => $postagemAdmin->id,
                'id_usuario' => $usuarioId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // === 2️⃣ Outras 14 postagens ===
        $postagensDados = [
            ['usuario_id' => 2, 'texto_postagem' => 'Hoje levamos meu filho ao parque e ele adorou! Pequenas vitórias que fazem a diferença. #Alegria #Parque'],
            ['usuario_id' => 3, 'texto_postagem' => 'Alguém recomenda atividades sensoriais simples para fazer em casa? #Atividades #TEA'],
            ['usuario_id' => 4, 'texto_postagem' => 'Sou psicólogo especializado em TEA e estou aberto para dúvidas sobre desenvolvimento infantil. #Psicologia #Inclusao'],
            ['usuario_id' => 5, 'texto_postagem' => 'Descobri recentemente o diagnóstico do meu sobrinho e quero aprender mais para apoiá-lo melhor. #Inclusao #Autismo'],
            ['usuario_id' => 6, 'texto_postagem' => 'Recomendo um documentário incrível sobre inclusão escolar. Vale a pena assistir! #Aprendizado #Inclusao'],
            ['usuario_id' => 7, 'texto_postagem' => 'Hoje testamos jogos educativos no tablet e foi um sucesso. O aprendizado pode ser divertido! #Jogos #Aprendizado'],
            ['usuario_id' => 8, 'texto_postagem' => 'Como vocês lidam com situações de sobrecarga sensorial em lugares barulhentos? #Sensibilidade #Dicas'],
            ['usuario_id' => 9, 'texto_postagem' => 'Orgulho do meu irmão, que hoje conseguiu pedir comida sozinho no restaurante! 💙 #Autonomia #Conquista'],
            ['usuario_id' => 10, 'texto_postagem' => 'Acabei de ler um artigo científico sobre novas abordagens terapêuticas no autismo. Fascinante! #Aprendizado #Autismo'],
            ['usuario_id' => 11, 'texto_postagem' => 'Estamos planejando um encontro presencial do grupo da região Leste. Quem anima participar? #Comunidade #Inclusao #Evento'],
            ['usuario_id' => 12, 'texto_postagem' => 'Fizemos uma oficina de culinária adaptada e todos amaram preparar biscoitos juntos! #Aprendizado #Inclusao'],
            ['usuario_id' => 13, 'texto_postagem' => 'Meu filho começou a usar comunicação alternativa com cartões visuais. Um passo importante! #Inclusao #TEA'],
            ['usuario_id' => 14, 'texto_postagem' => 'Gostaria de saber quais aplicativos vocês usam para apoiar a rotina diária. #Tecnologia #Rotina'],
            ['usuario_id' => 15, 'texto_postagem' => 'Estou muito feliz em ver tantos avanços na aceitação e inclusão social nos últimos anos. #Inclusao #Felicidade'],
        ];

        foreach ($postagensDados as $dados) {
            $postagem = Postagem::factory()->create($dados);

            // Processar hashtags
            $postagem->processarHashtags($dados['texto_postagem']);

            // Curtidas aleatórias entre 5 e 11 usuários
            $curtidores = collect($usuarios)
                ->shuffle()
                ->take(rand(5, 11))
                ->toArray();

            foreach ($curtidores as $usuarioId) {
                DB::table('tb_curtida')->insert([
                    'id_postagem' => $postagem->id,
                    'id_usuario' => $usuarioId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ 15 postagens criadas com hashtags e curtidas!');
    }
}
