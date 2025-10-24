<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Comunidade;
use App\Models\FoneUsuario;
use App\Models\Genero;
use Illuminate\Support\Facades\Validator;


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

            // Validator no padrão do Autista
            $validator = Validator::make($request->all(), [
                'user' => 'required|string|max:255',
                'apelido' => 'required|string|max:255',
                'email' => 'required|email|unique:tb_usuario,email',
                'senha' => 'required|string|min:6|max:255',
                'senha_confirmacao' => 'required|same:senha',
                'genero' => 'required|integer',
                'data_nascimento' => 'required|date',
                'foto' => 'image|mimes:png,jpg,gif|max:4096', //foto perfil
                'tipo_usuario' => 'required|in:3',
                'status_conta' => 'required|in:1',
                'numero_telefone' => 'required|array|min:1',
                'numero_telefone.*' => 'required|string|max:20'
            ], [
                'apelido.required' => 'O campo apelido é obrigatório',
                'user.required' => 'O campo user é obrigatório',
                'email.required' => 'O campo email é obrigatório',
                'email.email' => 'O campo email deve ser preenchido corretamente',
                'email.unique' => 'Este email já está cadastrado',
                'senha.required' => 'O campo senha é obrigatório',
                'senha.min' => 'Senha deve conter ao menos 6 caracteres',
                'senha_confirmacao.required' => 'O campo senha de confirmação é obrigatório',
                'senha_confirmacao.same' => 'O campo senha de confirmação está diferente do campo senha',
                'genero.required' => 'O campo gênero é obrigatório',
                'data_nascimento.required' => 'O campo data de nascimento é obrigatório',
                'foto.required' => 'É necessário haver uma imagem',
                'numero_telefone.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
                'numero_telefone.*.required' => 'O campo número de telefone é obrigatório (ao menos 1)',
            ]);

            // Verifica o tipo de usuário
            if ($request->tipo_usuario != 3) {
                return redirect()->back()
                    ->withErrors(['tipo_usuario' => 'Tentativa de fraude no tipo de usuário.'])
                    ->withInput();
            }

            // Inserir foto
            if ($request->hasFile('foto')) {
                // salva em storage/app/arquivos/perfil/fotos
                $path = $request->file('foto')->store('arquivos/perfil/fotos', 'public');
            }

            // Cria o usuário
            $usuario = Usuario::create([
                'user' => $request->user,
                'apelido' => $request->apelido,
                'email' => $request->email,
                'senha' => bcrypt($request->senha),
                'genero' => $request->genero,
                'data_nascimento' => $request->data_nascimento,
                'foto' => $path,
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


            return redirect()->route('login')
                ->with('success', 'Usuário comunidade cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar comunidade: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['erro' => 'Erro interno ao salvar dados. Tente novamente mais tarde.'])
                ->withInput();
        }
    }
}
