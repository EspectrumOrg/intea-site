<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Postagem;
use App\Models\Curtida;
use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Models\Autista;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
use App\Models\Tendencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContaController extends Controller
{
    private $usuario;
    private $postagem;
    private $genero;
    private $telefone;

    public function __construct(Usuario $usuario, Postagem $postagem, Genero $genero, FoneUsuario $telefone)
    {
        $this->usuario = $usuario;
        $this->postagem = $postagem;
        $this->genero = $genero;
        $this->telefone = $telefone;
    }

    /*
     Exibe o perfil completo do usuário com as quatro categorias
     */
    public function show($usuario_id = null)
{
    try {
        // Tenta encontrar o usuário (passado na URL ou o logado)
        $user = $usuario_id ? Usuario::findOrFail($usuario_id) : auth()->user();

        // Telefones, gêneros, dados específicos do tipo
        $generos = $this->genero->all();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();
        $dadosespecificos = $this->getDadosEspecificos($user);

        // Postagens do usuário
        $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
            ->with(['imagens', 'usuario'])
            ->where('usuario_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // Curtidas do usuário
        $likedPosts = Curtida::with(['postagem.usuario', 'postagem.imagens'])
            ->where('id_usuario', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // Postagens populares
        $postsPopulares = Postagem::withCount('curtidas')
            ->with(['imagens', 'usuario'])
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

          $tendenciasPopulares = Tendencia::populares(7)->get();

        // Pega o autista só se o user for responsável (tipo 5)
        $responsavel = null;
        $autista = null;
        if ($user->tipo_usuario == 5) {
            $responsavel = Responsavel::where('usuario_id', $user->id)->first();
            if ($responsavel) {
                $autista = Autista::where('responsavel_id', $responsavel->id)->first();
            }
        }

        return view('profile.show', compact(
            'user',
            'generos',
            'telefones',
            'dadosespecificos',
            'userPosts',
            'likedPosts',
            'postsPopulares',
            'tendenciasPopulares',
            'autista',
            'responsavel'
        ));

    } catch (\Exception $e) {
        // Mostra erro de forma segura sem quebrar a view
        Log::error('Erro ao carregar perfil: ' . $e->getMessage());

        return view('profile.show', [
            'user' => null,
            'generos' => [],
            'telefones' => [],
            'dadosespecificos' => null,
            'userPosts' => collect(),
            'likedPosts' => collect(),
            'postsPopulares' => collect(),
            'autista' => null,
            'responsavel' => null,
            'error' => 'Erro ao carregar o perfil. Tente novamente mais tarde.',
        ]);
    }
}


    /*
      Obtém os dados específicos baseados no tipo de usuário
     */
    private function getDadosEspecificos(Usuario $user)
    {
        switch ($user->tipo_usuario) {
            case 2: // Autista
                return $user->autista;
            case 4: // Profissional de Saúde
                return $user->profissionalsaude;
            case 5: // Responsável
                return $user->responsavel;
            default:
                return null;
        }
    }

    /*
      Verifica se o usuário é admin
     */
    private function isAdmin()
    {
        return auth()->check() && auth()->user()->tipo_usuario === 1;
    }

    /*
     Método antigo para compatibilidade
     */
    public function index($usuario_id)
    {
        return $this->show($usuario_id);
    }

    public function Conta() {}
}
