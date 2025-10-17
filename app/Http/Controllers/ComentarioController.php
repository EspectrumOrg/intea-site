<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\Comentario;
use App\Models\ImagemComentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request, $tipo, $id)
    {
        $request->validate(
            [
                'comentario' => 'required|string|max:500',
                'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif',
            ],
            [
                'comentario.required' => 'É necessário escrever alguma coisa',
            ]
        );

        // salvar dados gerais
        $dados = [
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
        ];

        // salvar como post coment ou post reply
        if ($tipo === 'postagem') {
            $dados['id_postagem'] = $id;
        } elseif ($tipo === 'comentario') {
            $dados['id_comentario_pai'] = $id;
        } else {
            abort(400, 'Tipo inválido de comentário.');
        }

        // salvar bd
        $comentario = Comentario::create($dados);

        // Criar imagem
        if ($request->hasFile('caminho_imagem')) {
    dd($request->file('caminho_imagem'));
        }

        return back()->with('success', 'Comentário publicado!');
    }

    public function focus($id)
    {
        $comentario = Comentario::with([
            'usuario',
            'image',
            'postagem.usuario',
            'respostas.usuario',
            'respostas.imagens'
        ])->findOrFail($id);

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        return view('feed.post.focus-comentario', compact('comentario', 'posts'));
    }
}
