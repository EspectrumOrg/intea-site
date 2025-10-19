<?php

namespace App\Http\Controllers;

use App\Models\Autista;
use App\Models\Responsavel;
use App\Models\Usuario;
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
    public function edit_autista($id)
    {
        $usuario = auth()->user();

        // Pega o responsável logado
        $responsavel = Responsavel::where('usuario_id', $usuario->id)->firstOrFail();

        // Garante que o autista é do responsável
        $autista = Autista::where('id', $id)
                    ->where('responsavel_id', $responsavel->id)
                    ->firstOrFail();

       return view('profile.dados-autista-responsavel', compact('autista'));
    }


    public function update_autista(Request $request, $id) 
{
    $usuario = auth()->user();
    $responsavel = Responsavel::where('usuario_id', $usuario->id)->firstOrFail();

    $autista = Autista::where('id', $id)
        ->where('responsavel_id', $responsavel->id)
        ->firstOrFail();

    if (!$autista->usuario) {
        return back()->withErrors(['Erro: Autista sem vínculo com usuário.']);
    }

    // Validação dos dados do request (ajuste conforme suas regras)
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'user' => 'required|string|max:255',
        'apelido' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'cpf' => 'required|string|max:14',
        'data_nascimento' => 'nullable|date',
    ]);

    // Atualiza os dados do usuário relacionado ao autista
    $usuarioAutista = $autista->usuario;
    $usuarioAutista->nome = $validated['nome'];
    $usuarioAutista->user = $validated['user'];
    $usuarioAutista->apelido = $validated['apelido'] ?? null;
    $usuarioAutista->email = $validated['email'];
    $usuarioAutista->cpf = $validated['cpf'];
    $usuarioAutista->data_nascimento = $validated['data_nascimento'] ?? null;

    $usuarioAutista->save();

    return redirect()->route('profile.show') // ou qualquer outra rota que faça sentido
                 ->with('status', 'autista-updated');
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

        return view('auth.create-responsavel', compact('generos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //retirar pontuação dos campos só com números
        $request->merge([
            'cpf' => preg_replace('/\D/', '', $request->cpf)
        ]);

        // 0. Validar Dados com regras básicas
        $request->validate([
            'nome' => 'required|string|max:255',
            'user' => 'required|string|max:255',
            'apelido' => 'required|string|max:255',
            'email' => 'required|lowercase|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6|max:255',
            'senha_confirmacao' => 'required|same:senha',
            'cpf' => 'required|max:20|unique:tb_usuario,cpf', // retirar pontuação posteriormente
            'cipteia_autista' => 'required|max:255',
            'genero' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'tipo_usuario' => 'required|in:5',
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
            $telefone_limpo = preg_replace('/\D/', '', $telefone);
            FoneUsuario::create([
                'usuario_id' => $usuario->id,
                'numero_telefone' => $telefone_limpo,
            ]);
        }

        Auth::login($usuario);

        return redirect()->route('post.index')->with('success', 'Usuário Tipo Responsável cadastrado com sucesso!');
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

    public function perfil()
    {
        // a linha abaixo e para poder usar o load se nao ele da erro
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        $usuario->load('responsavel', 'telefones', 'genero');
        return view('perfilResponsavel', compact('usuario'));
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
