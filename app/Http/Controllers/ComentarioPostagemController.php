<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\ComentarioPostagem;
use App\Models\ImagemComentarioPostagem;
use Illuminate\Http\Request;

class ComentarioPostagemController extends Controller
{
    public function store(Request $request, $id_postagem)
    {
        $request->validate(
            [
                'comentario' => 'required|string|max:500',
                'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif|max:2048',
            ],
            [
                'comentario.required' => 'É necessário escrever alguma coisa',
            ]
        );

        // Criar comentário
        $comentario = ComentarioPostagem::create([
            'id_postagem' => $id_postagem,
            'id_usuario' => auth()->id(), 
            'comentario' => $request->comentario,
        ]);

        // Criar imagem
        if ($request->hasFile('caminho_imagem')) {
            $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');

            ImagemComentarioPostagem::create([
                'id_comentario' => $comentario->id,
                'caminho_imagem' => $imagem,
                'id_postagem' => $id_postagem,
            ]);
        }

        return redirect()
            ->route('post.read', $id_postagem)
            ->with('success', 'Comentário publicado!');
    }
}
