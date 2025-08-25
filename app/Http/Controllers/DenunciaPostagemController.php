<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use Illuminate\Http\Request;

class DenunciaPostagemController extends Controller
{
    public function post(Request $request, $id_postagem, $id_usuario)
    {
        $request->validate([
            'motivo_denuncia' => 'required|string',
            'texto_denuncia' => 'required|string|max:555',
        ], [
            'motivo_denuncia.required' => 'O campo motivo denúncia é obrigatório',
            'texto_denuncia.required' => 'O campo texto é obrigatório',
            'texto_denuncia.max' => 'O campo texto só comporta até 555 caracteres',
        ]);

        Denuncia::create([
            'id_postagem' => $id_postagem,
            'id_usuario' => $id_usuario,
            'motivo_denuncia' => $request->motivo_denuncia,
            'texto_denuncia' => $request->texto_denuncia,
        ]);

        return back();
    }
}
