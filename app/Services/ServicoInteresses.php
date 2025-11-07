<?php

namespace App\Services;

use App\Models\{
    Interesse,
    Usuario,
    Postagem
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServicoInteresses
{
    /**
     * Processar e categorizar automaticamente uma postagem nos interesses
     */
    public function categorizarPostagemAutomaticamente(Postagem $postagem): array
    {
        $interessesAtribuidos = [];
        $interesses = Interesse::ativos()->get();
        
        foreach ($interesses as $interesse) {
            $relevancia = $this->calcularRelevanciaPostagem($postagem, $interesse);
            
            if ($relevancia >= 0.6) { // 60% de relevância mínima
                $postagem->interesses()->attach($interesse->id, [
                    'tipo' => 'automático',
                    'categorizado_por' => null,
                    'observacao' => "Relevância automática: " . round($relevancia * 100) . "%"
                ]);
                
                $interessesAtribuidos[] = [
                    'interesse' => $interesse,
                    'relevancia' => $relevancia
                ];
                
                $interesse->atualizarContadores();
            }
        }
        
        return $interessesAtribuidos;
    }

    /**
     * Calcular relevância de uma postagem para um interesse
     */
    public function calcularRelevanciaPostagem(Postagem $postagem, Interesse $interesse): float
    {
        $texto = Str::lower($postagem->texto_postagem);
        $palavrasChave = $interesse->obterPalavrasChave();
        
        $pontuacao = 0;
        $totalPalavrasChave = count($palavrasChave);
        
        if ($totalPalavrasChave === 0) {
            return 0;
        }
        
        foreach ($palavrasChave as $palavra) {
            $palavra = Str::lower($palavra);
            
            // Busca exata da palavra
            if (str_contains($texto, $palavra)) {
                $pontuacao += 1;
            }
            
            // Busca por similaridade (para palavras compostas)
            $similaridade = $this->calcularSimilaridadeTexto($texto, $palavra);
            if ($similaridade > 0.7) {
                $pontuacao += $similaridade;
            }
        }
        
        // Normalizar pontuação (0 a 1)
        $relevancia = $pontuacao / $totalPalavrasChave;
        
        // Bonus por hashtags relacionadas
        $relevancia += $this->calcularBonusHashtags($postagem, $interesse);
        
        return min($relevancia, 1.0); // Máximo 100%
    }

    /**
     * Calcular similaridade entre textos
     */
    private function calcularSimilaridadeTexto(string $texto, string $palavra): float
    {
        $palavrasTexto = str_word_count($texto, 1);
        $maxSimilaridade = 0;
        
        foreach ($palavrasTexto as $palavraTexto) {
            similar_text($palavra, $palavraTexto, $percentual);
            $maxSimilaridade = max($maxSimilaridade, $percentual / 100);
        }
        
        return $maxSimilaridade;
    }

    /**
     * Calcular bônus por hashtags relacionadas ao interesse
     */
    private function calcularBonusHashtags(Postagem $postagem, Interesse $interesse): float
    {
        $bonus = 0;
        $hashtags = $this->extrairHashtags($postagem->texto_postagem);
        
        $hashtagsInteresse = $this->obterHashtagsInteresse($interesse);
        
        foreach ($hashtags as $hashtag) {
            if (in_array($hashtag, $hashtagsInteresse)) {
                $bonus += 0.2; // 20% de bônus por hashtag relacionada
            }
        }
        
        return min($bonus, 0.4); // Máximo 40% de bônus
    }

    /**
     * Extrair hashtags do texto
     */
    private function extrairHashtags(string $texto): array
    {
        preg_match_all('/#(\w+)/', $texto, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Obter hashtags comuns para um interesse
     */
    private function obterHashtagsInteresse(Interesse $interesse): array
    {
        $hashtagsPorInteresse = [
            'tecnologia' => ['tech', 'programacao', 'coding', 'dev', 'software', 'ia', 'ai', 'robotica'],
            'esportes' => ['sports', 'futebol', 'bola', 'jogo', 'atleta', 'treino', 'competicao'],
            'musica' => ['music', 'banda', 'artista', 'show', 'festival', 'album', 'single'],
            'games' => ['gaming', 'game', 'player', 'console', 'steam', 'xbox', 'playstation'],
            'filmes-series' => ['cinema', 'filme', 'serie', 'netflix', 'streaming', 'ator'],
            'arte-design' => ['arte', 'design', 'criativo', 'pintura', 'fotografia', 'ilustracao'],
            'ciencia' => ['ciencia', 'pesquisa', 'experimento', 'laboratorio', 'descoberta'],
            'viagens' => ['viagem', 'turismo', 'destino', 'hotel', 'aventura', 'culturas'],
            'culinaria' => ['culinaria', 'comida', 'receita', 'restaurante', 'chef', 'gourmet'],
            'moda' => ['moda', 'estilo', 'roupa', 'beleza', 'tendencia', 'acessorio'],
            'negocios' => ['negocio', 'empresa', 'startup', 'investimento', 'empreendedor'],
            'saude' => ['saude', 'fitness', 'exercicio', 'academia', 'nutricao', 'bemestar']
        ];
        
        return $hashtagsPorInteresse[$interesse->slug] ?? [];
    }

    /**
     * Sugerir interesses para um usuário baseado em seu comportamento
     */
    public function sugerirInteressesUsuario(Usuario $usuario, int $limite = 6): array
    {
        $interessesSeguidos = $usuario->interesses()->pluck('interesses.id')->toArray();
        
        // Baseado nos interesses atuais
        $sugestoesPorSimilaridade = $this->sugerirPorInteressesSimilares($usuario, $interessesSeguidos, $limite);
        
        // Baseado nas postagens do usuário
        $sugestoesPorConteudo = $this->sugerirPorConteudoPostado($usuario, $interessesSeguidos, $limite);
        
        // Baseado na popularidade
        $sugestoesPopulares = $this->sugerirPopulares($interessesSeguidos, $limite);
        
        // Combinar e rankear sugestões
        $todasSugestoes = array_merge($sugestoesPorSimilaridade, $sugestoesPorConteudo, $sugestoesPopulares);
        
        // Agrupar por interesse e calcular score
        $sugestoesAgrupadas = [];
        foreach ($todasSugestoes as $sugestao) {
            $interesseId = $sugestao['interesse']->id;
            
            if (!isset($sugestoesAgrupadas[$interesseId])) {
                $sugestoesAgrupadas[$interesseId] = [
                    'interesse' => $sugestao['interesse'],
                    'score' => 0,
                    'motivos' => []
                ];
            }
            
            $sugestoesAgrupadas[$interesseId]['score'] += $sugestao['score'];
            $sugestoesAgrupadas[$interesseId]['motivos'][] = $sugestao['motivo'];
        }
        
        // Ordenar por score e limitar
        usort($sugestoesAgrupadas, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($sugestoesAgrupadas, 0, $limite);
    }

    /**
     * Sugerir interesses similares aos que o usuário já segue
     */
    private function sugerirPorInteressesSimilares(Usuario $usuario, array $interessesSeguidos, int $limite): array
    {
        if (empty($interessesSeguidos)) {
            return [];
        }
        
        $sugestoes = [];
        
        // Buscar usuários que seguem os mesmos interesses
        $usuariosSimilares = DB::table('interesse_usuario')
            ->whereIn('interesse_id', $interessesSeguidos)
            ->where('usuario_id', '!=', $usuario->id)
            ->groupBy('usuario_id')
            ->select('usuario_id', DB::raw('COUNT(*) as interesses_comuns'))
            ->orderBy('interesses_comuns', 'desc')
            ->limit(50)
            ->get();
        
        // Coletar interesses desses usuários
        $interessesSugeridos = [];
        foreach ($usuariosSimilares as $usuarioSimilar) {
            $interessesUsuario = DB::table('interesse_usuario')
                ->where('usuario_id', $usuarioSimilar->usuario_id)
                ->whereNotIn('interesse_id', $interessesSeguidos)
                ->pluck('interesse_id')
                ->toArray();
            
            foreach ($interessesUsuario as $interesseId) {
                if (!isset($interessesSugeridos[$interesseId])) {
                    $interessesSugeridos[$interesseId] = 0;
                }
                $interessesSugeridos[$interesseId] += $usuarioSimilar->interesses_comuns;
            }
        }
        
        // Converter para formato padronizado
        foreach ($interessesSugeridos as $interesseId => $score) {
            $interesse = Interesse::find($interesseId);
            if ($interesse && $interesse->ativo) {
                $sugestoes[] = [
                    'interesse' => $interesse,
                    'score' => $score / 10, // Normalizar score
                    'motivo' => 'Usuários com interesses similares seguem este interesse'
                ];
            }
        }
        
        return $sugestoes;
    }

    /**
     * Sugerir interesses baseado no conteúdo que o usuário posta
     */
    private function sugerirPorConteudoPostado(Usuario $usuario, array $interessesSeguidos, int $limite): array
    {
        $postagens = $usuario->postagens()
            ->with('interesses')
            ->limit(100)
            ->get();
        
        if ($postagens->isEmpty()) {
            return [];
        }
        
        $interessesRelevantes = [];
        
        foreach ($postagens as $postagem) {
            $interessesPostagem = Interesse::ativos()
                ->whereNotIn('id', $interessesSeguidos)
                ->get();
            
            foreach ($interessesPostagem as $interesse) {
                $relevancia = $this->calcularRelevanciaPostagem($postagem, $interesse);
                
                if ($relevancia > 0.5) {
                    if (!isset($interessesRelevantes[$interesse->id])) {
                        $interessesRelevantes[$interesse->id] = [
                            'interesse' => $interesse,
                            'score' => 0,
                            'postagens' => 0
                        ];
                    }
                    
                    $interessesRelevantes[$interesse->id]['score'] += $relevancia;
                    $interessesRelevantes[$interesse->id]['postagens']++;
                }
            }
        }
        
        // Converter para formato padronizado
        $sugestoes = [];
        foreach ($interessesRelevantes as $dados) {
            $scoreNormalizado = $dados['score'] / $dados['postagens'];
            $sugestoes[] = [
                'interesse' => $dados['interesse'],
                'score' => $scoreNormalizado,
                'motivo' => "Baseado no conteúdo das suas postagens ({$dados['postagens']} postagens relevantes)"
            ];
        }
        
        return $sugestoes;
    }

    /**
     * Sugerir interesses populares
     */
    private function sugerirPopulares(array $interessesSeguidos, int $limite): array
    {
        $interessesPopulares = Interesse::ativos()
            ->whereNotIn('id', $interessesSeguidos)
            ->orderBy('contador_membros', 'desc')
            ->limit($limite * 2)
            ->get();
        
        $sugestoes = [];
        $maxMembros = Interesse::max('contador_membros') ?: 1;
        
        foreach ($interessesPopulares as $interesse) {
            $score = $interesse->contador_membros / $maxMembros;
            
            $sugestoes[] = [
                'interesse' => $interesse,
                'score' => $score,
                'motivo' => "Interesse popular com {$interesse->contador_membros} seguidores"
            ];
        }
        
        return $sugestoes;
    }

    /**
     * Obter estatísticas de engajamento por interesse
     */
    public function obterEstatisticasInteresse(Interesse $interesse): array
    {
        $postagens = $interesse->postagens()
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->withCount(['curtidas', 'comentarios'])
            ->get();
        
        $totalCurtidas = $postagens->sum('curtidas_count');
        $totalComentarios = $postagens->sum('comentarios_count');
        $mediaEngajamento = $postagens->count() > 0 ? ($totalCurtidas + $totalComentarios) / $postagens->count() : 0;
        
        // Usuários mais ativos
        $usuariosAtivos = DB::table('interesse_usuario')
            ->join('tb_postagem', 'interesse_usuario.usuario_id', '=', 'tb_postagem.usuario_id')
            ->join('interesse_postagem', 'tb_postagem.id', '=', 'interesse_postagem.postagem_id')
            ->where('interesse_postagem.interesse_id', $interesse->id)
            ->where('tb_postagem.bloqueada_auto', false)
            ->where('tb_postagem.removida_manual', false)
            ->select('interesse_usuario.usuario_id', DB::raw('COUNT(tb_postagem.id) as total_postagens'))
            ->groupBy('interesse_usuario.usuario_id')
            ->orderBy('total_postagens', 'desc')
            ->limit(10)
            ->get();
        
        return [
            'total_postagens' => $postagens->count(),
            'total_curtidas' => $totalCurtidas,
            'total_comentarios' => $totalComentarios,
            'media_engajamento' => round($mediaEngajamento, 2),
            'taxa_engajamento' => $interesse->contador_membros > 0 ? 
                round(($totalCurtidas + $totalComentarios) / $interesse->contador_membros * 100, 2) : 0,
            'usuarios_ativos' => $usuariosAtivos
        ];
    }

    /**
     * Reclassificar postagens antigas (para usar em command)
     */
    public function reclassificarPostagensAntigas(int $limite = 100): array
    {
        $postagens = Postagem::whereDoesntHave('interesses')
            ->orWhereHas('interesses', function($query) {
                $query->where('tipo', 'sugerido');
            })
            ->limit($limite)
            ->get();
        
        $resultados = [
            'total_processadas' => 0,
            'interesses_atribuidos' => 0,
            'postagens_sem_interesse' => 0
        ];
        
        foreach ($postagens as $postagem) {
            $resultados['total_processadas']++;
            
            // Remover classificações antigas sugeridas
            $postagem->interesses()->wherePivot('tipo', 'sugerido')->detach();
            
            // Reclassificar
            $interessesAtribuidos = $this->categorizarPostagemAutomaticamente($postagem);
            
            if (count($interessesAtribuidos) > 0) {
                $resultados['interesses_atribuidos'] += count($interessesAtribuidos);
            } else {
                $resultados['postagens_sem_interesse']++;
            }
        }
        
        return $resultados;
    }

    /**
     * Migrar usuários antigos para o sistema de interesses
     */
    public function migrarUsuariosAntigos(int $limite = 50): array
    {
        $usuarios = Usuario::where('onboarding_concluido', false)
            ->where('created_at', '<', now()->subDays(7)) // Usuários com mais de 7 dias
            ->limit($limite)
            ->get();
        
        $resultados = [
            'total_processados' => 0,
            'onboarding_concluido' => 0,
            'interesses_atribuidos' => 0
        ];
        
        foreach ($usuarios as $usuario) {
            $resultados['total_processados']++;
            
            // Atribuir interesses baseado no comportamento
            $interessesPadrao = Interesse::ativos()
                ->destaques()
                ->limit(3)
                ->pluck('id')
                ->toArray();
            
            foreach ($interessesPadrao as $interesseId) {
                $usuario->seguirInteresse($interesseId, true);
                $resultados['interesses_atribuidos']++;
            }
            
            // Marcar onboarding como concluído
            $usuario->completarOnboarding();
            $resultados['onboarding_concluido']++;
        }
        
        return $resultados;
    }
}