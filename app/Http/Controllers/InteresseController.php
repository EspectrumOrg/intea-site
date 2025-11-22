<?php

namespace App\Http\Controllers;

use App\Models\Interesse;
use App\Models\Postagem;
use App\Models\Tendencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class InteresseController extends Controller
{
    public function show($slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        $postagens = $interesse->postagensRecentes(20);
        $usuariosPopulares = $interesse->usuariosPopulares(6);
        $postagensDestacadas = $interesse->postagensDestacadas(5);
        $usuarioSegue = $usuario ? $interesse->usuarioSegue($usuario->id) : false;

        $tendenciasPopulares = Tendencia::populares(7)->get();
        
        return view('interesses.show', compact(
            'interesse', 
            'postagens', 
            'usuariosPopulares',
            'postagensDestacadas',
            'usuarioSegue',
            'tendenciasPopulares',
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
        ->onEachSide(1) // Mostra apenas 1 página antes e depois da atual
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
            'icone' => 'required|string|max:50',
            'cor' => 'required|string|size:7', // #FFFFFF
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nome.required' => 'O nome do interesse é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 50 caracteres',
            'nome.unique' => 'Já existe um interesse com este nome',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.max' => 'A descrição não pode ter mais de 200 caracteres',
            'icone.required' => 'Selecione um ícone',
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

            // Processar banner se enviado
            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('arquivos/interesses/banners', 'public');
            }

            // Criar interesse
            $interesse = Interesse::create([
                'nome' => $request->nome,
                'slug' => $slug,
                'descricao' => $request->descricao,
                'sobre' => $request->sobre,
                'icone' => $request->icone,
                'cor' => $request->cor,
                'banner' => $bannerPath,
                'contador_membros' => 1, // O criador é o primeiro membro
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
