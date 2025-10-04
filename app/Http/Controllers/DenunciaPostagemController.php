<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\DenunciaPostagem;
use App\Models\ImagemPostagem;
use Illuminate\Http\Request;

class DenunciaPostagemController extends Controller
{
    private $denuncia;
    private $imagem_postagem;

    public function __construct(DenunciaPostagem $denuncia, ImagemPostagem $imagem_postagem)
    {
        $this->denuncia = $denuncia;
        $this->imagem_postagem = $imagem_postagem;
    }

    public function index(Request $request)
    {
        $query = $this->denuncia->query();

        /* Busca por nome, user ou email
        if ($request->filled('search_denuncia')) {
            $search = $request->search;
            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }*/

        // Filtro por motivo_denuncia
        if ($request->filled('motivo_denuncia')) {
            $query->where('motivo_denuncia', $request->motivo_denuncia);
        }

        // Filtro por status_denuncia
        if ($request->filled('status_denuncia')) {
            $query->where('status_denuncia', $request->status_denuncia);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc'); // padrão: mais recente
        $query->orderBy('created_at', $ordem);

        $denuncias = $query->with(['usuario', 'postagem.usuario', 'postagem.imagens'])->paginate(10);

        return view('admin.denuncia.index', compact('denuncias'));
    }

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

        DenunciaPostagem::create([
            'id_postagem' => $id_postagem,
            'id_usuario' => $id_usuario,
            'motivo_denuncia' => $request->motivo_denuncia,
            'texto_denuncia' => $request->texto_denuncia,
            'status_denuncia' => 1,
        ]);

        return back();
    }

    public function resolve($id)
    {
        $denuncia = DenunciaPostagem::findOrFail($id);

        if ($denuncia->status_denuncia == 0) {
            return redirect()->back()->with('info', 'Essa denúncia já foi marcada como resolvida.');
        }

        $denuncia->status_denuncia = 0; // 0 = resolvida, 1 = pendente
        $denuncia->save();

        return redirect()->back()->with('successo', 'Denúncia marcada como resolvida com sucesso.');
    }


    public function destroy($id) //Banir usuário
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->status_conta = 0;
        $usuario->save();

        session()->flash("successo", "Usuário banido");
        return redirect()->back();
    }
}
