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

    public function show($usuario_id = null)
{
    try {
        // Tenta encontrar o usuário (passado na URL ou o logado)
        $user = $usuario_id ? Usuario::findOrFail($usuario_id) : auth()->user();
        
        if (!$user) {
            return redirect('/feed')->with('error', 'Usuário não encontrado.');
        }

        $currentUser = auth()->user();
        
      
        if ($currentUser && $currentUser->id != $user->id && $currentUser->tipo_usuario != 1) {
            // Se NÃO é o próprio usuário E NÃO é admin, OCULTA o CPF
            $user->cpf = '•••••••••••';
        }
        
        // Se não está logado, também oculta o CPF
        if (!$currentUser) {
            $user->cpf = '•••••••••••';
        }

        $generos = $this->genero->all();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();
        $dadosespecificos = $this->getDadosEspecificos($user);

        $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
            ->with(['imagens', 'usuario'])
            ->where('usuario_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $likedPosts = Curtida::with(['postagem.usuario', 'postagem.imagens'])
            ->where('id_usuario', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $postsPopulares = Postagem::withCount('curtidas')
            ->with(['imagens', 'usuario'])
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

       /* $likedComments = Curtida::with([
        'comentario.usuario', 
        'comentario.postagem',
        'comentario.postagem.usuario'
        ])
        ->where('id_usuario', $user->id)
        ->whereNotNull('id_comentario') // Apenas curtidas em comentários
        ->orderByDesc('created_at')
        ->get();*/

        $tendenciasPopulares = Tendencia::populares(7)->get();
        

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
            //'likedComments', 
            'postsPopulares',
            'tendenciasPopulares',
            'autista',
            'responsavel'
        ));

    } catch (\Exception $e) {
        Log::error('Erro ao carregar perfil: ' . $e->getMessage());
        return redirect('/feed')->with('error', 'Erro ao carregar o perfil.');
    }
}

    public function index($usuario_id)
{
    try {
        $user = Usuario::findOrFail($usuario_id);
        $currentUser = auth()->user();

        // Proteção de CPF
        if ($currentUser && $currentUser->id != $user->id && $currentUser->tipo_usuario != 1) {
            $user->cpf = '•••••••••••';
        }
        
        if (!$currentUser) {
            $user->cpf = '•••••••••••';
        }

        // Resto do código index()...
        $generos = $this->genero->all();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();
        $dadosespecificos = $this->getDadosEspecificos($user);

        $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
            ->with(['imagens', 'usuario'])
            ->where('usuario_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $likedPosts = Curtida::with(['postagem.usuario', 'postagem.imagens'])
            ->where('id_usuario', $user->id)
            ->orderByDesc('created_at')
            ->get();

            /*
            $likedComments = Curtida::with([
                'comentario.usuario', 
                'comentario.postagem',
                'comentario.postagem.usuario'
                ])
                ->where('id_usuario', $user->id)
                ->whereNotNull('id_comentario') // Apenas curtidas em comentários
                ->orderByDesc('created_at')
                ->get();

            */

        $postsPopulares = Postagem::withCount('curtidas')
            ->with(['imagens', 'usuario'])
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        $tendenciasPopulares = Tendencia::populares(7)->get();

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
         /*   'likedComments', */
            'postsPopulares',
            'tendenciasPopulares',
            'autista',
            'responsavel'
        ));

    } catch (\Exception $e) {
        Log::error('Erro em conta.index: ' . $e->getMessage());
        return redirect('/feed')->with('error', 'Perfil não encontrado.');
    }
}
    /* Obtém os dados específicos baseados no tipo de usuário */
    private function getDadosEspecificos(Usuario $user)
    {
        if (!$user) return null;

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

    public function Conta() {}
}