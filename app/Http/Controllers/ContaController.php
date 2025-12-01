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
use App\Models\Interesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

    /**
     * Método principal para exibir perfil (próprio ou de outro usuário)
     */
    public function show($usuario_id = null)
    {
        try {
            $user = $usuario_id ? Usuario::findOrFail($usuario_id) : auth()->user();

            if (!$user) {
                return redirect('/feed')->with('error', 'Usuário não encontrado.');
            }

            $currentUser = auth()->user();

            // Proteção de CPF
            if (!$currentUser || ($currentUser->id != $user->id && $currentUser->tipo_usuario != 1)) {
                $user->cpf = '•••••••••••';
            }

            $generos = $this->genero->all();
            $telefones = $this->telefone->where('usuario_id', $user->id)->get();
            $dadosespecificos = $this->getDadosEspecificos($user);

            $seguindo = $user->seguindo()->get();
            $seguidores = $user->seguidores()->get();

            // 📌 Interesses do usuário
            $interessesUsuario = $user->interesses()
                ->withCount(['seguidores', 'postagens'])
                ->get();

            // 📌 Postagens do usuário
            $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
                ->with(['imagens', 'usuario', 'interesses'])
                ->where('usuario_id', $user->id)
                ->orderByDesc('created_at')
                ->get();

            // 📌 Postagens curtidas pelo usuário
            $likedPosts = Curtida::with(['postagem.usuario', 'postagem.imagens', 'postagem.interesses'])
                ->where('id_usuario', $user->id)
                ->orderByDesc('created_at')
                ->get();

            // 📌 Postagens mais populares
            $postsPopulares = Postagem::withCount('curtidas')
                ->with(['imagens', 'usuario', 'interesses'])
                ->orderByDesc('curtidas_count')
                ->take(5)
                ->get();

            $tendenciasPopulares = Tendencia::populares(7)->get();

            // 🔥 Relação RESPONSÁVEL → AUTISTA usando tabela pivot
            $responsavel = null;
            $autistas = null;

            if ($user->tipo_usuario == 5) {
                $responsavel = Responsavel::where('usuario_id', $user->id)->first();

                if ($responsavel) {
                    $autistas = $responsavel->autistas()->get();
                }
            }

            // Cálculo de idade
            $idade = null;
            $maiorDeIdade = false;
            if ($user->data_nascimento) {
                $idade = Carbon::parse($user->data_nascimento)->age;
                $maiorDeIdade = $idade >= 18;
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
                'autistas',
                'responsavel',
                'seguindo',
                'seguidores',
                'interessesUsuario',
                'idade',
                'maiorDeIdade'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar perfil: ' . $e->getMessage());
            return redirect('/feed')->with('error', 'Erro ao carregar o perfil.');
        }
    }

    /**
     * Método alternativo para exibir perfil de outro usuário
     * Mantido para compatibilidade com rotas existentes
     */
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

            $generos = $this->genero->all();
            $telefones = $this->telefone->where('usuario_id', $user->id)->get();
            $dadosespecificos = $this->getDadosEspecificos($user);
            $seguindo = $user->seguindo()->get();
            $seguidores = $user->seguidores()->get();

            // 📌 Interesses do usuário
            $interessesUsuario = $user->interesses()
                ->withCount(['seguidores', 'postagens'])
                ->get();

            // 📌 Postagens do usuário
            $userPosts = Postagem::withCount(['curtidas', 'comentarios'])
                ->with(['imagens', 'usuario', 'interesses'])
                ->where('usuario_id', $user->id)
                ->orderByDesc('created_at')
                ->get();

            // 📌 Postagens curtidas pelo usuário
            $likedPosts = Curtida::with(['postagem.usuario', 'postagem.imagens', 'postagem.interesses'])
                ->where('id_usuario', $user->id)
                ->orderByDesc('created_at')
                ->get();

            // 📌 Postagens mais populares
            $postsPopulares = Postagem::withCount('curtidas')
                ->with(['imagens', 'usuario', 'interesses'])
                ->orderByDesc('curtidas_count')
                ->take(5)
                ->get();

            $tendenciasPopulares = Tendencia::populares(7)->get();

            // 🔥 Relação RESPONSÁVEL → AUTISTA
            $responsavel = null;
            $autistas = null;
            
            if ($user->tipo_usuario == 5) {
                $responsavel = Responsavel::where('usuario_id', $user->id)->first();
                if ($responsavel) {
                    $autistas = $responsavel->autistas()->get();
                }
            }

            // Cálculo de idade
            $idade = null;
            $maiorDeIdade = false;
            if ($user->data_nascimento) {
                $idade = Carbon::parse($user->data_nascimento)->age;
                $maiorDeIdade = $idade >= 18;
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
                'autistas',
                'responsavel',
                'seguindo',
                'seguidores',
                'interessesUsuario',
                'idade',
                'maiorDeIdade'
            ));

        } catch (\Exception $e) {
            Log::error('Erro em conta.index: ' . $e->getMessage());
            return redirect('/feed')->with('error', 'Perfil não encontrado.');
        }
    }

    /**
     * Obtém os dados específicos baseados no tipo de usuário
     */
    private function getDadosEspecificos(Usuario $user)
    {
        if (!$user) return null;

        return match($user->tipo_usuario) {
            2 => $user->autista,
            4 => $user->profissionalsaude,
            5 => $user->responsavel,
            default => null,
        };
    }
}