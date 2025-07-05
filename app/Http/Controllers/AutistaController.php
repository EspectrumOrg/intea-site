<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use App\Models\Autista;
use App\Models\FoneUsuario;

class AutistaController extends Controller
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
        //
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
            'nomeUsuario' => 'required|string|max:255',
            'emailUsuario' => 'required|email|unique:tb_usuario,emailUsuario',
            'senhaUsuario' => 'required|string|min:6',
            'cpfUsuario' => 'required|string',
            'generoUsuario' => 'required|string',
            'dataNascUsuario' => 'required|date',
            'cepUsuario' => 'nullable|string',
            'logradouroUsuario' => 'nullable|string',
            'enderecoUsuario' => 'nullable|string',
            'ruaUsuario' => 'nullable|string',
            'bairroUsuario' => 'nullable|string',
            'numeroUsuario' => 'nullable|string',
            'cidadeUsuario' => 'nullable|string',
            'estadoUsuario' => 'nullable|string',
            'complementoUsuario' => 'nullable|string',
            'rgAutista' => 'required|string',
            'userUsuario' => 'nullable|string|max:255',
            'apelidoUsuario' => 'nullable|string|max:255',
            'cpfResponsavel' => 'nullable|string', // novo campo, obrigatório para menores de 18 anos
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
        $dataNascimento = new \DateTime($request->dataNascUsuario);
        $hoje = new \DateTime();
        $idade = $hoje->diff($dataNascimento)->y;

        // Se usuário for menor de 18 anos, CPF do responsável torna-se obrigatório
        if ($idade < 18 && empty($request->cpfResponsavel)) {
            return response()->json([
                'message' => 'CPF do responsável é obrigatório para menores de 18 anos.'
            ], 422);
        }

        // Função para limpar o CPF, retirando tudo que não for número
        $limparCPF = function ($cpf) {
            return preg_replace('/[^0-9]/', '', $cpf);
        };
        $cpfRequest = $limparCPF($request->cpfUsuario);
        Log::info('CPF limpo: ' . $cpfRequest);

        // Verifica se o CPF do usuário já está cadastrado no banco
        if (Usuario::where('cpfUsuario', $cpfRequest)->exists()) {
            Log::warning('CPF já cadastrado: ' . $cpfRequest);
            return response()->json(['message' => 'CPF já cadastrado.'], 409);
        }

        // Valida o CPF do usuário, certificando que é um CPF válido
        if (!self::validaCPF($cpfRequest)) {
            Log::warning('CPF inválido: ' . $cpfRequest);
            return response()->json(['message' => 'CPF inválido.'], 422);
        }

        // Inicializa variável para armazenar o ID do cuidador (responsável)
        $idCuidador = null;
        // Se usuário for menor de 18 anos, procura o cuidador pelo CPF informado
        if ($idade < 18) {
            $cpfRespLimpo = $limparCPF($request->cpfResponsavel);

            // Busca o responsável no banco pelo CPF
            $cuidador = Usuario::where('cpfUsuario', $cpfRespLimpo)->first();
            if (!$cuidador) {
                return response()->json([
                    'message' => 'CPF do responsável não encontrado no sistema.'
                ], 422);
            }

            // Guarda o ID do cuidador para salvar na tabela autista depois
            $idCuidador = $cuidador->id;
        }

        // Se o campo userUsuario estiver vazio, define-o com o apelidoUsuario
        $userUsuario = $request->userUsuario ?? '';
        if (empty($userUsuario)) {
            $userUsuario = $request->apelidoUsuario ?? '';
            Log::info('userUsuario estava vazio, usando apelidoUsuario: ' . $userUsuario);
        }

        // Tenta criar os registros no banco dentro de um bloco try-catch para tratar erros
        try {
            // Cria o usuário com os dados validados
            $usuario = Usuario::create([
                'nomeUsuario' => $request->nomeUsuario,
                'emailUsuario' => $request->emailUsuario,
                'userUsuario' => $userUsuario,
                'senhaUsuario' => bcrypt($request->senhaUsuario), // senha criptografada
                'cpfUsuario' => $cpfRequest,
                'generoUsuario' => $request->generoUsuario,
                'dataNascUsuario' => $request->dataNascUsuario,
                'cepUsuario' => $request->cepUsuario,
                'logradouroUsuario' => $request->logradouroUsuario,
                'enderecoUsuario' => $request->enderecoUsuario,
                'ruaUsuario' => $request->ruaUsuario,
                'bairroUsuario' => $request->bairroUsuario,
                'numeroUsuario' => $request->numeroUsuario,
                'cidadeUsuario' => $request->cidadeUsuario,
                'estadoUsuario' => $request->estadoUsuario,
                'complementoUsuario' => $request->complementoUsuario,
                'apelidoUsuario' => $request->apelidoUsuario,
            ]);

            Log::info('Usuário criado com ID: ' . $usuario->id);

            // Se o request tem telefones e é array, cadastra todos na tabela tb_foneusuario
            if ($request->has('telefoneUsuario') && is_array($request->telefoneUsuario)) {
                foreach ($request->telefoneUsuario as $telefone) {
                    \App\Models\FoneUsuario::create([
                        'idusuario' => $usuario->id,
                        'numeroUsuario' => $telefone,
                    ]);
                }
                Log::info('Telefones cadastrados para usuário ID: ' . $usuario->id);
            }

            // Cria o registro na tabela autista, relacionando ao usuário e cuidador (se houver)
            Autista::create([
                'cipteiaAutista' => 'Existente',
                'statusCipteiaAutista' => 'Ativo',
                'rgAutista' => $request->rgAutista,
                'idusuario' => $usuario->id,
                'idCuidador' => $idCuidador,
            ]);

            Log::info('Autista criado para usuário ID: ' . $usuario->id);

            // Retorna sucesso com status 201
            return response()->json(['message' => 'Usuário e autista cadastrados com sucesso.'], 201);
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
