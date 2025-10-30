<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use App\Models\Autista;
use App\Models\Genero;
use App\Models\FoneUsuario;

class AutistaController extends Controller
{
    private $genero;

    public function __construct(Genero $genero)
    {
        $this->genero = $genero;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        $generos = $this->genero->all();
        return view('auth.create-autista', compact('generos'));
    }

    public function store(Request $request)
    {
        Log::info('Início do método store', $request->all());

        // Validação
        $validator = Validator::make($request->all(), [
            'user' => 'nullable|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:tb_usuario,email',
            'senha' => 'required|string|min:6',
            'cpf' => 'required|max:20|unique:tb_usuario,cpf',
            'genero' => 'required|string',
            'data_nascimento' => 'required|date',
            'cpf_responsavel' => 'nullable|string',
            'tipo_usuario' => 'required|in:2',
            'status_conta' => 'required|in:1',
            'numero_telefone' => 'required|array|min:1',
            'numero_telefone.*' => 'required|string|max:20',
            'foto' => 'image|mimes:png,jpg,gif|max:4096', //foto perfil
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // Calcula idade
        $data_nascimento = new \DateTime($request->data_nascimento);
        $hoje = new \DateTime();
        $idade = $hoje->diff($data_nascimento)->y;

        if ($idade < 18 && empty($request->cpf_responsavel)) {
            return response()->json([
                'message' => 'CPF do responsável é obrigatório para menores de 18 anos.'
            ], 422);
        }

        $limparCPF = function ($cpf) {
            return preg_replace('/[^0-9]/', '', $cpf);
        };

        $cpfRequest = $limparCPF($request->cpf);

        if (Usuario::where('cpf', $cpfRequest)->exists()) {
            return response()->json(['message' => 'CPF já cadastrado.'], 409);
        }

        if (!self::validaCPF($cpfRequest)) {
            return response()->json(['message' => 'CPF inválido.'], 422);
        }

        if ($request->hasFile('foto')) {
            // salva em storage/app/arquivos/perfil/fotos
            $path = $request->file('foto')->store('arquivos/perfil/fotos', 'public');
        }


        $user_usuario = $request->user ?? $request->apelido ?? '';

        try {
            $usuario = Usuario::create([
                'email' => $request->email,
                'user' => $user_usuario,
                'senha' => bcrypt($request->senha),
                'cpf' => $cpfRequest,
                'genero' => $request->genero,
                'data_nascimento' => $request->data_nascimento,
                'apelido' => $request->apelido,
                'foto' => $path,
                'tipo_usuario' => $request->tipo_usuario,
                'status_conta' => $request->status_conta,
            ]);

            Log::info('Usuário criado com ID: ' . $usuario->id);

            if ($request->has('numero_telefone') && is_array($request->numero_telefone)) {
                foreach ($request->numero_telefone as $telefone) {
                    $telefone_limpo = preg_replace('/\D/', '', $telefone);
                    FoneUsuario::create([
                        'usuario_id' => $usuario->id,
                        'numero_telefone' => $telefone_limpo,
                    ]);
                }
            }

            $idCuidador = null;

            if ($idade < 18) {
                $cpfRespLimpo = preg_replace('/\D/', '', $request->cpf_responsavel);

                // Busca o usuário do responsável pelo CPF
                $cuidadorUsuario = Usuario::where('cpf', $cpfRespLimpo)->first();

                if (!$cuidadorUsuario) {
                    return response()->json([
                        'message' => 'CPF do responsável não encontrado no sistema.'
                    ], 422);
                }

                // Usa o ID do usuário encontrado como responsável
                $idCuidador = $cuidadorUsuario->id;
            }
            Autista::create([
                'cipteia_autista' => $request->CipteiaAutista,
                'status_cipteia_autista' => 'Ativo',
                'usuario_id' => $usuario->id,
                'responsavel_id' => $idCuidador, // já vai ser o ID do usuário responsável
            ]);

            Log::info('Autista criado para usuário ID: ' . $usuario->id);

            return redirect()->route('login')->with('success', 'Usuário Autista cadastrado com sucesso!');
        } catch (\Exception $e) {
            // Retorna o erro em JSON
            Log::error('Erro ao criar usuário/autista: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro interno ao salvar dados.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private static function validaCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf))
            return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++)
                $d += $cpf[$c] * (($t + 1) - $c);
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d)
                return false;
        }
        return true;
    }

    public function show(string $id)
    {
    }
    public function edit(string $id)
    {
    }
    public function update(Request $request, string $id)
    {
    }
    public function destroy(string $id)
    {
    }
}
