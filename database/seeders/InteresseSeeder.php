<?php

namespace Database\Seeders;

use App\Models\Interesse;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteresseSeeder extends Seeder
{
    public function run()
    {
        // Primeiro, encontrar o administrador (id 1)
        $admin = Usuario::find(1);
        
        if (!$admin) {
            // Criar admin se não existir
            $admin = Usuario::create([
                'nome' => 'Administrador Sistema',
                'user' => 'admin',
                'email' => 'admin@intea.com',
                'senha' => bcrypt('admin123'),
                'tipo_usuario' => 1,
                'ativo' => true,
                'email_verificado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->command->info('✅ Admin criado automaticamente!');
        }

        $interesses = [
            [
                'nome' => 'Tecnologia & Inovação',
                'slug' => 'tecnologia',
                'icone' => 'smartphone',
                'cor' => '#3B82F6',
                'descricao' => 'Descubra as últimas inovações tech',
                'sobre' => 'Um espaço dedicado à tecnologia, programação, gadgets e inovações. Compartilhe descobertas, projetos e discussões sobre o futuro digital.',
                'destaque' => true,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Esportes & Atividades',
                'slug' => 'esportes', 
                'icone' => 'sports_soccer',
                'cor' => '#EF4444',
                'descricao' => 'Do futebol aos e-sports',
                'sobre' => 'Para amantes de esportes! Futebol, basquete, e-sports, corrida e muito mais. Compartilhe jogos, resultados e paixões esportivas.',
                'destaque' => true,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Música & Sons',
                'slug' => 'musica',
                'icone' => 'music_note',
                'cor' => '#8B5CF6',
                'descricao' => 'Todos os ritmos e artistas',
                'sobre' => 'Um feed musical completo! Descubra novos artistas, compartilhe playlists, discuta gêneros e viva a música em comunidade.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Games & Jogos',
                'slug' => 'games',
                'icone' => 'sports_esports',
                'cor' => '#10B981',
                'descricao' => 'Mundo dos video games',
                'sobre' => 'Para gamers de todos os tipos! Notícias, reviews, dicas e comunidades sobre jogos, consoles e a cultura gamer.',
                'destaque' => true,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Cinema & Séries',
                'slug' => 'filmes-series',
                'icone' => 'movie',
                'cor' => '#F59E0B',
                'descricao' => 'Mundo do entretenimento',
                'sobre' => 'Sua central de entretenimento! Críticas, teorias, lançamentos e discussões sobre filmes, séries e streaming.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Arte & Criatividade',
                'slug' => 'arte-design',
                'icone' => 'palette',
                'cor' => '#EC4899',
                'descricao' => 'Expressão artística e design',
                'sobre' => 'Um espaço criativo para artistas e apreciadores. Compartilhe obras, técnicas, inspirações e explore a criatividade coletiva.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Ciência & Conhecimento',
                'slug' => 'ciencia',
                'icone' => 'science',
                'cor' => '#06B6D4',
                'descricao' => 'Descobertas e curiosidades',
                'sobre' => 'Explore o universo da ciência! Descobertas, pesquisas, curiosidades e discussões sobre o mundo científico.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Viagens & Culturas',
                'slug' => 'viagens',
                'icone' => 'travel_explore',
                'cor' => '#84CC16',
                'descricao' => 'Descubra novos destinos',
                'sobre' => 'Para viajantes e exploradores! Compartilhe experiências, dicas de destinos, culturas e inspire novas aventuras.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Gastronomia & Receitas',
                'slug' => 'culinaria',
                'icone' => 'restaurant',
                'cor' => '#F97316',
                'descricao' => 'Arte da culinária',
                'sobre' => 'Um banquete de sabores! Receitas, técnicas culinárias, restaurantes e tudo sobre o mundo gastronômico.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Moda & Estilo',
                'slug' => 'moda',
                'icone' => 'checkroom',
                'cor' => '#8B5CF6',
                'descricao' => 'Tendências e estilo pessoal',
                'sobre' => 'Seu feed de moda! Tendências, estilo pessoal, dicas de looks e inspirações do universo fashion.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Negócios & Empreendedorismo',
                'slug' => 'negocios',
                'icone' => 'business',
                'cor' => '#6366F1',
                'descricao' => 'Mundo dos negócios',
                'sobre' => 'Networking e crescimento profissional! Estratégias, startups, investimentos e discussões sobre o mundo empresarial.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Saúde & Bem-estar',
                'slug' => 'saude',
                'icone' => 'favorite',
                'cor' => '#DC2626',
                'descricao' => 'Cuidados e qualidade de vida',
                'sobre' => 'Sua comunidade de bem-estar! Dicas de saúde, exercícios, nutrição e qualidade de vida em um só lugar.',
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($interesses as $interesseData) {
            // Criar o interesse
            $interesse = Interesse::create($interesseData);
            
            // Fazer o admin seguir o interesse
            $admin->seguirInteresse($interesse->id, true);
            
            // Tornar o admin moderador DONO do interesse
            $interesse->moderadores()->attach($admin->id, [
                'cargo' => 'dono',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Incrementar contador manualmente
            $interesse->increment('contador_membros');
            
            $this->command->info("✅ Interesse '{$interesse->nome}' criado com admin como dono");
        }

        $this->command->info('🎉 Todos os 12 interesses criados com sucesso! Admin é dono de todos.');
    }
}