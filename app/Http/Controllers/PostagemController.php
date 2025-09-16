<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postagem;
use App\Models\ImagemPostagem;
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
        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();
        $postagens = $this->postagem->with(['imagens', 'usuario'])->OrderByDesc('created_at')->get();

        return view('feed', compact('postagens', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $postagens = $this->postagem->all();
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
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif|max:2048',
        ], [
            'texto_postagem.required' => 'O campo texto é obrigatório',
            'texto_postagem.max' => 'O campo texto só comporta até 755 caracteres',
        ]);

        // Criar Postagem
        $postagem = Postagem::create([
            'usuario_id' => $user->id,
            'texto_postagem' => $request->texto_postagem,
        ]);

        // Criar Imagens Ligadas à postagem
        if ($request->hasFile('caminho_imagem')) {
            $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');

            ImagemPostagem::create([
                'caminho_imagem' => $imagem,
                'id_postagem' => $postagem->id,
            ]);
        }
        return redirect()->route('post.index')->with('Sucesso', 'Postado, confira já!');
    }

    /**
     * Display the specified resource.
     */
    public function show($postagem)
    {
        $postagem = Postagem::withCount('curtidas')
            ->with(['comentarios.usuario', 'comentarios.image'])
            ->findOrFail($postagem);

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        return view('feed.post.read', compact('postagem', 'posts'));
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
            'texto_postagem' => 'required|string|max:755',
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif|max:2048',
        ]);

        $post->update([
            'texto_postagem' => $request->texto_postagem,
        ]);

        if ($request->hasFile('caminho_imagem')) {
            $arquivo = $request->file('caminho_imagem');
            $caminho = $arquivo->store('arquivos/postagens', 'public');

            $imagemPrincipal = $post->imagens()->first();

            if ($imagemPrincipal) {
                // Apaga imagem antiga se existir
                if ($imagemPrincipal->caminho_imagem && Storage::disk('public')->exists($imagemPrincipal->caminho_imagem)) {
                    Storage::disk('public')->delete($imagemPrincipal->caminho_imagem);
                }
                $imagemPrincipal->caminho_imagem = $caminho;
                $imagemPrincipal->save();
            } else {
                $post->imagens()->create(['caminho_imagem' => $caminho]);
            }
        }


        return redirect()->route('post.index')->with('Sucesso', 'Postagem atualizada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $postagem = Postagem::findOrFail($id);
        $postagem->delete();

        session()->flash("successo", "Postagem exclúido");
        return redirect()->back();
    }
}
