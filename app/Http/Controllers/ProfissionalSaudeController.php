<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\ProfissionalSaude;
use App\Models\Genero;
use App\Models\FoneUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfissionalSaudeController extends Controller
{
    private $genero;

    public function __construct(Genero $genero) //Gerar objeto (transformar variavel $news em objeto News pelo request)
    {
        $this->genero = $genero;
    }
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
        $generos = $this->genero->all();

        return view('cadastro.create-profissional-saude', compact('generos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'user' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|lowercase|unique:tb_usuario,email',
            'senha' => 'required|string|min:6|max:255|confirmed',
            'cpf' => 'required|digits:11',
            'genero' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'tipo_usuario' => 'required|in:4',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20',
            'tipo_registro' => 'required|string|max:255',
            'registro_profissional' => 'required|string|max:255',
        ], [
            'nome.required' => 'O campo nome é obrigatório',
            'user.required' => 'O campo user é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.lowercase' => 'O campo email não deve conter letras maiúsculas',
            'email.email' => 'O campo email deve ser preenchido corretamente',
            'email.unique' => 'este email já eestá cadastrado',
            'senha.required' => 'O campo senha é obrigatório',
            'senha.min' => 'Senha deve conter ao menos 6 caracteres',
            'senha_confirmacao.required' => 'O campo senha de confirmação é obrigatório',
            'senha_confirmacao.same' => 'O campo senha de confirmação está diferente do campo senha',
            'cpf.required' => 'O campo cpf é obrigatório',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos',
            'genero.required' => 'O campo gênero é obrigatório',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório',
            'numero_telefone.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
            'numero_telefone.*.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
        ]);

        // Validação customizada do CPF
        if (!self::validaCPF($request->cpf)) {
            return back()
                ->withErrors(['cpf' => 'CPF inválido. Por favor, verifique e tente novamente.'])
                ->withInput();
        }

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

        Auth::login($usuario);

        return redirect()->route('pagina_saude')->with('Sucesso', 'Profissional de Saúde cadastrado com sucesso!');
    }

    // Função para validar CPF, copie ela para dentro da sua classe (controller)
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
