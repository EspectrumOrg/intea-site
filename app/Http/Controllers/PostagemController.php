<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postagem;
use App\Models\ImagemPostagem;
use App\Models\Tendencia;
use App\Models\Interesse;
use Faker\Core\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostagemController extends Controller
{
    private $postagem;
    private $imagem_postagem;

    public function __construct(Postagem $postagem, ImagemPostagem $imagem_postagem)
    {
        $this->postagem = $postagem;
        $this->imagem_postagem = $imagem_postagem;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Verificar se precisa fazer onboarding
        if (!Auth::user()->onboardingConcluido()) {
            return redirect()->route('onboarding');
        }

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // Feed principal - todas as postagens visíveis
        $postagens = $this->postagem
            ->with(['imagens', 'usuario', 'interesses'])
            ->whereHas('usuario', function ($q) use ($userId) {
                $q->where('visibilidade', 1)
                    ->orWhere('id', $userId)
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->where('visibilidade', 0)
                            ->whereHas('seguidores', function ($q3) use ($userId) {
                                $q3->where('segue_id', $userId);
                            });
                    });
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderByDesc('created_at')
            ->paginate(20);

        $tendenciasPopulares = Tendencia::populares(7)->get();
        $interessesUsuario = Auth::user()->interesses;

        return view('feed.post.index', compact('postagens', 'posts', 'tendenciasPopulares', 'interessesUsuario'));
    }

    public function seguindo()
    {
        $userId = Auth::id();

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        $postagens = $this->postagem
            ->with(['imagens', 'usuario', 'interesses'])
            ->whereHas('usuario', function ($q) use ($userId) {
                $q->whereHas('seguidores', function ($q2) use ($userId) {
                    $q2->where('segue_id', $userId);
                })->orWhere('id', $userId);
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderByDesc('created_at')
            ->paginate(20);

        $tendenciasPopulares = Tendencia::populares(7)->get();
        $interessesUsuario = Auth::user()->interesses;

        return view('feed.post.index', compact('postagens', 'posts', 'tendenciasPopulares', 'interessesUsuario'));
    }

    /**
     * Feed personalizado por interesses
     */
/**
 * Feed personalizado por interesses
 */
/**
 * Feed personalizado por interesses
 */
public function personalizado()
    {
        $userId = Auth::id();
        
        if (Auth::user()->interesses()->count() === 0) {
            return redirect()->route('post.index')
                           ->with('info', 'Siga alguns interesses para ter um feed personalizado');
        }

        $interessesIds = Auth::user()->interesses()->pluck('interesses.id');

        // Postagens populares para sidebar
        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // Feed personalizado
        $postagens = $this->postagem
            ->with(['imagens', 'usuario', 'interesses'])
            ->whereHas('interesses', function($query) use ($interessesIds) {
                $query->whereIn('interesses.id', $interessesIds);
            })
            ->where(function ($q) use ($userId) {
                $q->whereHas('usuario', function ($q2) use ($userId) {
                    $q2->where('visibilidade', 1)
                        ->orWhere('id', $userId)
                        ->orWhere(function ($q3) use ($userId) {
                            $q3->where('visibilidade', 0)
                                ->whereHas('seguidores', function ($q4) use ($userId) {
                                    $q4->where('segue_id', $userId);
                                });
                        });
                });
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderByDesc('created_at')
            ->paginate(20);

        $tendenciasPopulares = Tendencia::populares(7)->get();
        $interessesUsuario = Auth::user()->interesses;

        return view('feed.post.personalizado', compact('postagens', 'posts', 'tendenciasPopulares', 'interessesUsuario'));
    }

    /**
     * Feed por Interesse Específico
     */

     public function porInteresse($slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $userId = Auth::id();

        // Postagens populares para sidebar
        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // Feed por interesse
        $postagens = $this->postagem
            ->with(['imagens', 'usuario', 'interesses'])
            ->whereHas('interesses', function($query) use ($interesse) {
                $query->where('interesses.id', $interesse->id);
            })
            ->where(function ($q) use ($userId) {
                $q->whereHas('usuario', function ($q2) use ($userId) {
                    $q2->where('visibilidade', 1)
                        ->orWhere('id', $userId)
                        ->orWhere(function ($q3) use ($userId) {
                            $q3->where('visibilidade', 0)
                                ->whereHas('seguidores', function ($q4) use ($userId) {
                                    $q4->where('segue_id', $userId);
                                });
                        });
                });
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderByDesc('created_at')
            ->paginate(20);

        $tendenciasPopulares = Tendencia::populares(7)->get();
        $interessesUsuario = Auth::user()->interesses;

        return view('feed.post.interesse', compact('postagens', 'posts', 'tendenciasPopulares', 'interessesUsuario', 'interesse'));
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id();

        $postagens = $this->postagem
            ->with(['imagens', 'usuario'])
            ->whereHas('usuario', function ($q) use ($userId) {
                $q->where('visibilidade', 1)
                    ->orWhere('id', $userId)
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->where('visibilidade', 0)
                            ->whereHas('seguidores', function ($q3) use ($userId) {
                                $q3->where('segue_id', $userId);
                            });
                    });
            })
            ->orderByDesc('created_at')
            ->get();
        $imagem_postagem = $this->imagem_postagem->all();

        return view('feed.post.index', compact('imagem_postagem', 'postagens'));

    /**
     * Store a newly created resource in storage.
     */
        }
    public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'texto_postagem' => 'required|string|max:1000',
        'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif|max:4096',
        'interesse_id' => 'nullable|exists:interesses,id',
        'interesse_slug' => 'nullable|string',
    ]);

    // Criar Postagem
    $postagem = Postagem::create([
        'usuario_id' => $user->id,
        'texto_postagem' => $request->texto_postagem,
    ]);

    // Processar hashtags
    $postagem->processarHashtags($request->texto_postagem);

    // Vincular ao interesse específico
    $interesseId = $this->obterInteresseId($request);
    if ($interesseId) {
        $postagem->interesses()->attach($interesseId, [
            'tipo' => 'manual',
            'categorizado_por' => $user->id,
            'observacao' => 'Postagem direta no interesse'
        ]);
        
        // Atualizar contador do interesse
        $interesse = Interesse::find($interesseId);
        $interesse->atualizarContadores();
    }

    // Criar Imagens
    if ($request->hasFile('caminho_imagem')) {
        $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');
        ImagemPostagem::create([
            'caminho_imagem' => $imagem,
            'id_postagem' => $postagem->id,
        ]);
    }

    return redirect()->back()->with('success', 'Postagem criada com sucesso!');
}

/**
 * Obtém o ID do interesse baseado na requisição
 */
private function obterInteresseId(Request $request)
{
    // Prioridade 1: ID do interesse selecionado
    if ($request->filled('interesse_id')) {
        return $request->interesse_id;
    }
    
    // Prioridade 2: Slug do interesse da URL
    if ($request->filled('interesse_slug')) {
        $interesse = Interesse::where('slug', $request->interesse_slug)->first();
        return $interesse ? $interesse->id : null;
    }
    
    return null;
}

    /**
     * Display the specified resource.
     */
    public function show($postagem)
    {
        $postagem = Postagem::withCount('curtidas')
            ->with(['comentarios.usuario', 'comentarios.image'])
            ->findOrFail($postagem);

        // Não mais usado 19/10 (pode excluir)
        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        $tendenciasPopulares = Tendencia::populares(5)->get();

        return view('feed.post.read', compact('postagem', 'posts', 'tendenciasPopulares'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Postagem $postagem)
    {
        return view('post.edit', compact('postagem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Postagem $post)
    {
        $request->validate([
            'texto_postagem' => 'required|string|max:1000',
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif,jpeg|max:4096',
            'remover_imagem' => 'nullable|boolean', // <- campo hidden opcional para remoção
        ]);

        // Atualiza texto
        $post->update([
            'texto_postagem' => $request->texto_postagem,
        ]);

        // Reprocessa hashtags (com ID diferenciado)
        $post->tendencias()->detach();
        $post->processarHashtags($request->texto_postagem, $post->id); // <- passa o ID pra gerar tags únicas

        $imagemPrincipal = $post->imagens()->first();

        // Caso o usuário tenha clicado em "X" para remover a imagem
        if ($request->boolean('remover_imagem')) {
            if ($imagemPrincipal) {
                if (Storage::disk('public')->exists($imagemPrincipal->caminho_imagem)) {
                    Storage::disk('public')->delete($imagemPrincipal->caminho_imagem);
                }
                $imagemPrincipal->delete();
            }
        }

        // Caso o usuário tenha enviado uma nova imagem
        elseif ($request->hasFile('caminho_imagem')) {
            $arquivo = $request->file('caminho_imagem');
            $caminho = $arquivo->store('arquivos/postagens', 'public');

            if ($imagemPrincipal) {
                if (Storage::disk('public')->exists($imagemPrincipal->caminho_imagem)) {
                    Storage::disk('public')->delete($imagemPrincipal->caminho_imagem);
                }
                $imagemPrincipal->update(['caminho_imagem' => $caminho]);
            } else {
                $post->imagens()->create(['caminho_imagem' => $caminho]);
            }
        }

        return redirect()->back()->with('success', 'Postagem atualizada com êxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $postagem = Postagem::findOrFail($id);

        // guardar tendências
        $tendencias = $postagem->tendencias()->get();

        // Remover associações com tendências antes de deletar
        $postagem->tendencias()->detach();

        // Deletar postagem
        $postagem->delete();

        // Deletar tendências
        foreach ($tendencias as $tendencia) {
            if ($tendencia->postagens()->count() === 0) {
                $tendencia->delete();
            }
        }

        return redirect()->route('post.index')->with('success', 'Postagem excluída com êxito!');
    }
}