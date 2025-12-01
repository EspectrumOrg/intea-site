<?php

namespace App\Http\Controllers;

use App\Models\Interesse;
use App\Models\Postagem;
use App\Models\Tendencia;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InteresseController extends Controller
{
    public function show($slug)
{
    $interesse = Interesse::where('slug', $slug)
        ->withCount(['seguidores', 'postagens'])
        ->firstOrFail();
    
    $usuario = Auth::user();
    
    // Postagens com mais curtidas
    $postagensMaisCurtidas = $interesse->postagens()
        ->with(['usuario', 'imagens', 'interesses'])
        ->withCount(['curtidas', 'comentarios'])
        ->where('bloqueada_auto', false)
        ->where('removida_manual', false)
        ->orderBy('curtidas_count', 'desc')
        ->limit(20)
        ->get();
    
    // Usuários populares
    $usuariosPopulares = $interesse->usuariosPopulares(6);
    
    // Postagens destacadas
    $postagensDestacadas = $interesse->postagensDestacadas(5);
    
    // Verificar se usuário segue o interesse
    $usuarioSegue = $usuario ? $interesse->usuarioSegue($usuario->id) : false;
    
    // Verificar se usuário é moderador
    $usuarioEhModerador = $usuario ? $interesse->moderadores()->where('usuario_id', $usuario->id)->exists() : false;
    
    // Verificar se usuário é dono
    $usuarioEhDono = $usuario ? $interesse->moderadores()->where('usuario_id', $usuario->id)->wherePivot('cargo', 'dono')->exists() : false;
    
    // Obter o dono do interesse
    $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
    
    // Estatísticas do interesse
    $estatisticas = $interesse->obterEstatisticas();
    
    // Tendencias populares para sidebar
    $tendenciasPopulares = Tendencia::populares(7)->get();

    return view('interesses.show', compact(
        'interesse', 
        'postagensMaisCurtidas', 
        'usuariosPopulares',
        'postagensDestacadas',
        'usuarioSegue',
        'usuarioEhModerador',
        'usuarioEhDono',
        'dono', // NOVO: Passando o dono para a view
        'estatisticas',
        'tendenciasPopulares'
    ));
}

    public function seguir(Request $request, $id)
    {
        $interesse = Interesse::findOrFail($id);
        $usuario = Auth::user();
        
        if (!$usuario->segueInteresse($interesse->id)) {
            $usuario->seguirInteresse($interesse->id, $request->boolean('notificacoes', true));
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Agora você segue ' . $interesse->nome,
                'dados' => [
                    'segue' => true,
                    'contador_membros' => $interesse->contador_membros
                ]
            ]);
        }
        
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Você já segue este interesse'
        ], 400);
    }

    public function deixarSeguir($id)
    {
        $interesse = Interesse::findOrFail($id);
        $usuario = Auth::user();
        
        if ($usuario->segueInteresse($interesse->id)) {
            $usuario->deixarSeguirInteresse($interesse->id);
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Você deixou de seguir ' . $interesse->nome,
                'dados' => [
                    'segue' => false,
                    'contador_membros' => $interesse->contador_membros
                ]
            ]);
        }
        
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Você não segue este interesse'
        ], 400);
    }

    public function index()
    {
        $interesses = Interesse::ativos()
            ->withCount('seguidores')
            ->orderBy('seguidores_count', 'desc')
            ->paginate(12)
            ->onEachSide(1)
            ->withQueryString();

        $usuario = Auth::user();
        $interessesUsuario = $usuario ? $usuario->interesses->pluck('id')->toArray() : [];
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.index', compact('interesses', 'interessesUsuario', 'tendenciasPopulares'));
    }

    public function postagens($slug, Request $request)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        
        $postagens = $interesse->postagensVisiveis()
                    ->with(['usuario', 'imagens', 'interesses'])
                    ->withCount(['curtidas', 'comentarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return response()->json([
            'sucesso' => true,
            'interesse' => $interesse,
            'postagens' => $postagens
        ]);
    }

    public function sugeridos()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuário não autenticado'
            ], 401);
        }
        
        $interessesSugeridos = $usuario->obterInteressesSugeridos(6);
        
        return response()->json([
            'sucesso' => true,
            'interesses' => $interessesSugeridos
        ]);
    }

    public function categorizarPostagem(Request $request, $interesseId)
    {
        $request->validate([
            'postagem_id' => 'required|exists:postagens,id',
            'observacao' => 'nullable|string|max:500'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $postagem = Postagem::findOrFail($request->postagem_id);
        
        if ($postagem->pertenceAoInteresse($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Postagem já está neste interesse'
            ], 400);
        }
        
        $postagem->categorizarInteresse(
            $interesse->id,
            'manual',
            Auth::id(),
            $request->observacao
        );
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Postagem categorizada com sucesso',
            'interesse' => $interesse
        ]);
    }

    /**
     * Mostrar formulário de criação de interesse
     */
    public function create()
    {
        $tendenciasPopulares = Tendencia::populares(7)->get();
        return view('interesses.create', compact('tendenciasPopulares'));
    }

    /**
     * Salvar novo interesse - CORRIGIDO
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:50|unique:interesses,nome',
            'descricao' => 'required|string|max:200',
            'sobre' => 'nullable|string|max:1000',
            'icone_type' => 'required|in:default,custom',
            'icone' => 'required_if:icone_type,default|string|max:50',
            'icone_custom' => 'required_if:icone_type,custom|image|mimes:jpeg,png,jpg,svg|max:1024',
            'cor' => 'required|string|size:7',
        ], [
            'nome.required' => 'O nome do interesse é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 50 caracteres',
            'nome.unique' => 'Já existe um interesse com este nome',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.max' => 'A descrição não pode ter mais de 200 caracteres',
            'icone.required_if' => 'Selecione um ícone padrão',
            'icone_custom.required_if' => 'Faça upload de um ícone customizado',
            'icone_custom.image' => 'O arquivo deve ser uma imagem',
            'icone_custom.mimes' => 'Formatos permitidos: JPEG, PNG, JPG, SVG',
            'icone_custom.max' => 'O ícone não pode ter mais de 1MB',
            'cor.required' => 'Selecione uma cor',
        ]);

        try {
            // Processar ícone
            $icone = null;
            $iconeCustomPath = null;
            
            if ($request->icone_type === 'default') {
                $icone = $request->icone;
            } else {
                // Salvar ícone customizado
                if ($request->hasFile('icone_custom')) {
                    $iconeCustomPath = $request->file('icone_custom')->store('arquivos/interesses/icones', 'public');
                    $icone = 'custom';
                }
            }

            // Usar o método criar do modelo Interesse
            $interesse = Interesse::criar([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'sobre' => $request->sobre,
                'icone' => $icone,
                'icone_custom' => $iconeCustomPath,
                'cor' => $request->cor,
            ]);

            // O criador automaticamente segue o interesse
            Auth::user()->seguirInteresse($interesse->id, true);

            // Tornar o criador moderador com cargo de DONO
            $interesse->moderadores()->attach(Auth::id(), [
                'cargo' => 'dono',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('interesses.show', $interesse->slug)
                ->with('success', 'Interesse criado com sucesso! Você é o moderador fundador.');

        } catch (\Exception $e) {
            // Log do erro para debug
            \Log::error('Erro ao criar interesse: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erro ao criar interesse: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Pesquisar interesses
     */
    public function pesquisar(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('interesses.index');
        }

        $interesses = Interesse::ativos()
            ->where(function($q) use ($query) {
                $q->where('nome', 'LIKE', "%{$query}%")
                  ->orWhere('descricao', 'LIKE', "%{$query}%")
                  ->orWhere('sobre', 'LIKE', "%{$query}%");
            })
            ->withCount('seguidores')
            ->orderBy('seguidores_count', 'desc')
            ->paginate(12);

        $usuario = Auth::user();
        $interessesUsuario = $usuario ? $usuario->interesses->pluck('id')->toArray() : [];
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.pesquisa', compact(
            'interesses', 
            'interessesUsuario', 
            'tendenciasPopulares',
            'query'
        ));
    }

    /**
     * Gerenciar Interesses do Usuário
     */
    public function gerenciar()
    {
        $usuario = Auth::user();
        $interessesGerenciáveis = $usuario->obterInteressesGerenciáveis();
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.gerenciar', compact(
            'interessesGerenciáveis',
            'tendenciasPopulares'
        ));
    }

    /**
     * Editar Interesse
     */
    public function edit($slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeEditarInteresse($interesse->id)) {
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('error', 'Você não tem permissão para editar este interesse.');
        }

        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.edit', compact(
            'interesse',
            'tendenciasPopulares'
        ));
    }

    /**
     * Atualizar Interesse
     */
    public function update(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeEditarInteresse($interesse->id)) {
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('error', 'Você não tem permissão para editar este interesse.');
        }

        $request->validate([
            'nome' => 'required|string|max:50|unique:interesses,nome,' . $interesse->id,
            'descricao' => 'required|string|max:200',
            'sobre' => 'nullable|string|max:1000',
            'icone_type' => 'required|in:default,custom',
            'icone' => 'required_if:icone_type,default|string|max:50',
            'icone_custom' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
            'cor' => 'required|string|size:7',
            'moderacao_ativa' => 'boolean',
        ]);

        try {
            $dados = [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'sobre' => $request->sobre,
                'cor' => $request->cor,
                'moderacao_ativa' => $request->boolean('moderacao_ativa', true),
            ];

            // Processar ícone
            if ($request->icone_type === 'default') {
                $dados['icone'] = $request->icone;
                $dados['icone_custom'] = null;
            } else {
                $dados['icone'] = 'custom';
                if ($request->hasFile('icone_custom')) {
                    $dados['icone_custom'] = $request->file('icone_custom')->store('arquivos/interesses/icones', 'public');
                }
            }

            // Atualizar slug se o nome mudou
            if ($interesse->nome !== $request->nome) {
                $dados['slug'] = Str::slug($request->nome);
            }

            $interesse->update($dados);

            return redirect()->route('interesses.show', $interesse->slug)
                ->with('success', 'Interesse atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar interesse: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Deletar Interesse
     */
    public function destroy($slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeDeletarInteresse($interesse->id)) {
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('error', 'Você não tem permissão para deletar este interesse.');
        }

        try {
            $usuario->deletarInteresse($interesse->id);

            return redirect()->route('interesses.index')
                ->with('success', 'Interesse deletado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('error', 'Erro ao deletar interesse: ' . $e->getMessage());
        }
    }

    /**
     * Remover Postagem do Interesse
     */
    public function removerPostagem(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeRemoverPostagem($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Você não tem permissão para remover postagens deste interesse.'
            ], 403);
        }

        $request->validate([
            'postagem_id' => 'required|exists:postagens,id',
            'motivo' => 'nullable|string|max:500'
        ]);

        try {
            $sucesso = $usuario->removerPostagemDoInteresse(
                $interesse->id,
                $request->postagem_id,
                $request->motivo
            );

            if ($sucesso) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Postagem removida do interesse com sucesso.'
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao remover postagem do interesse.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerenciar Moderadores
     */
    public function moderadores($slug)
    {
        $interesse = Interesse::where('slug', $slug)
            ->with(['moderadores' => function($query) {
                $query->select('tb_usuario.id', 'user', 'apelido', 'foto');
            }])
            ->firstOrFail();

        $usuario = Auth::user();

        if (!$usuario->podeAdicionarModerador($interesse->id)) {
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('error', 'Você não tem permissão para gerenciar moderadores.');
        }

        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.moderadores', compact(
            'interesse',
            'tendenciasPopulares'
        ));
    }

    /**
     * Adicionar Moderador
     */
    public function adicionarModerador(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeAdicionarModerador($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Você não tem permissão para adicionar moderadores.'
            ], 403);
        }

        $request->validate([
            'usuario_id' => 'required|exists:tb_usuario,id'
        ]);

        try {
            $sucesso = $usuario->adicionarModerador($interesse->id, $request->usuario_id);

            if ($sucesso) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Moderador adicionado com sucesso.'
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao adicionar moderador.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover Moderador
     */
    public function removerModerador(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeAdicionarModerador($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Você não tem permissão para remover moderadores.'
            ], 403);
        }

        $request->validate([
            'usuario_id' => 'required|exists:tb_usuario,id'
        ]);

        try {
            $sucesso = $usuario->removerModerador($interesse->id, $request->usuario_id);

            if ($sucesso) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Moderador removido com sucesso.'
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao remover moderador.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transferir Propriedade
     */
    public function transferirPropriedade(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->isDonoInteresse($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Apenas o dono atual pode transferir a propriedade.'
            ], 403);
        }

        $request->validate([
            'novo_dono_id' => 'required|exists:tb_usuario,id'
        ]);

        try {
            $sucesso = $usuario->transferirPropriedade($interesse->id, $request->novo_dono_id);

            if ($sucesso) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Propriedade transferida com sucesso.'
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao transferir propriedade.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pesquisar usuários para adicionar como moderador
     */
    public function pesquisarUsuarios(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeAdicionarModerador($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Sem permissão.'
            ], 403);
        }

        $query = $request->get('q');
        
        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        $usuarios = Usuario::where(function($q) use ($query) {
                $q->where('user', 'LIKE', "%{$query}%")
                  ->orWhere('apelido', 'LIKE', "%{$query}%")
                  ->orWhere('nome', 'LIKE', "%{$query}%");
            })
            ->where('id', '!=', $usuario->id) // Não incluir a si mesmo
            ->whereNotIn('id', $interesse->moderadores()->pluck('usuario_id')) // Não incluir moderadores existentes
            ->limit(10)
            ->get(['id', 'user', 'apelido', 'foto']);

        return response()->json($usuarios);
    }
}