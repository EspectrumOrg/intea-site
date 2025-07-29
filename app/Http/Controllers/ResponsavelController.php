<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Responsavel;
use App\Models\FoneUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponsavelController extends Controller
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
        return view('cadastro.create-responsavel');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    // 0. Validar Dados com regras básicas
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
        // mensagens de erro ...
    ]);

    // Validação customizada do CPF
    if (!self::validaCPF($request->cpf)) {
        return back()
            ->withErrors(['cpf' => 'CPF inválido. Por favor, verifique e tente novamente.'])
            ->withInput();
    }

    // Verifica tipo usuário
    if ($request->tipo_usuario != 5) {
        abort(403, 'Tentativa de fraude no tipo de usuário.');
    }

    // Cria usuário e demais dados
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

    Responsavel::create([
        'usuario_id' => $usuario->id,
        'cipteia_autista' => $request->cipteia_autista,
    ]);

    foreach ($request->numero_telefone as $telefone) {
        FoneUsuario::create([
            'usuario_id' => $usuario->id,
            'numero_telefone' => $telefone,
        ]);
    }

    Auth::login($usuario);

    return redirect()->route('dashboard')->with('Sucesso', 'Usuário Tipo Responsável cadastrado com sucesso!');
}

// Função estática para validar CPF (copie essa função dentro da classe)
private static function validaCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
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
