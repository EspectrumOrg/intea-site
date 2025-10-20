<?php

namespace App\Http\Controllers;

use App\Models\Curtida;
use Illuminate\Http\Request;

class CurtidaController extends Controller
{
    public function toggleCurtida(Request $request)
    {
        $usuarioId = auth()->id();
        $tipo = $request->input('tipo'); // 'postagem' ou 'comentario'
        $id = $request->input('id');     // id do post ou comentário

        // Define a coluna de acordo com o tipo
        $coluna = match ($tipo) {
            'postagem' => 'id_postagem',
            'comentario' => 'id_comentario',
            default => null,
        };

        if (!$coluna) {
            return back()->with('erro', 'Tipo inválido de curtida.');
        }

        // Verifica se já existe curtida
        $curtida = Curtida::where($coluna, $id)
            ->where('id_usuario', $usuarioId)
            ->first();

        if ($curtida) {
            $curtida->delete();
            return back()->with('nada', 'Curtida removida!');
        }

        Curtida::create([
            $coluna => $id,
            'id_usuario' => $usuarioId,
        ]);

        return back()->with('nada', 'Curtida adicionada!');
    }
}
