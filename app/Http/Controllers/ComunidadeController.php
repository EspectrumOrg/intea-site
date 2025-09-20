<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Comunidade;
use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use function Laravel\Prompts\alert;

class ComunidadeController extends Controller
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

        return view('auth.create-comunidade', compact('generos'));
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
            'email' => 'required|lowercase|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6|max:255',
            'senha_confirmacao' => 'required|same:senha',
            'cpf' => 'required|max:20|unique:tb_usuario,cpf', // retirar pontuação posteriormente
            'genero' => 'required|integer',
            'data_nascimento' => 'required|date',
            'cep' => 'nullable|string|max:20', // retirar pontuação posteriormente
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
            'numero_telefone.*' => 'required|string|max:20' // retirar pontuação posteriormente
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
            'cpf.unique' => 'CPF á cadastrado',
            'genero.required' => 'O campo gênero é obrigatório',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório',
            'numero_telefone.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
            'numero_telefone.*.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
        ]);

        /* Validação customizada do CPF 
        if (!self::validaCPF($request->cpf)) {
            return back()
                ->withErrors(['cpf' => 'CPF inválido. Por favor, verifique e tente novamente.'])
                ->withInput();
        }*/

        if ($request->tipo_usuario != 3) {
            abort(403, 'Tentativa de fraude no tipo de usuário.');
        }

        //retirar pontuação dos campos só com números
        $cpf_limpo = preg_replace('/\D/', '', $request->cpf);

        // Criar Usuário Padrão
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'user' => $request->user,
            'apelido' => $request->apelido,
            'email' => $request->email,
            'senha' => bcrypt($request->senha),
            'cpf' => $cpf_limpo,
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

        // Criar Dados Específicos Comunidade
        Comunidade::create([
            'usuario_id' => $usuario->id,
        ]);

        // Criar Telefone(s)
        foreach ($request->numero_telefone as $telefone) {
            $telefone_limpo = preg_replace('/\D/', '', $telefone);
            FoneUsuario::create([
                'usuario_id' => $usuario->id,
                'numero_telefone' => $telefone_limpo,
            ]);
        }

        Auth::login($usuario);

        //return response()->json($request->all());
        return redirect()->route('feed.index')->with('Sucesso', 'Usuário Tipo Comunidade cadastrado com sucesso!');
    }

    // Método para validar CPF
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
