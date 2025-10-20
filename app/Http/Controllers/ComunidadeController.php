<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Comunidade;
use App\Models\FoneUsuario;
use App\Models\Genero;

class ComunidadeController extends Controller
{
    /**
     * Exibe o formulário de cadastro.
     */
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
     * Salva um novo usuário do tipo Comunidade.
     */
    public function store(Request $request)
    {
        try {
            // Limpa o CPF (remove pontos e traços)
            $cpfLimpo = preg_replace('/\D/', '', $request->cpf);
            $request->merge(['cpf' => $cpfLimpo]);

            // Validação dos campos
            $request->validate([
                'nome' => 'required|string|max:255',
                'user' => 'required|string|max:255',
                'apelido' => 'required|string|max:255',
                'email' => 'required|lowercase|email|unique:tb_usuario,email',
                'senha' => 'required|string|min:6|max:255',
                'senha_confirmacao' => 'required|same:senha',
                'cpf' => 'required|max:20|unique:tb_usuario,cpf',
                'genero' => 'required|integer',
                'data_nascimento' => 'required|date',
                'tipo_usuario' => 'required|in:3',
                'status_conta' => 'required|in:1',
                'numero_telefone' => 'required|array|min:1',
                'numero_telefone.*' => 'required|string|max:20'
            ], [
                'nome.required' => 'O campo nome é obrigatório',
                'apelido.required' => 'O campo apelido é obrigatório',
                'user.required' => 'O campo user é obrigatório',
                'email.required' => 'O campo email é obrigatório',
                'email.lowercase' => 'O campo email não deve conter letras maiúsculas',
                'email.email' => 'O campo email deve ser preenchido corretamente',
                'email.unique' => 'Este email já está cadastrado',
                'senha.required' => 'O campo senha é obrigatório',
                'senha.min' => 'Senha deve conter ao menos 6 caracteres',
                'senha_confirmacao.required' => 'O campo senha de confirmação é obrigatório',
                'senha_confirmacao.same' => 'O campo senha de confirmação está diferente do campo senha',
                'cpf.required' => 'O campo CPF é obrigatório',
                'cpf.unique' => 'CPF já cadastrado',
                'genero.required' => 'O campo gênero é obrigatório',
                'data_nascimento.required' => 'O campo data de nascimento é obrigatório',
                'numero_telefone.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
                'numero_telefone.*.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
            ]);

            // Validação lógica de CPF
            if (!self::validaCPF($cpfLimpo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF inválido. Por favor, verifique e tente novamente.'
                ], 400);
            }

            // Verifica o tipo de usuário
            if ($request->tipo_usuario != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tentativa de fraude no tipo de usuário.'
                ], 403);
            }

            // Cria o usuário
            $usuario = Usuario::create([
                'nome' => $request->nome,
                'user' => $request->user,
                'apelido' => $request->apelido,
                'email' => $request->email,
                'senha' => bcrypt($request->senha),
                'cpf' => $cpfLimpo,
                'genero' => $request->genero,
                'data_nascimento' => $request->data_nascimento,
                'tipo_usuario' => $request->tipo_usuario,
                'status_conta' => $request->status_conta,
            ]);

            // Cria registro na tabela Comunidade
            Comunidade::create([
                'usuario_id' => $usuario->id,
            ]);

            // Cria os telefones
            foreach ($request->numero_telefone as $telefone) {
                $telefone_limpo = preg_replace('/\D/', '', $telefone);
                FoneUsuario::create([
                    'usuario_id' => $usuario->id,
                    'numero_telefone' => $telefone_limpo,
                ]);
            }

            // Login automático (opcional)
            Auth::login($usuario);

            // Retorna sucesso em JSON
            return response()->json([
                'success' => true,
                'message' => 'Usuário Tipo Comunidade cadastrado com sucesso!',
                'redirect' => url('/login')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna erros de validação no formato JSON
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Loga o erro e retorna resposta genérica
            Log::error('Erro ao criar comunidade: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao salvar dados. Tente novamente mais tarde.'
            ], 500);
        }

        return redirect()->route('login')->with('success', 'Usuário comunidade cadastrado com sucesso!');
    }

    /**
     * Função auxiliar para validar CPF.
     */
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
}
