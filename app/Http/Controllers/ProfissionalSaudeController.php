<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Responsavel;
use App\Models\FoneUsuario;
use App\Models\ProfissionalSaude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfissionalSaudeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cadastro.create-profissional-saude');
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    $request->validate([
        'nome' => 'required|string|max:255',
        'user' => 'nullable|string|max:255',
        'apelido' => 'nullable|string|max:255',
        'email' => 'required|email|unique:tb_usuario,email',
        'senha' => 'required|string|min:6|max:255|confirmed',
        'cpf' => 'required|string|max:255',
        'genero' => 'required|string|max:255',
        'data_nascimento' => 'required|date',
        'tipo_usuario' => 'required|in:4',
        'status_conta' => 'required|in:1',
        'numero_telefone' => 'required|array|min:1',
        'numero_telefone.*' => 'required|string|max:20',
        'tipo_registro' => 'required|string|max:255',
        'registro_profissional' => 'required|string|max:255',
    ], [
        'email.unique' => 'Este e-mail já está cadastrado.',
        'senha.confirmed' => 'As senhas não coincidem.',
    ]);

    if ($request->tipo_usuario != 4) {
        abort(403, 'Tentativa de fraude no tipo de usuário.');
    }

    $usuario = Usuario::create([
        'nome' => $request->nome,
        'user' => $request->user,
        'apelido' => $request->apelido,
        'email' => $request->email,
        'senha' => bcrypt($request->senha),
        'cpf' => $request->cpf,
        'genero' => $request->genero,
        'data_nascimento' => $request->data_nascimento,
        'tipo_usuario' => $request->tipo_usuario,
        'status_conta' => $request->status_conta,
    ]);

    ProfissionalSaude::create([
        'usuario_id' => $usuario->id,
        'tipo_registro' => $request->tipo_registro,
        'registro_profissional' => $request->registro_profissional,
    ]);

    foreach ($request->numero_telefone as $telefone) {
        FoneUsuario::create([
            'usuario_id' => $usuario->id,
            'numero_telefone' => $telefone,
        ]);
    }

    return redirect()->route('cadastro.index')->with('Sucesso', 'Profissional de Saúde cadastrado com sucesso!');
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
