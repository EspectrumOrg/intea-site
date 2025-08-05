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

    public function __construct(Usuario $usuario )
    {
        $this->usuario = $usuario;
    }
    public function index()
    {
        $usuario = $this->usuario->orderBy('id')->paginate(10); //ordem páginas

        return view("admin.usuario.index", compact('usuario'));
    }
}
