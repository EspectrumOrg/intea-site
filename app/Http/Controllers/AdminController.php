<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Responsavel;
use App\Models\FoneUsuario;
use Illuminate\Http\Request;

class AdminController extends Controller
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
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:tb_usuario,emailUsuario',
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
            'tipo_usuario' => 'required|in:5',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|'
        ]);

        if ($request->tipo_usuario != 5) { // 0.5 Define tipo Responsável
            abort(403, 'Tentativa de fraude no tipo de usuário.');
        }
        // 1. Criar Usuário Padrão
        $usuario = Usuario::create([
            'nome' => $request->nome,
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
        // 2. Criar Dados Específicos Admin
        $cuidador = Responsavel::create([
            'usuario_id' => $usuario->id,
            'cipteia_autista' => $request->cipteiaAutista,
        ]);
        // 3. Criar Telefone
        $fone = FoneUsuario::create([
            'usuario_id' => $usuario->id,
            'numero_telefone' => $request->foneUsuario,
        ]);

        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo Admin cadastrado com sucesso!');
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
