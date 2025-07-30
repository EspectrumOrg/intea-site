<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Responsavel;
use App\Models\Genero;
use App\Models\FoneUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponsavelController extends Controller
{
    private $genero;

    public function __construct(Genero $genero) //Gerar objeto (transformar variavel $news em objeto News pelo request)
    {
        $this->genero = $genero;
    }
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $generos = $this->genero->all();

        return view('cadastro.create-responsavel', compact('generos'));
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
            'apelido' => 'required|string|max:255',
            'email' => 'required|lowercase|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6|max:255',
            'senha_confirmacao' => 'required|same:senha',
            'cpf' => 'required|digits:11',
            'cipteia_autista' => 'required|max:255',
            'genero' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'tipo_usuario' => 'required|in:5',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20'
        ], [
            'nome.required' => 'O campo nome é obrigatório',
            'user.required' => 'O campo user é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.lowercase' => 'o campo email não deve conter letras maiúsculas',
            'email.email' => 'o campo email deve ser preenchido corretamente',
            'email.unique' => 'este email já existe em nossos registros',
            'senha.required' => 'O campo senha é obrigatório',
            'senha.min' => 'Senha deve conter ao menos 6 caracteres',
            'senha_confirmacao.required' => 'O campo senha de confirmação é obrigatório',
            'senha_confirmacao.same' => 'O campo senha de confirmação está diferente do campo senha',
            'cpf.required' => 'O campo cpf é obrigatório',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos',
            'cipteia_autista.required' => 'O campo cipteia autista é obrigatório',
            'genero.required' => 'O campo gênero é obrigatório',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório',
            'numero_telefone.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
            'numero_telefone.*.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
        ]);

        if ($request->tipo_usuario != 5) { // 0.5 Define tipo Responsável
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
            'tipo_usuario' => $request->tipo_usuario,
            'status_conta' => $request->status_conta,
        ]);
        // 2. Criar Dados Específicos Responsável
        Responsavel::create([
            'usuario_id' => $usuario->id,
            'cipteia_autista' => $request->cipteia_autista,
        ]);
        // 3. Criar Telefone(s)
        foreach ($request->numero_telefone as $telefone) {
            FoneUsuario::create([
                'usuario_id' => $usuario->id,
                'numero_telefone' => $telefone,
            ]);
        }

        Auth::login($usuario); //Entra direto

        return redirect()->route('dashboard')->with('Sucesso', 'Usuário Tipo Responsável cadastrado com sucesso!');
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
