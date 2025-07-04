<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Admin;
use App\Models\Autista;
use App\Models\Comunidade;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;

class UsuarioController extends Controller
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
        return view('cadastro.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeAdmin(Request $request)
    {
        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns',
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
            'tipo_usuario' => 'required|in:1',
            'status_conta' => 'required|in:1',
        ]);

        if ($request->tipo_usuario != 1) {
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

        // 2. Criar Dados Específicos
        Admin::create([
            'usuario_id' => $usuario->id,
        ]);
        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo cadastrado com sucesso!');
    }

    public function storeAutista(Request $request)
    {
        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns',
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
            'tipo_usuario' => 'required|in:2',
            'status_conta' => 'required|in:1',
        ]);

        if ($request->tipo_usuario != 2) {
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

        // 2. Criar Dados Específicos Autista
        Autista::create([
            'usuario_id' => $usuario->id,
        ]);
        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo Autista cadastrado com sucesso!');
    }

    public function storeComunidade(Request $request)
    {
        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns',
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
            'tipo_usuario' => 'required|in:3',
            'status_conta' => 'required|in:1',
        ]);

        if ($request->tipo_usuario != 3) {
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

        // 2. Criar Dados Específicos Comunidade
        Comunidade::create([
            'usuario_id' => $usuario->id,
        ]);
        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo Comunidade cadastrado com sucesso!');
    }

    public function storeProfissionalSaude(Request $request)
    {
        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns',
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
            'tipo_usuario' => 'required|in:4',
            'status_conta' => 'required|in:1',
        ]);

        if ($request->tipo_usuario != 4) {
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

        // 2. Criar Dados Específicos Profissional Saúde
        ProfissionalSaude::create([
            'usuario_id' => $usuario->id,
        ]);
        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo Profissional de Saúde cadastrado com sucesso!');
    }

    public function storeResponsavel(Request $request)
    {
        // 0. Validar Dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns',
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
        ]);

        if ($request->tipo_usuario != 5) {
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

        // 2. Criar Dados Específicos Responsável
        Responsavel::create([
            'usuario_id' => $usuario->id,
        ]);
        return redirect()->route('cadastro.index')->with('Sucesso', 'Usuário Tipo Responsável cadastrado com sucesso!');
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
