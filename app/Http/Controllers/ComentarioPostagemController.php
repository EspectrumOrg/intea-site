<?php

namespace App\Http\Controllers;

use App\Models\ComentarioPostagem;
use Illuminate\Http\Request;

class ComentarioPostagemController extends Controller
{
    public function store(Request $request, $id_postagem)
    {
        $request->validate([
            'comentario' => 'required|string|max:500',
        ]);

        ComentarioPostagem::create([
            'id_postagem' => $id_postagem,
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('sucess');
    }
}
