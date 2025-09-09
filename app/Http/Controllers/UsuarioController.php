<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Admin;
use App\Models\Autista;
use App\Models\Comunidade;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;

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

    public function index(Request $request)
    {
        $query = $this->usuario->query();

        // Busca por nome, user ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $terms = explode(' ', $search);
        
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->Where('nome', 'like', "%{$term}%")
                      ->orWhere('user', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                }
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
        $usuario = Usuario::findOrFail($id);
        $usuario->status_conta = 0;
        $usuario->save();

        session()->flash("success", "Usuário banido");
        return redirect()->back();
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
