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


    
    public function index(Request $request) // retornar dados
    {
        $query = $this->denuncia->query();

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

        $denuncias = $query->with(['usuario', 'postagem.usuario', 'postagem.imagens', 'postagem.denuncias'])->paginate(10);

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
            'status_denuncia' => '1',
        ]);

        return back()->with('warning', 'usuário denunciado');
    }



    public function resolve($id) // marcar denúncia como resolvida
    {
        $denuncia = DenunciaPostagem::findOrFail($id);

        // Resolver principal
        $denuncia->status_denuncia = 0;
        $denuncia->save();

        // Resolver todas relacionadas à mesma postagem
        DenunciaPostagem::where('id_postagem', $denuncia->id_postagem)
            ->where('id', '!=', $denuncia->id)
            ->update(['status_denuncia' => 0]);

        session()->flash("success", "Denúncia resolvida junto com relacionadas.");
        return redirect()->back();
    }



    public function destroy($id) // banir usuário
    {
        if ($id = !1) {  //ID diferente de 1
            $usuario = Usuario::findOrFail($id);
            $usuario->status_conta = 2;
            $usuario->save();

            session()->flash("warning", "Usuário banido");
            return redirect()->back();
        } else {
            session()->flash("warning", "O usuário principal não pode ser banido!");
            return redirect()->back();
        }
    }
}
