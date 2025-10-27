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

        // Dados básicos
        $dados = [
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
        ];

        // Verifica tipo e define relacionamento
        if ($tipo === 'postagem') {
            $dados['id_postagem'] = $id;
        } elseif ($tipo === 'comentario') {
            $dados['id_comentario_pai'] = $id;
        } else {
            abort(400, 'Tipo inválido de comentário.');
        }

        // Cria o comentário
        $comentario = Comentario::create($dados);

        // Se veio uma imagem, salva no storage
        if ($request->hasFile('caminho_imagem')) {
            $arquivo = $request->file('caminho_imagem');
            $caminho = $arquivo->store('imagens_comentarios', 'public');

            ImagemComentario::create([
                'id_comentario' => $comentario->id,
                'caminho_imagem' => $caminho,
            ]);
        }

        // 🔁 Redirecionamento dinâmico
        if ($tipo === 'postagem') {
            return redirect()
                ->route('post.read', ['postagem' => $id])
                ->with('success', 'Comentário publicado!');
        } elseif ($tipo === 'comentario') {
            return redirect()
                ->route('comentario.focus', ['id' => $id])
                ->with('success', 'Resposta publicada!');
        }

        // fallback (só em caso de erro)
        return back()->with('error', 'Não foi possível redirecionar.');
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
