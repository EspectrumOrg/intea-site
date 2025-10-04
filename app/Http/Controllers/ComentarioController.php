<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\Comentario;
use App\Models\ImagemComentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
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
        $comentario = Comentario::create([
            'id_postagem' => $id_postagem,
            'id_usuario' => auth()->id(), 
            'comentario' => $request->comentario,
        ]);

        // Criar imagem
        if ($request->hasFile('caminho_imagem')) {
            $imagem = $request->file('caminho_imagem')->store('arquivos/postagens', 'public');

            ImagemComentario::create([
                'id_comentario' => $comentario->id,
                'caminho_imagem' => $imagem,
            ]);
        }

        return redirect()
            ->route('post.read', $id_postagem)
            ->with('success', 'Comentário publicado!');
    }
}
