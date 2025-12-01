<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Autista;
use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Models\Responsavel;

class AutistaController extends Controller
{
    private $genero;

    public function __construct(Genero $genero)
    {
        $this->genero = $genero;
    }

    public function edit_responsavel($id)
{
    $usuarioLogado = auth()->user();

    // Busca o responsável vinculado ao usuário logado
    $responsavel = $usuarioLogado->responsavel()->firstOrFail();

    // Busca o autista vinculado ao responsável via relação muitos-para-muitos
    $autista = $responsavel->autistas()->with('usuario')->where('autista_id', $id)->first();

    if (!$autista) {
        abort(404, 'Autista não encontrado ou você não tem permissão para editá-lo.');
    }

    return view('responsavel.dados-autista-responsavel', compact('autista'));
}


public function update_responsavel(Request $request, $id)
{
    $usuario = auth()->user();
    $responsavel = $usuario->responsavel()->firstOrFail();

    $autista = $responsavel->autistas()->with('usuario')->where('autista_id', $id)->firstOrFail();

    $usuarioAutista = $autista->usuario;

    $validated = $request->validate([
        'user' => 'required|string|max:255',
        'apelido' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'cpf' => 'required|string|max:14',
        'data_nascimento' => 'nullable|date',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $usuarioAutista->update([
        'user' => $validated['user'],
        'apelido' => $validated['apelido'] ?? null,
        'email' => $validated['email'],
        'cpf' => $validated['cpf'],
        'data_nascimento' => $validated['data_nascimento'] ?? null,
    ]);

    if ($request->hasFile('foto')) {
        if ($usuarioAutista->foto && Storage::disk('public')->exists($usuarioAutista->foto)) {
            Storage::disk('public')->delete($usuarioAutista->foto);
        }

        $path = $request->file('foto')->store('fotos_autistas', 'public');
        $usuarioAutista->foto = $path;
        $usuarioAutista->save();
    }

    return redirect()->route('responsavel.painel')
                     ->with('status', 'autista-updated');
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
        'foto' => 'image|mimes:png,jpg,gif|max:4096',
    ]);

    if ($validator->fails()) {
        Log::warning('Falha na validação', $validator->errors()->toArray());
        return redirect()->back()->withErrors($validator)->withInput();
    }
    Log::info('Validação passou');

    // 2Calcula idade
    $data_nascimento = new \DateTime($request->data_nascimento);
    $hoje = new \DateTime();
    $idade = $hoje->diff($data_nascimento)->y;
    Log::info('Idade calculada: ' . $idade);

    // Valida CPF do responsável se menor de idade
    if ($idade < 18 && empty($request->cpf_responsavel)) {
        Log::warning('Menor de idade sem CPF do responsável');
        return redirect()->back()->withErrors([
            'cpf_responsavel' => 'CPF do responsável é obrigatório para menores de 18 anos.'
        ])->withInput();
    }

    // Limpa CPF
    $cpfRequest = preg_replace('/[^0-9]/', '', $request->cpf);
    Log::info('CPF limpo: ' . $cpfRequest);

    if (Usuario::where('cpf', $cpfRequest)->exists()) {
        Log::warning('CPF já cadastrado: ' . $cpfRequest);
        return redirect()->back()->withErrors(['cpf' => 'CPF já cadastrado.'])->withInput();
    }

    if (!self::validaCPF($cpfRequest)) {
        Log::warning('CPF inválido: ' . $cpfRequest);
        return redirect()->back()->withErrors(['cpf' => 'CPF inválido.'])->withInput();
    }
    Log::info('CPF validado');

    // Salva foto
    $path = null;
    if ($request->hasFile('foto')) {
        try {
            $path = $request->file('foto')->store('arquivos/perfil/fotos', 'public');
            Log::info('Foto salva em: ' . $path);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar foto: ' . $e->getMessage());
        }
    } else {
        Log::info('Nenhuma foto enviada');
    }

    // Nome de usuário
    $user_usuario = $request->user ?? $request->apelido ?? '';
    Log::info('Nome de usuário definido: ' . $user_usuario);

    try {
        // Cria usuário
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

        // Salva telefones
        if ($request->has('numero_telefone') && is_array($request->numero_telefone)) {
            foreach ($request->numero_telefone as $telefone) {
                $telefone_limpo = preg_replace('/\D/', '', $telefone);
                FoneUsuario::create([
                    'usuario_id' => $usuario->id,
                    'numero_telefone' => $telefone_limpo,
                ]);
                Log::info('Telefone salvo: ' . $telefone_limpo);
            }
        }

        //  Cria responsável caso menor de idade
        $idResponsavel = null;
        if ($idade < 18) {
            $cpfRespLimpo = preg_replace('/\D/', '', $request->cpf_responsavel);
            Log::info('CPF responsável limpo: ' . $cpfRespLimpo);

            $cuidadorUsuario = Usuario::where('cpf', $cpfRespLimpo)->first();
            if (!$cuidadorUsuario) {
                Log::warning('Responsável não encontrado');
                return redirect()->back()->withErrors([
                    'cpf_responsavel' => 'CPF do responsável não encontrado no sistema.'
                ])->withInput();
            }

            // Atualiza tipo se necessário
            if ($cuidadorUsuario->tipo_usuario != 5) {
                $cuidadorUsuario->tipo_usuario = 5;
                $cuidadorUsuario->save();
            }

            $responsavel = Responsavel::firstOrCreate(
                ['usuario_id' => $cuidadorUsuario->id]
            );
            $idResponsavel = $responsavel->id;
            Log::info('Responsável criado ou encontrado, ID: ' . $idResponsavel);
        }

        // Cria autista
        $autista = Autista::create([
            'usuario_id' => $usuario->id,
            'cipteia_autista' => $request->CipteiaAutista ?? null,
            'status_cipteia_autista' => "ativo",
        ]);
        Log::info('Autista criado com ID: ' . $autista->id);

        // Relacionamento pivot
        if ($idResponsavel) {
            $autista->responsaveis()->attach($idResponsavel);
            Log::info("Responsável {$idResponsavel} associado ao autista {$autista->id}");
        }

        Log::info('Cadastro finalizado com sucesso');
        return redirect()->route('login')->with('success', 'Usuário Autista cadastrado com sucesso!');
    } catch (\Exception $e) {
        Log::error('Erro ao criar usuário/autista: ' . $e->getMessage());
        return redirect()->back()->withErrors([
            'error' => 'Erro interno ao salvar dados: ' . $e->getMessage()
        ])->withInput();
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
