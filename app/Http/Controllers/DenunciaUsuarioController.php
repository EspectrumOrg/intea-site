<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\DenunciaUsuario;
use Illuminate\Http\Request;

class DenunciaUsuarioController extends Controller
{

    private $denuncia;

    public function __construct(DenunciaUsuario $denuncia)
    {
        $this->denuncia = $denuncia;
    }

    public function index(Request $request) // retornar dados
    {
        $query = $this->denuncia->query();

        // Busca por nome, user ou email
        if ($request->filled('search_denuncia')) {
            $search = $request->search;
            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por motivo_denuncia
        if ($request->filled('motivo_denuncia')) {
            $query->where('motivo_denuncia', $request->motivo_denuncia);
        }

        // Filtro por status_conta
        if ($request->filled('status_conta')) {
            $query->where('status_conta', $request->status_conta);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc'); // padrão: mais recente
        $query->orderBy('created_at', $ordem);

        $denuncias = $query->with(['usuario', 'postagem.usuario', 'postagem.imagens'])->paginate(10);

        return view('admin.usuario.index', compact('denuncias'));
    }



    public function post(Request $request, $id_usuario_denunciado, $id_usuario_denunciante) // criar denúncia
    {
        $request->validate([
            'motivo_denuncia' => 'required|string',
            'texto_denuncia' => 'required|string|max:555',
        ], [
            'motivo_denuncia.required' => 'O campo motivo denúncia é obrigatório',
            'texto_denuncia.required' => 'O campo texto é obrigatório',
            'texto_denuncia.max' => 'O campo texto só comporta até 555 caracteres',
        ]);

        DenunciaUsuario::create([
            'id_usuario_denunciado' => $id_usuario_denunciado,
            'id_usuario_denunciante' => $id_usuario_denunciante,
            'motivo_denuncia' => $request->motivo_denuncia,
            'texto_denuncia' => $request->texto_denuncia,
        ]);

        return back();
    }
}
