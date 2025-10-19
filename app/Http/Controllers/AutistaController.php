<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Models\Autista;
use App\Models\Genero;
use App\Models\FoneUsuario;

class AutistaController extends Controller
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

        return view('auth.create-autista', compact('generos'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Loga o início do método e os dados recebidos no request
        Log::info('Início do método store', $request->all());

        // Validação dos campos obrigatórios e formato dos dados recebidos
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'user' => 'nullable|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6',
            'cpf' => 'required|max:20|unique:tb_usuario,cpf', // retirar pontuação posteriormente
            'genero' => 'required|string',
            'data_nascimento' => 'required|date',
            'cep' => 'nullable|string|max:20', // retirar pontuação posteriormente
            'logradouro' => 'nullable|string',
            'endereco' => 'nullable|string',
            'rua' => 'nullable|string',
            'bairro' => 'nullable|string',
            'numero' => 'nullable|string',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string',
            'complemento' => 'nullable|string',
            'cpf_responsavel' => 'nullable|string', // novo campo, obrigatório para menores de 18 anos
            'tipo_usuario' => 'required|in:2',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20' // retirar pontuação posteriormente
        ]);

        // Se a validação falhar, retorna erros com status 422
        if ($validator->fails()) {
            Log::error('Validação falhou', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calcula a idade do usuário com base na data de nascimento
        $data_nascimento = new \DateTime($request->data_nascimento);
        $hoje = new \DateTime();
        $idade = $hoje->diff($data_nascimento)->y;

        // Se usuário for menor de 18 anos, CPF do responsável torna-se obrigatório
        if ($idade < 18 && empty($request->cpf_responsavel)) {
            return response()->json([
                'message' => 'CPF do responsável é obrigatório para menores de 18 anos.'
            ], 422);
        }

        // Função para limpar o CPF, retirando tudo que não for número
        $limparCPF = function ($cpf) {
            return preg_replace('/[^0-9]/', '', $cpf);
        };
        $cpfRequest = $limparCPF($request->cpf);
        Log::info('CPF limpo: ' . $cpfRequest);

        // Verifica se o CPF do usuário já está cadastrado no banco
        if (Usuario::where('cpf', $cpfRequest)->exists()) {
            Log::warning('CPF já cadastrado: ' . $cpfRequest);
            return response()->json(['message' => 'CPF já cadastrado.'], 409);
        }

        // Valida o CPF do usuário, certificando que é um CPF válido
        if (!self::validaCPF($cpfRequest)) {
            Log::warning('CPF inválido: ' . $cpfRequest);
            return response()->json(['message' => 'CPF inválido.'], 422);
        }

        // Inicializa variável para armazenar o ID do Responsavel (responsável)
        $idCuidador = null;
        // Se usuário for menor de 18 anos, procura o cuidador pelo CPF informado
        if ($idade < 18) {
            $cpfRespLimpo = $limparCPF($request->cpf_responsavel);

            // Busca o responsável no banco pelo CPF
            $cuidador = Usuario::where('cpf', $cpfRespLimpo)->first();
            if (!$cuidador) {
                return response()->json([
                    'message' => 'CPF do responsável não encontrado no sistema.'
                ], 422);
            }

            // Guarda o ID do cuidador para salvar na tabela autista depois
            $idCuidador = $cuidador->id;
        }

        // Se o campo userUsuario estiver vazio, define-o com o apelidoUsuario
        $user_usuario = $request->user ?? '';
        if (empty($user_usuario)) {
            $user_usuario = $request->apelido ?? '';
            Log::info('user estava vazio, usando apelido: ' . $user_usuario);
        }

        // Tenta criar os registros no banco dentro de um bloco try-catch para tratar erros
        try {
            // Cria o usuário com os dados validados
            $usuario = Usuario::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'user' => $user_usuario,
                'senha' => bcrypt($request->senha), // senha criptografada
                'cpf' => $cpfRequest,
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
                'apelido' => $request->apelido,
                'tipo_usuario' => $request->tipo_usuario,
                'status_conta' => $request->status_conta,
            ]);

            Log::info('Usuário criado com ID: ' . $usuario->id);

            // Se o request tem telefones e é array, cadastra todos na tabela tb_foneusuario
            if ($request->has('numero_telefone') && is_array($request->numero_telefone)) {
                foreach ($request->numero_telefone as $telefone) {
                    $telefone_limpo = preg_replace('/\D/', '', $telefone); //tira tudo que não for números do telefone
                    \App\Models\FoneUsuario::create([
                        'usuario_id' => $usuario->id,
                        'numero_telefone' => $telefone_limpo,
                    ]);
                }
                Log::info('Telefones cadastrados para usuário ID: ' . $usuario->id);
            }

            // Cria o registro na tabela autista, relacionando ao usuário e cuidador (se houver)
            Autista::create([
                'cipteia_autista' => $request->CipteiaAutista,
                'status_cipteia_autista' => 'Ativo',
                'usuario_id' => $usuario->id,
                'responsavel_id' => $idCuidador,
            ]);




            Log::info('Autista criado para usuário ID: ' . $usuario->id);
            // Retorna sucesso com status 201
            //return redirect()->route('dashboard')->with('Sucesso', 'Usuário e autista cadastrados com sucesso!');
            //return response()->json(['message' => 'Usuário e autista cadastrados com sucesso.'], 201);


            //return response()->json($request->all());
            return redirect()->route('login')->with('success', 'Usuário autista cadastrado com sucesso!');
        } catch (\Exception $e) {
            // Em caso de erro, loga e retorna erro interno 500
            Log::error('Erro ao criar usuário/autista: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao salvar dados.'], 500);
        }
    }

    // Função que valida CPF conforme algoritmo padrão
    private static function validaCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se tem 11 dígitos ou se todos são iguais
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Cálculo dos dígitos verificadores do CPF
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
