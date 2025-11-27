<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\Comentario;
use \App\Models\Tendencia;
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
            'respostas' => function ($q) {
                $q->whereHas('usuario', function ($q2) {
                    $q2->where('status_conta', 1);
                });
            },
            'respostas.usuario',
            'respostas.image',
        ])
            ->whereHas('usuario', function ($q) {
                $q->where('status_conta', 1);
            })
            ->findOrFail($id);

        $posts = Postagem::withCount('curtidas')
            ->whereHas('usuario', function ($q) {
                $q->where('status_conta', 1);
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        $tendenciasPopulares = Tendencia::populares(5)
            ->whereHas('postagens', function ($q) {
                $q->whereHas('usuario', function ($q2) {
                    $q2->where('status_conta', 1);
                });
            })
            ->get();

        return view('feed.post.focus-comentario', compact('comentario', 'posts', 'tendenciasPopulares'));
    }

    public function update(Request $request, Comentario $comentario)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
            'caminho_imagem' => 'nullable|image|mimes:png,jpg,gif,jpeg|max:4096',
        ]);

        // Atualiza o texto
        $comentario->update([
            'comentario' => $request->comentario,
        ]);

        $imagem = $comentario->image;

        // Remover imagem de postagem
        if ($request->remover_imagem == 1 && $imagem) {
            Storage::disk('public')->delete($imagem->caminho_imagem);
            $imagem->delete();
        }

        // Enviar nova imagem
        if ($request->hasFile('caminho_imagem')) {
            $path = $request->file('caminho_imagem')->store('imagens_comentarios', 'public');

            if ($imagem) {
                Storage::disk('public')->delete($imagem->caminho_imagem);
                $imagem->update(['caminho_imagem' => $path]);
            } else {
                $comentario->image()->create(['caminho_imagem' => $path]);
            }
        }

        return redirect()->back()->with('success', 'Comentário atualizado com êxito!');
    }


    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);

        //Comentários, Curtidas e Denúncias relacionadas
        $comentario->respostas()->delete();
        $comentario->curtidas_comentario()->delete();
        $comentario->denuncias()->delete();

        // Deletar comentário
        $comentario->delete();

        return redirect()->back()->with('success', 'Comentário excluído com êxito!');
    }
}
