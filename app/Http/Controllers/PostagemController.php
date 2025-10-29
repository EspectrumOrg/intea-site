<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postagem;
use App\Models\ImagemPostagem;
use App\Models\Tendencia;
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

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

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

<<<<<<< HEAD

    $tendenciasPopulares = \App\Models\Tendencia::populares(7)->get();

=======
    $tendenciasPopulares = \App\Models\Tendencia::populares(7)->get();
>>>>>>> parte-admin

        return view('feed.post.index', compact('postagens', 'posts', 'tendenciasPopulares'));
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
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'texto_postagem' => 'required|string|max:1000',
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif|max:4096',
        ], [
            'texto_postagem.required' => 'O campo texto é obrigatório',
            'texto_postagem.max' => 'O campo texto só comporta até 1000 caracteres',
        ]);

        // Criar Postagem
        $postagem = Postagem::create([
            'usuario_id' => $user->id,
            'texto_postagem' => $request->texto_postagem,
        ]);

        // Processar hashtags e vincular tendências
        $postagem->processarHashtags($request->texto_postagem);

        // Criar Imagens Ligadas à postagem
        if ($request->hasFile('caminho_imagem')) {
            $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');

            ImagemPostagem::create([
                'caminho_imagem' => $imagem,
                'id_postagem' => $postagem->id,
            ]);
        }
        return redirect()->route('post.index')->with('success', 'Postado, confira já!');
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

        $tendenciasPopulares = \App\Models\Tendencia::populares(5)->get();

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

        return redirect()->route('post.index')->with('success', 'Postagem atualizada com êxito!');
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

        // Deletar tendências sem postagens 🔥
        foreach ($tendencias as $tendencia) {
            if ($tendencia->postagens()->count() === 0) {
                $tendencia->delete();
            }
        }

        return redirect()->route('post.index')->with('success', 'Postagem exclúida com êxito!');
    }
}
