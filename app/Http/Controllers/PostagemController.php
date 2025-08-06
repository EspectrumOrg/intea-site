<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postagem;
use App\Models\ImagemPostagem;

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
        $postagens = $this->postagem->with(['imagens', 'usuario'])->get();

        return view('dashboard', compact('postagens'));
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
        $request->validate([
            'titulo_postagem' => 'required|string|max:255',
            'texto_postagem' => 'required|string|max:555',
            'caminho_imagem' => 'nullable|string|max:255',
        ], [
            'titulo_postagem.required' => 'O campo título é obrigatório',
            'texto_postagem.required' => 'O campo texto é obrigatório',
            'titulo_postagem.max' => 'O campo título só comporta até 255 caracteres',
            'texto_postagem.max' => 'O campo texto só comporta até 555 caracteres',
        ]);

        // Criar Postagem
        $postagem = Postagem::create([
            'titulo_postagem' => $request->titulo_postagem,
            'texto_postagem' => $request->texto_postagem,
        ]);

        // Criar Imagens Ligadas à imagem
        ImagemPostagem::create([
            'caminho_imagem' => $request->caminho_imagem,
            'id_postagem' => $postagem->id, // associa corretamente
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
