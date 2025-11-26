<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Banimento;
use App\Models\BanimentoConfirmacao;
use App\Models\BanimentoReconsideracao;
use App\Models\ChatPrivado;
use App\Models\Denuncia;
use App\Models\seguirModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    private $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }



    public function Conversas()
    {
        $usuarioLogado = Auth::id();

        $conversas = ChatPrivado::where('usuario1_id', $usuarioLogado)
            ->orWhere('usuario2_id', $usuarioLogado)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('conversas', compact('conversas', 'usuarioLogado'));
    }

    public function teste()
    {
        $usuarioLogado = Auth::id();

        // Busca todas as conversas que envolvem o usuário logado
        $conversas = ChatPrivado::where('usuario1_id', $usuarioLogado)
            ->orWhere('usuario2_id', $usuarioLogado)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Busca todos os IDs dos usuários que o logado está seguindo
        $seguindoIds = seguirModel::where('segue_id', $usuarioLogado)
            ->pluck('seguindo_id');

        // Busca os dados desses usuários
        $usuariosSeguindo = Usuario::whereIn('id', $seguindoIds)->get();

        return view('feed.chats.conversas', compact('conversas', 'usuariosSeguindo', 'usuarioLogado'));
    }

    public function index(Request $request)
    {
        $query = $this->usuario->query();

        // Busca por apelido, user ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('apelido', 'like', "%{$search}%")
                    ->orWhere('user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo_usuario
        if ($request->filled('tipo_usuario')) {
            $query->where('tipo_usuario', $request->tipo_usuario);
        }

        // Filtro por status_conta
        if ($request->filled('status_conta')) {
            $query->where('status_conta', $request->status_conta);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc'); // padrão: mais recente
        $query->orderBy('created_at', $ordem);

        $usuario = $query->paginate(10)->appends($request->all());

        return view('admin.usuario.index', compact('usuario'));
    }

    public function update_privacidade(Request $request)
    {

        $request->validate([
            'visibilidade' => 'required|in:0,1',
        ]);

        $request->user()->visibilidade = $request->visibilidade;
        $request->user()->save();

        return redirect()->back()->with('success', 'Configurações de privacidade atualizadas com sucesso.');
    }

    public function buscarUsuarios(Request $request)
    {
        $usuarioId = auth()->id() ?? 0;
        $search = trim($request->input('q', ''));

        // Se o campo estiver vazio, retorna vazio
        if ($search === '') {
            return response()->json([]);
        }

        // Se começar com '#', busca tendência em vez de usuário
        if (substr($search, 0, 1) === '#') {
            $termo = strtolower(str_replace('#', '', $search));

            $tendencias = \App\Models\Tendencia::where(DB::raw('LOWER(hashtag)'), 'like', "%{$termo}%")
                ->orWhere(DB::raw('LOWER(slug)'), 'like', "%{$termo}%")
                ->orderBy('contador_uso', 'desc')
                ->get(['id', 'hashtag as nome', 'slug', 'contador_uso']);

            return response()->json(
                $tendencias->map(function ($t) {
                    return [
                        'id' => $t->slug, // usamos o slug na URL
                        'user' =>  $t->nome, // pra aparecer com hashtag
                        'apelido' => $t->contador_uso . ' usos',
                        'foto' => null, // tendência não tem foto
                        'tipo' => 'tendencia'
                    ];
                })
            );
        }

        // Caso contrário, busca usuários normalmente
        $usuarios = \App\Models\Usuario::where('id', '!=', $usuarioId)
            ->where('status_conta', 1)
            ->where(function ($q) use ($search) {
                $q->where('user', 'like', "%{$search}%")
                    ->orWhere('apelido', 'like', "%{$search}%");
            })
            ->orderBy('user', 'asc')
            ->get(['id', 'user', 'apelido', 'foto']);

        return response()->json($usuarios);
    }


    public function destroy(Request $request, $id)
    {
        if ($id != 1) {
            $usuario = Usuario::findOrFail($id);

            // Registra a mensagem de banimento -------------(Importante!)
            $banimento = Banimento::create([
                'id_usuario' => $usuario->id,
                'id_admin' => auth()->id(),
                'infracao' => $request->infracao,
                'motivo' => $request->motivo,
                'id_postagem' => $request->id_postagem,
                'id_comentario' => $request->id_comentario,
            ]);

            // Enviar email pro user
            Mail::to($usuario->email)->send(new \App\Mail\BanimentoMail($banimento));

            $usuarioDenunciante = Usuario::findOrFail($request->denunciante);

            $banimentoConfirmacao = BanimentoConfirmacao::create([
                'id_usuario' => $usuarioDenunciante->id,
                'id_usuario_banido' => $usuario->id,
                'id_admin' => auth()->id(),
                'infracao' => $request->infracao,
            ]);

            // Enviar email pro user que fez a denuncia
            Mail::to($usuarioDenunciante->email)->send(new \App\Mail\BanimentoConfirmacaoMail($banimentoConfirmacao));

            // Marca usuário como banido
            $usuario->status_conta = 2;

            // 1 - Denúncias onde o DENUNCIADO é o usuário (denúncia direta)
            Denuncia::where('id_usuario_denunciado', $usuario->id)
                ->update(['status_denuncia' => 'resolvida']);

            // 2 - Denúncias feitas POR esse usuário
            Denuncia::where('id_usuario_denunciante', $usuario->id)
                ->update(['status_denuncia' => 'resolvida']);

            // 3 - Denúncias feitas contra POSTAGENS do usuário
            Denuncia::whereIn('id_postagem', $usuario->postagens()->pluck('id'))
                ->update(['status_denuncia' => 'resolvida']);

            // 4 - Denúncias feitas contra COMENTÁRIOS do usuário
            Denuncia::whereIn('id_comentario', $usuario->comentarios()->pluck('id'))
                ->update(['status_denuncia' => 'resolvida']);

            $usuario->save();

            session()->flash("warning", "Usuário banido, conteúdo removido do site.");
            return redirect()->back();
        } else { //Caso Administrador Chefe
            session()->flash("error", "O usuário principal não pode ser banido!");
            return redirect()->back();
        }
    }

    public function desbanir($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->status_conta = 1;
        $usuario->save();

        // Registra a mensagem de reconsideracao -------------(Importante!)
        $banimentoReconsideracao = BanimentoReconsideracao::create([
            'id_usuario' => $usuario->id,
            'id_admin' => auth()->id()
        ]);

        // Enviar email pro user
        Mail::to($usuario->email)->send(new \App\Mail\BanimentoReconsideracaoMail($banimentoReconsideracao));

        session()->flash("success", "Usuário desbanido");

        return redirect()->back();
    }

    public function excluirConta(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());

        if ($usuario->id != 1) {

            $request->validate(
                [
                    'password' => ['required', 'current_password'],
                ],
                [
                    'password.current_password' => 'senha inválida',
                ]
            );

            $usuario->comentarios()->delete();

            $postagens = $usuario->postagens()->with('tendencias', 'curtidas')->get();

            foreach ($postagens as $postagem) {

                foreach ($postagem->tendencias as $tendencia) {
                    $tendencia->contador_uso = max($tendencia->contador_uso - 1, 0);
                    $tendencia->save();
                }

                $postagem->curtidas()->delete();

                $postagem->tendencias()->detach();

                $postagem->delete();
            }

            $tendencias = \App\Models\Tendencia::whereIn(
                'id',
                $postagens->pluck('tendencias.*.id')->flatten()
            )->get();

            foreach ($tendencias as $tendencia) {
                if ($tendencia->postagens()->count() === 0) {
                    $tendencia->delete();
                }
            }

            $usuario->status_conta = 0;
            $usuario->save();

            // Fazer logout e invalidar sessão
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Sua conta foi excluída com sucesso.');
        } else {
            return back()->with('error', 'A conta principal não pode ser exclúida');
        }
    }
}
