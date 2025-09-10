<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Postagem;
use Illuminate\Http\Request;

class ContaController extends Controller
{
    private $usuario;
    private $postagem;

    public function __construct(Usuario $usuario, Postagem $postagem)
    {
        $this->usuario = $usuario;
        $this->postagem = $postagem;
    }

    public function index($usuario_id)
    {
        $posts = Postagem::withCount('curtidas')
            ->where('usuario_id', $usuario_id)
            ->orderByDesc('created_at') // mais recentes primeiro
            ->get();
        $postagens = $this->postagem->with(['imagens', 'usuario'])->OrderByDesc('created_at')->get();

        $usuario = Usuario::findOrFail($usuario_id);
        return view('feed.conta.index', compact('postagens', 'posts', 'usuario'));
    }

    public function Conta() {}
}
