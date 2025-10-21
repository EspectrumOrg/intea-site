<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Admin;
use App\Models\Autista;
use App\Models\ChatPrivado;
use App\Models\ChatPrivadoModel;
use App\Models\Comunidade;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
use App\Models\seguirModel;

class UsuarioController extends Controller
{
    private $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }



    public function Conversas()
    {
        $usuarioLogado = Auth::id();

        $conversas = ChatPrivado::where('usuario1_id', $usuarioLogado)
            ->orWhere('usuario2_id', $usuarioLogado)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('conversas', compact('conversas', 'usuarioLogado'));
    }

    public function teste()
    {
        $usuarioLogado = Auth::id();

        // Busca todas as conversas que envolvem o usuário logado
        $conversas = ChatPrivado::where('usuario1_id', $usuarioLogado)
            ->orWhere('usuario2_id', $usuarioLogado)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Busca todos os IDs dos usuários que o logado está seguindo
        $seguindoIds = seguirModel::where('segue_id', $usuarioLogado)
            ->pluck('seguindo_id');

        // Busca os dados desses usuários
        $usuariosSeguindo = Usuario::whereIn('id', $seguindoIds)->get();

        return view('feed.chats.conversas', compact('conversas', 'usuariosSeguindo', 'usuarioLogado'));
    }

    public function index(Request $request)
    {
        $query = $this->usuario->query();

        // Busca por nome, user ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo_usuario
        if ($request->filled('tipo_usuario')) {
            $query->where('tipo_usuario', $request->tipo_usuario);
        }

        // Filtro por status_conta
        if ($request->filled('status_conta')) {
            $query->where('status_conta', $request->status_conta);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc'); // padrão: mais recente
        $query->orderBy('created_at', $ordem);

        $usuario = $query->paginate(10)->appends($request->all());

        return view('admin.usuario.index', compact('usuario'));
    }

    public function destroy($id)
    {
        if ($id != 1) {
            $usuario = Usuario::findOrFail($id);

            // Exclui comentários do usuário
            $usuario->comentarios()->delete();

            // Busca todas as postagens do usuário
            $postagens = $usuario->postagens()->with('tendencias')->get();

            // Coleta todas as tendências ligadas às postagens
            $tendenciasIds = [];
            foreach ($postagens as $postagem) {
                foreach ($postagem->tendencias as $tendencia) {
                    $tendenciasIds[] = $tendencia->id;
                }
            }

            // Exclui postagens do usuário (automático detach das pivot)
            foreach ($postagens as $postagem) {
                $postagem->tendencias()->detach();
                $postagem->delete();
            }

            // Verifica tendências que ficaram sem postagens e apaga 🔥
            $tendencias = \App\Models\Tendencia::whereIn('id', $tendenciasIds)->get();
            foreach ($tendencias as $tendencia) {
                if ($tendencia->postagens()->count() === 0) {
                    $tendencia->delete();
                }
            }

            // Marca usuário como banido
            $usuario->status_conta = 2;
            $usuario->save();

            session()->flash("success", "Usuário banido e conteúdo removido.");
            return redirect()->back();
        } else {
            session()->flash("aviso", "O usuário principal não pode ser banido!");
            return redirect()->back();
        }
    }




    public function desbanir($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->status_conta = 1;
        $usuario->save();

        session()->flash("success", "Usuário desbanido");

        return redirect()->back();
    }
}
