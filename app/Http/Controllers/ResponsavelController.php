<?php

namespace App\Http\Controllers;

use App\Models\Autista;
use App\Models\Responsavel;
use App\Models\Usuario;
use App\Models\Genero;
use App\Models\FoneUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

public function addDependente(Request $request)
{
    Log::info('Início do método addDependente', $request->all());

    $request->validate([
        'cpf' => 'nullable|string|max:20',
        'ciptea' => 'nullable|string|max:100',
    ]);

    try {
        $cpf = $request->filled('cpf')
            ? preg_replace('/[^0-9]/', '', $request->cpf)
            : null;

        // Busca autista por CPF OU CIPTEA
        $autista = Autista::query()
            ->when($cpf, fn($query) =>
                $query->whereHas('usuario', fn($q) => $q->where('cpf', $cpf))
            )
            ->when($request->filled('ciptea'), fn($query) =>
                $query->where('cipteia_autista', $request->ciptea)
            )
            ->first();

        if (!$autista) {
            return redirect()->route('profile.show');
        }

        // Obtém ou cria o responsável
        $responsavel = auth()->user()->responsavel
            ?? auth()->user()->responsavel()->create([]);

        // Verifica se o vínculo já existe
        if ($responsavel->autistas()->where('autista_id', $autista->id)->exists()) {
            return redirect()->route('profile.show');
        }

        // Adiciona vínculo (pivot)
        $responsavel->autistas()->attach($autista->id);

        // Atualiza tipo de usuário
        if (auth()->user()->tipo_usuario === 3) {
            auth()->user()->update(['tipo_usuario' => 5]);
        }

        Log::info("Usuário {$responsavel->usuario_id} agora é responsável pelo autista {$autista->id}");

        return redirect()->route('profile.show');

    } catch (\Exception $e) {
        Log::error('Erro ao vincular dependente: ' . $e->getMessage());
        return redirect()->route('profile.show');
    }
}



public function removeDependente(Request $request)
{
    Log::info('Início do método removeDependente', $request->all());

    $request->validate([
        'dependente_id' => 'required|integer|exists:tb_autista,id',
    ]);

    try {
        $autista = Autista::findOrFail($request->dependente_id);
        $responsavel = auth()->user()->responsavel;

        if (!$responsavel) {
            return redirect()->route('profile.show');
        }

        // Remove o vínculo (pivot)
        $responsavel->autistas()->detach($autista->id);

        Log::info("Usuário {$responsavel->usuario_id} desvinculou o dependente {$autista->id}");

        // Se não houver mais dependentes, volta tipo para 3
        if ($responsavel->autistas()->count() === 0) {
            auth()->user()->update(['tipo_usuario' => 3]);
        }

        return redirect()->route('profile.show');

    } catch (\Exception $e) {
        Log::error('Erro ao desvincular dependente: ' . $e->getMessage());
        return redirect()->route('profile.show');
    }
}


    public function index() {
        
    }

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

        return redirect()->route('login')->with('success', 'Usuário responsável cadastrado com sucesso!');
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
