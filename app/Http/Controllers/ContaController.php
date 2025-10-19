<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Postagem;
use App\Models\CurtidaPostagem;
use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Models\Autista;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
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
            // Se não for passado ID, mostra o perfil do usuário logado
            $responsavel = Responsavel::where('usuario_id', auth()->id())->firstOrFail();
            $autista = Autista::where('responsavel_id', $responsavel->id)->firstOrFail();

            $user = $usuario_id ? Usuario::findOrFail($usuario_id) : auth()->user();
            
            $generos = $this->genero->all();
            $telefones = $this->telefone->where('usuario_id', $user->id)->get();
            
            // Dados específicos do tipo de usuário
            $dadosespecificos = $this->getDadosEspecificos($user);

            // Postagens do usuário
            $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
                ->with(['imagens', 'usuario'])
                ->where('usuario_id', $user->id)
                ->orderByDesc('created_at')
                ->get();

            // Postagens curtidas pelo usuário
            $likedPosts = CurtidaPostagem::with([
                'postagem.usuario', 
                'postagem.imagens'
            ])
            ->where('id_usuario', $user->id)
            ->orderByDesc('created_at')
            ->get();

            // Posts populares para sidebar
            $postsPopulares = Postagem::withCount('curtidas')
                ->with(['imagens', 'usuario'])
                ->orderByDesc('curtidas_count')
                ->take(5)
                ->get();

            return view('profile.show', compact(
                'user', 
                'generos', 
                'telefones', 
                'dadosespecificos', 
                'userPosts', 
                'likedPosts',
                'postsPopulares',
                'autista',
                'responsavel'
            ));

        } catch (\Exception $e) {
            Log::error('Erro no ContaController: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar perfil.');
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