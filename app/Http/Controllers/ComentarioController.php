<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\Comentario;
use App\Models\ImagemComentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        // Salva caso imagem
        if ($request->hasFile('caminho_imagem')) {
            $arquivo = $request->file('caminho_imagem');
            $caminho = $arquivo->store('imagens_comentarios', 'public');

            ImagemComentario::create([
                'id_comentario' => $comentario->id,
                'caminho_imagem' => $caminho,
            ]);
        }

        // Redirecionamento
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
            'respostas.image'
        ])->findOrFail($id);

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        $tendenciasPopulares = \App\Models\Tendencia::populares(5)->get();

        return view('feed.post.focus-comentario', compact('comentario', 'posts', 'tendenciasPopulares'));
    }

     public function update(Request $request, Comentario $comentario)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif,jpeg|max:4096',
        ]);

        // Atualiza texto
        $comentario->update([
            'comentario' => $request->comentario,
        ]);

        $imagemPrincipal = $comentario->imagem;

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
            $caminho = $arquivo->store('imagens_comentarios', 'public');

            if ($imagemPrincipal) {
                if (Storage::disk('public')->exists($imagemPrincipal->caminho_imagem)) {
                    Storage::disk('public')->delete($imagemPrincipal->caminho_imagem);
                }
                $imagemPrincipal->update(['caminho_imagem' => $caminho]);
            } else {
                $comentario->image()->create(['caminho_imagem' => $caminho]);
            }
        }

        return redirect()->back()->with('success', 'Comentário atualizado com êxito!');
    }
}