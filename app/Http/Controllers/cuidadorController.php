<?php

namespace App\Http\Controllers;
use App\Models\cuidadorModel;
use App\Models\FoneUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;

class cuidadorController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'user' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6|max:255',
            'cpf' => 'required|string|max:255',
            'genero' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'cep' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'rua' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'tipo_usuario' => 'required|in:5', // Corrigido para 5
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20',
            'cipteia' => 'required|string|max:255',
        ]);

        if ($request->tipo_usuario != 5) {
            abort(403, 'Tentativa de fraude no tipo de usuário.');
        }

        // 1. Criar Usuário Padrão
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'user' => $request->user,
            'apelido' => $request->apelido,
            'email' => $request->email,
            'senha' => bcrypt($request->senha),
            'cpf' => $request->cpf,
            'genero' => $request->genero,
            'data_nascimento' => $request->data_nascimento,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'endereco' => $request->endereco,
            'rua' => $request->rua,
            'bairro' => $request->bairro,
            'numero' => $request->numero,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'complemento' => $request->complemento,
            'tipo_usuario' => $request->tipo_usuario,
            'status_conta' => $request->status_conta,
        ]);

      try {
    cuidadorModel::create([
        'usuario_id' => $usuario->id,
        'cipteiaAutista' => $request->cipteia
    ]);
} catch (\Exception $e) {
    return back()->withErrors(['msg' => 'Erro ao inserir cuidador: ' . $e->getMessage()]);
}

        foreach ($request->numero_telefone as $telefone) {
            FoneUsuario::create([
                'usuario_id' => $usuario->id,
                'numero_telefone' => $telefone,
            ]);
        }
            return redirect()->route('api.todos.usuarios');

    } // <-- Fecha o método store aqui!

public function apiTodosUsuarios()
{
  $usuarios = \App\Models\Usuario::all()->map(function ($user) {
        return [
            'nome' => $user->nome,
            'email' => $user->email,
        'tipo_usuario' => $user->tipo_usuario,  // <-- aqui pega o número direto
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $usuarios
    ]);
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
