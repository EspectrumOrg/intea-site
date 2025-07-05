<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Comunidade;
use App\Models\FoneUsuario;

use function Laravel\Prompts\alert;

class ComunidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cadastro.create-comunidade');
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
            'tipo_usuario' => 'required|in:3',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20'
        ]);

        if ($request->tipo_usuario != 3) { // 0.5 Define tipo Comunidade
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

        // 2. Criar Dados Específicos Comunidade
        Comunidade::create([
            'usuario_id' => $usuario->id,
        ]);
        // 3. Criar Telefone(s)
        foreach ($request->numero_telefone as $telefone) {
            FoneUsuario::create([
                'usuario_id' => $usuario->id,
                'numero_telefone' => $request->$telefone,
            ]);
        }

        return redirect()->route('index')->with('Sucesso', 'Usuário Tipo Comunidade cadastrado com sucesso!');
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
