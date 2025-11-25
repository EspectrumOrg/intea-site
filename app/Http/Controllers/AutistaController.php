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

    // Validação inicial
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
        Log::warning('Falha na validação:', $validator->errors()->toArray());
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Calcula idade
    $data_nascimento = new \DateTime($request->data_nascimento);
    $hoje = new \DateTime();
    $idade = $hoje->diff($data_nascimento)->y;

    // Valida CPF do responsável se menor de idade
    if ($idade < 18 && empty($request->cpf_responsavel)) {
        return redirect()->back()
            ->withErrors(['cpf_responsavel' => 'CPF do responsável é obrigatório para menores de 18 anos.'])
            ->withInput();
    }

    // Limpa CPF
    $cpfRequest = preg_replace('/[^0-9]/', '', $request->cpf);

    // Verifica duplicidade
    if (Usuario::where('cpf', $cpfRequest)->exists()) {
        return redirect()->back()
            ->withErrors(['cpf' => 'CPF já cadastrado.'])
            ->withInput();
    }

    // Valida CPF
    if (!self::validaCPF($cpfRequest)) {
        return redirect()->back()
            ->withErrors(['cpf' => 'CPF inválido.'])
            ->withInput();
    }

    // Salva foto se existir
    $path = null;
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('arquivos/perfil/fotos', 'public');
    }

    $user_usuario = $request->user ?? $request->apelido ?? '';

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
            }
        }

        $idCuidador = null;

        // Se menor de idade, associa responsável
        if ($idade < 18) {
    $cpfRespLimpo = preg_replace('/\D/', '', $request->cpf_responsavel);

    // Log do CPF limpo para conferência
    Log::info('CPF responsável limpo para busca:', [$cpfRespLimpo]);

    // Busca usando Model Usuario
    $cuidadorUsuario = Usuario::where('cpf', $cpfRespLimpo)->first();

    // Se não encontrou, tenta buscar direto no banco (debug)
    if (!$cuidadorUsuario) {
        Log::warning('Responsável não encontrado via Model Usuario, tentando busca direta no DB.');

        $cuidadorUsuario = DB::table('tb_usuario')->where('cpf', $cpfRespLimpo)->first();

        if ($cuidadorUsuario) {
            Log::info('Responsável encontrado via busca direta no DB.', ['usuario' => $cuidadorUsuario]);
        } else {
            Log::error('Responsável não encontrado no banco de dados, mesmo com busca direta.');
            return redirect()->back()
                ->withErrors(['cpf_responsavel' => 'CPF do responsável não encontrado no sistema.'])
                ->withInput();
        }
    }

    // Se usuário encontrado via Model (objeto Eloquent)
    if ($cuidadorUsuario instanceof Usuario) {
        if ($cuidadorUsuario->tipo_usuario != 5) {
            $cuidadorUsuario->tipo_usuario = 5;
            $cuidadorUsuario->save();
        }
    }

    // Se usuário encontrado via Query Builder (objeto stdClass), você pode querer ajustar isso:
    if (is_object($cuidadorUsuario) && !($cuidadorUsuario instanceof Usuario)) {
        // Aqui não dá pra chamar save() diretamente. Se quiser atualizar tipo_usuario,
        // teria que fazer um update manual:
        if ($cuidadorUsuario->tipo_usuario != 5) {
            DB::table('tb_usuario')
                ->where('id', $cuidadorUsuario->id)
                ->update(['tipo_usuario' => 5]);
            Log::info('Atualizado tipo_usuario do responsável via query builder.');
        }
    }

    $registroResponsavel = Responsavel::firstOrCreate(
        ['usuario_id' => $cuidadorUsuario->id],
        ['cipteia_autista' => $request->CipteiaAutista]
    );

    $idCuidador = $registroResponsavel->id;
}
Autista::create([
    'usuario_id' => $usuario->id,
    'cipteia_autista' => $request->CipteiaAutista ?? null,
    'rg_autista' => $request->RgAutista ?? null,
    'status_cipteia_autista' => $request->StatusCipteiaAutista ?? null,
    'responsavel_id' => $idCuidador, // null caso não seja menor de idade
]);


        Log::info('Autista criado para usuário ID: ' . $usuario->id);

        return redirect()->route('login')->with('success', 'Usuário Autista cadastrado com sucesso!');
    } catch (\Exception $e) {
        Log::error('Erro ao criar usuário/autista: ' . $e->getMessage());
        return redirect()->back()
            ->withErrors(['error' => 'Erro interno ao salvar dados: ' . $e->getMessage()])
            ->withInput();
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
