<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postagem;
use App\Models\ImagemPostagem;
use Faker\Core\File;
use Illuminate\Support\Facades\Auth;

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
        $postagens = $this->postagem->with(['imagens', 'usuario'])->get();

        return view('dashboard', compact('postagens', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $postagens = $this->postagem->all();
        $imagem_postagem = $this->imagem_postagem->all();

        return view('dashboard.post.index', compact('imagem_postagem', 'postagens'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'texto_postagem' => 'required|string|max:755',
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

        // Criar Imagens Ligadas à imagem
        $imagem = null;
        if ($request->hasFile('caminho_imagem')) {
            $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');
        }
        ImagemPostagem::create([
            'caminho_imagem' => $imagem,
            'id_postagem' => $postagem->id,
        ]);

        return redirect()->route('dashboard')->with('Sucesso', 'Postado, confira já!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
