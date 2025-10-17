<?php

namespace App\Http\Controllers;

use App\Models\CurtidaPostagem;
use App\Models\Postagem;
use Illuminate\Http\Request;

class CurtidaPostagemController extends Controller
{
    public function toggleCurtida($id)
    {
        $usuarioId = auth()->id();

        // garantir que a postagem existe
        $postagem = Postagem::findOrFail($id);

        // verificar se já existe curtida
        $curtida = CurtidaPostagem::where('id_postagem', $postagem->id)
            ->where('id_usuario', $usuarioId)
            ->first();

        if ($curtida) {
            // se já existe, remove
            $curtida->delete();
            return back()->with('success', 'Curtida removida!');
        } else {
            // se não existe, cria
            CurtidaPostagem::create([
                'id_postagem' => $postagem->id,
                'id_usuario' => $usuarioId,
            ]);
            return back()->with('success', 'Curtida adicionada!');
        }
    }
}
