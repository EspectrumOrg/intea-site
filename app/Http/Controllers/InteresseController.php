<?php

namespace App\Http\Controllers;

use App\Models\Interesse;
use App\Models\Postagem;
use App\Models\Tendencia;
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
    
    // Postagens com mais curtidas (em vez de recentes)
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
     * Salvar novo interesse
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
            // Criar slug único
            $slug = Str::slug($request->nome);
            $counter = 1;
            $originalSlug = $slug;
            
            while (Interesse::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

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

            // Criar interesse
            $interesse = Interesse::create([
                'nome' => $request->nome,
                'slug' => $slug,
                'descricao' => $request->descricao,
                'sobre' => $request->sobre,
                'icone' => $icone,
                'icone_custom' => $iconeCustomPath,
                'cor' => $request->cor,
                'contador_membros' => 1,
                'contador_postagens' => 0,
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
            ]);

            // O criador automaticamente segue o interesse
            Auth::user()->seguirInteresse($interesse->id, true);

            // Tornar o criador moderador
            $interesse->moderadores()->attach(Auth::id(), [
                'cargo' => 'fundador',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('interesses.show', $interesse->slug)
                ->with('success', 'Interesse criado com sucesso! Você é o moderador fundador.');

        } catch (\Exception $e) {
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
}