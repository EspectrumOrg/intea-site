<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TelefoneController;
use App\Http\Controllers\TendenciaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutistaController;
use App\Http\Controllers\ChatPrivadoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ComunidadeController;
use App\Http\Controllers\CurtidaController;
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\GruposControler;
use App\Http\Controllers\PostagemController;
use App\Http\Controllers\ProfissionalSaudeController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeguirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PusherController;
use App\Mail\Contato;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\InteresseController;
use App\Http\Controllers\PreferenciasUsuarioController;
use App\Http\Controllers\ModeracaoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui você registra as rotas da aplicação.
|
*/

// Início
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('post.index');
    }
    return view('landpage');
})->name('landpage');

Route::get('/teste', function () {
    return view('teste');
})->name('teste');

Route::post('/contato', [ContatoController::class, 'store'])->name('contato.store');

Route::get('/feed/configuracao/config', function () {
    $user = Auth::user();
    return view(
        'feed.configuracao.config',
        compact('user')
    );
})->name('configuracao.config');

// NÃO MEXA, ÁREA DE MONOCROMATICO!
Route::post('/update-theme-preference', function (Illuminate\Http\Request $request) {
    try {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado']);
        }

        $user = Auth::user();
        $user->tema_preferencia = $request->tema_preferencia;
        $user->save();

        Log::info('Preferência de tema atualizada', [
            'user_id' => $user->id,
            'tema_preferencia' => $request->tema_preferencia
        ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('Erro ao atualizar preferência de tema: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
})->middleware('auth');

// somente para quem não está logado
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/cadastro', function () {
    return view('auth.register');
})->middleware('guest')->name('cadastro.index');

// Cadastro de Autista
Route::resource("autista", AutistaController::class)->names("autista");
// Cadastro de Comunidade
Route::resource("comunidade", ComunidadeController::class)->names("comunidade");
// Cadastro de Profissional de Saúde
Route::resource("profissional", ProfissionalSaudeController::class)->names("profissional");
// Cadastro de Responsável
Route::resource("responsavel", ResponsavelController::class)->names("responsavel");

// ========== ROTAS PÚBLICAS ==========
Route::get('/perfil/{usuario_id?}', [ContaController::class, 'show'])->name('profile.show');
Route::get('/tendencias', [TendenciaController::class, 'index'])->name('tendencias.index');
Route::get('/tendencias/{slug}', [TendenciaController::class, 'show'])->name('tendencias.show');
Route::get('/api/tendencias/populares', [TendenciaController::class, 'apiPopulares'])->name('api.tendencias.populares');
Route::get('/api/tendencias/search', [TendenciaController::class, 'search'])->name('api.tendencias.search');
Route::get('/api/tendencias', [TendenciaController::class, 'apiTendencias'])->name('api.tendencias');

// API pública para interesses
Route::get('/api/interesses/slug/{slug}', function ($slug) {
    $interesse = \App\Models\Interesse::where('slug', $slug)->first();
    if (!$interesse) {
        return response()->json(['error' => 'Interesse não encontrado'], 404);
    }
    return response()->json([
        'id' => $interesse->id,
        'nome' => $interesse->nome,
        'slug' => $interesse->slug
    ]);
});

// ========== ROTAS EXCLUÍDAS DO MIDDLEWARE DE ONBOARDING ==========
Route::middleware(['auth'])->group(function () {
    // Rotas de onboarding (acessíveis mesmo com onboarding não concluído)
    Route::get('/onboarding', [PreferenciasUsuarioController::class, 'onboarding'])->name('onboarding');
    Route::post('/onboarding/salvar', [PreferenciasUsuarioController::class, 'salvarOnboarding'])->name('onboarding.salvar');
    Route::post('/onboarding/pular', [PreferenciasUsuarioController::class, 'pularOnboarding'])->name('onboarding.pular');
    
    // Logout
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// ========== USUÁRIO LOGADO PADRÃO (COM ONBOARDING OBRIGATÓRIO) ==========
Route::middleware(['auth', 'check.ban', 'check.onboarding'])->group(function () {

    // ========== INTERESSES ==========
    Route::get('/interesses', [InteresseController::class, 'index'])->name('interesses.index');
    Route::get('/interesses/criar', [InteresseController::class, 'create'])->name('interesses.create');
    Route::post('/interesses', [InteresseController::class, 'store'])->name('interesses.store');
    Route::get('/interesses/pesquisar', [InteresseController::class, 'pesquisar'])->name('interesses.pesquisar');
    Route::get('/interesses/{slug}', [InteresseController::class, 'show'])->name('interesses.show');
    Route::post('/interesses/{id}/seguir', [InteresseController::class, 'seguir'])->name('interesses.seguir');
    Route::post('/interesses/{id}/deixar-seguir', [InteresseController::class, 'deixarSeguir'])->name('interesses.deixar-seguir');
    Route::get('/interesses/sugeridos', [InteresseController::class, 'sugeridos'])->name('interesses.sugeridos');

    // ========== SISTEMA DE DONOS E GERENCIAMENTO DE INTERESSES ==========
    Route::get('/interesses/{slug}/editar', [InteresseController::class, 'edit'])->name('interesses.edit')->middleware('auth');
    Route::put('/interesses/{slug}', [InteresseController::class, 'update'])->name('interesses.update')->middleware('auth');
    Route::delete('/interesses/{slug}', [InteresseController::class, 'destroy'])->name('interesses.destroy')->middleware('auth');
    Route::get('/interesses/{slug}/moderadores', [InteresseController::class, 'moderadores'])->name('interesses.moderadores')->middleware('auth');
    Route::post('/interesses/{slug}/adicionar-moderador', [InteresseController::class, 'adicionarModerador'])->name('interesses.adicionar-moderador')->middleware('auth');
    Route::delete('/interesses/{slug}/remover-moderador', [InteresseController::class, 'removerModerador'])->name('interesses.remover-moderador')->middleware('auth');
    Route::post('/interesses/{slug}/transferir-propriedade', [InteresseController::class, 'transferirPropriedade'])->name('interesses.transferir-propriedade')->middleware('auth');
    Route::get('/api/interesses/todos', [InteresseController::class, 'todosInteresses']);

    // Rota para servir arquivos no Windows (onde storage:link não funciona)
    Route::get('/storage/serve/{path}', function($path) {
        $storagePath = storage_path('app/public/' . $path);
        
        if (!file_exists($storagePath)) {
            abort(404);
        }
        
        return response()->file($storagePath);
    })->where('path', '.*')->name('storage.serve');

    // Feeds por interesse
    Route::get('/seguindo', [PostagemController::class, 'seguindo'])->name('post.seguindo');
    Route::get('/personalizado', [PostagemController::class, 'personalizado'])->name('post.personalizado');
    Route::get('/interesse/{slug}', [PostagemController::class, 'porInteresse'])->name('post.interesse');
    Route::get('/i/{slug}', [PostagemController::class, 'porInteresse']);

    // ========== FEED E POSTAGENS ==========
    Route::get('/feed/seguindo', [PostagemController::class, 'seguindo'])->name('post.seguindo');

    // Feed e postagens
    Route::resource("feed", PostagemController::class)
        ->names("post")
        ->parameters(["feed" => "post"]);
    Route::post('/feed/curtida', [CurtidaController::class, 'toggleCurtida'])->name('curtida.toggle');
    Route::get('/feed/{postagem}', [PostagemController::class, 'show'])->name('post.read');

    // Comentários
    Route::resource("comentario", ComentarioController::class)->names('comentario');
    Route::post('/feed/{tipo}/{id}', [ComentarioController::class, 'store'])->name('post.comentario');
    Route::get('/feed/{id}/foco', [ComentarioController::class, 'focus'])->name('comentario.focus');
    Route::post('/feed/{id}', [ComentarioController::class, 'store'])->name('comentario.curtida');
    Route::delete('/comentario/{id}/destroy', [ComentarioController::class, 'destroy'])->name('comentario.destroy');
    Route::put('/comentario/{comentario}', [ComentarioController::class, 'update'])->name('comentario.update');

    Route::get('/buscar', [UsuarioController::class, 'buscarUsuarios'])->name('buscar.usuarios');
    Route::get('/notificacao', [NotificacaoController::class, 'index'])->name('notificacao.index');

    // Grupo
    Route::get('/grupo', [GruposControler::class, 'exibirGrupos'])->name('grupo.index');
    Route::post('/grupo/entrar/{grupoId}', [GruposControler::class, 'entrarNoGrupo'])->name('grupo.entrar');
    Route::post('/grupo/criar', [GruposControler::class, 'criarGrupo'])->name('grupos.inserir');

    Route::get('/chat-test', function () {
        return view('chat-test');
    });

    // Denúncias
    Route::post('/denuncia', [DenunciaController::class, 'store'])->name('denuncia.store');
    Route::resource("denuncia", DenunciaController::class)
        ->names("denuncia")
        ->parameters(["denuncia" => "denuncias"]);

    // Seguir
    Route::post('/seguir/{user}', [SeguirController::class, 'store'])->name('seguir.store');
    Route::post('/seguir', [SeguirController::class, 'store'])->name('seguir.store');
    Route::delete('/seguir/{user}', [SeguirController::class, 'destroy'])->name('seguir.destroy');
    Route::delete('/seguir/pedido/{user}', [SeguirController::class, 'cancelarPedido'])->name('seguir.cancelar');

    // Mensagens
    Route::get('/mensagem', function () {
        return view('mensagens.painelmensagem');
    })->name('cadastro.index');

    // Conta e denúncias de usuário
    Route::get('/conta/{usuario_id}', [ContaController::class, 'index'])->name('conta.index');
    Route::post('/conta/{id_usuario_denunciado}/denuncia/{id_usuario_denunciante}', [DenunciaController::class, 'post'])->name('usuario.denuncia');
    Route::delete('/usuario/excluir', [UsuarioController::class, 'excluirConta'])
        ->name('usuario.excluir');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/usuario/{id}/seguindo/count', [SeguirController::class, 'countSeguindo']);
    Route::get('/usuario/{id}/seguidores/count', [SeguirController::class, 'countSeguidores']);
    Route::get('/usuario/{id}/seguindo', [SeguirController::class, 'listarSeguindo'])->name('usuario.listar.seguindo');
    Route::get('/usuario/{id}/seguidores', [SeguirController::class, 'listarSeguidores'])->name('usuario.listar.seguidores');

    Route::get('/buscar-usuarios-chat', [ChatPrivadoController::class, 'buscarUsuarioschat'])->name('buscar.usuarios.chat');
    Route::get('/conversas', [UsuarioController::class, 'teste'])->name('teste');
    Route::get('/chat', [PusherController::class, 'webzap'])->name('chat.dashboard');
    Route::get('/chat/carregar', [PusherController::class, 'carregarChat'])->name('chat.carregar');
    Route::post('/broadcast', [PusherController::class, 'broadcast'])->name('broadcast');

    // Atualizar visibilidade de usuário
    Route::patch('/usuario/update-privacidade', [\App\Http\Controllers\UsuarioController::class, 'update_privacidade'])
        ->name('usuario.update_privacidade');
        
    // Notificações
    Route::get('/notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/{id}/aceitar', [NotificacaoController::class, 'aceitar'])->name('notificacoes.aceitar');
    Route::delete('/notificacao/{id}', [NotificacaoController::class, 'destroy'])->name('notificacao.destroy');
    Route::delete('/notificacoes/{id}', [NotificacaoController::class, 'recusar'])->name('notificacoes.recusar');
    
    // ========== SISTEMA DE MODERAÇÃO ==========
    Route::prefix('moderacao')->group(function () {
        // Painéis
        Route::get('/interesse/{slugInteresse}', [ModeracaoController::class, 'painel'])->name('moderacao.painel');
        Route::get('/global', [ModeracaoController::class, 'painelGlobal'])->name('moderacao.global');

        // Postagens
        Route::post('/postagens/{postagemId}/remover', [ModeracaoController::class, 'removerPostagem'])->name('moderacao.postagens.remover');
        Route::post('/postagens/{postagemId}/restaurar', [ModeracaoController::class, 'restaurarPostagem'])->name('moderacao.postagens.restaurar');
        Route::post('/postagens/acao-em-massa', [ModeracaoController::class, 'acaoEmMassaPostagens'])->name('moderacao.postagens.acao-em-massa');

        // Palavras Proibidas
        Route::post('/interesse/{interesseId}/palavras-proibidas', [ModeracaoController::class, 'adicionarPalavraProibida'])->name('moderacao.palavras-proibidas.adicionar');
        Route::post('/palavras-proibidas-globais', [ModeracaoController::class, 'adicionarPalavraProibidaGlobal'])->name('moderacao.palavras-proibidas-globais.adicionar');
        Route::delete('/palavras-proibidas/{palavraId}', [ModeracaoController::class, 'removerPalavraProibida'])->name('moderacao.palavras-proibidas.remover');
        Route::delete('/palavras-proibidas-globais/{palavraId}', [ModeracaoController::class, 'removerPalavraProibidaGlobal'])->name('moderacao.palavras-proibidas-globais.remover');

        // Usuários
        Route::post('/interesse/{interesseId}/expulsar', [ModeracaoController::class, 'expulsarUsuario'])->name('moderacao.usuarios.expulsar');
        Route::post('/usuarios/{usuarioId}/banir-sistema', [ModeracaoController::class, 'banirUsuarioSistema'])->name('moderacao.usuarios.banir-sistema');

        // Infrações
        Route::get('/infracoes/pendentes', [ModeracaoController::class, 'listarInfracoesPendentes'])->name('moderacao.infracoes.pendentes');
        Route::post('/infracoes/{infracaoId}/verificar', [ModeracaoController::class, 'verificarInfracao'])->name('moderacao.infracoes.verificar');

        // Estatísticas
        Route::get('/estatisticas/interesse/{interesseId}', [ModeracaoController::class, 'obterEstatisticasInteresse'])->name('moderacao.estatisticas.interesse');
        Route::get('/estatisticas/globais', [ModeracaoController::class, 'obterEstatisticasGlobais'])->name('moderacao.estatisticas.globais');
        Route::post('/relatorios', [ModeracaoController::class, 'gerarRelatorioModeracao'])->name('moderacao.relatorios.gerar');

        // Processamento Automático
        Route::post('/processar-banimentos-automaticos', [ModeracaoController::class, 'processarBanimentosAutomaticos'])->name('moderacao.banimentos.automaticos');

        // Debug (remover após testes)
        Route::get('/debug/permissoes', [ModeracaoController::class, 'debugPermissoes'])->name('moderacao.debug.permissoes');
    });
});

// ========== USUÁRIOS ESPECÍFICOS (SEM ONBOARDING OBRIGATÓRIO) ==========

// Profissional de Saúde Logado
Route::middleware(['auth', 'check.ban', 'is_profissional'])->group(function () {
    Route::get('/pagina_saude', function () {
        return view('paginas/profissional_saude/inicio_profissional_saude');
    })->name('pagina_saude');
});

// Responsável Logado
Route::middleware(['auth', 'check.ban', 'is_responsavel'])->group(function () {
    Route::get('/painel-responsavel', [\App\Http\Controllers\ResponsavelPainelController::class, 'edit'])->name('responsavel.painel');
    Route::get('/autistas/{id}/editar', [App\Http\Controllers\AutistaController::class, 'edit_responsavel'])->name('autistas.edit_responsavel');
    Route::patch('/autistas/{id}', [App\Http\Controllers\AutistaController::class, 'update_responsavel'])->name('autistas.update_responsavel');
    Route::delete('/dependente/remover', [ResponsavelController::class, 'removeDependente'])->name('dependente.remover');
    
    // rotas para adicionar dependente via responsavel
    Route::post('/responsavel/{id}/adicionar-dependente', [ResponsavelController::class, 'addDependente'])->name('responsavel.adicionar_dependente');
});

// Apenas Admin (sem middleware de onboarding para admins)
Route::middleware(['auth', 'is_admin', 'check.ban'])->group(function () {
    Route::resource("admin", AdminController::class)->names("admin");
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource("usuario", UsuarioController::class)->names("usuario")->parameters(["usuario" => "usuarios"]);
    Route::delete('/usuario/{usuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
    Route::delete('/usuarioDenuncia/{usuario}', [UsuarioController::class, 'destroyDenuncia'])->name('usuario.destroyDenuncia');
    Route::patch('/usuarios/{usuario}/desbanir', [UsuarioController::class, 'desbanir'])->name('usuario.desbanir');
    Route::delete('/denuncia/{denuncia}', [DenunciaController::class, 'banirUsuario'])->name('denuncia.destroy');
    Route::put('/denuncia/{denuncia}/resolve', [DenunciaController::class, 'resolve'])->name('denuncia.resolve');
    Route::get('/suporte', [ContatoController::class, 'index'])->name('contato.index');
    Route::post('/suporte/resposta', [ContatoController::class, 'resposta'])->name('contato.resposta');
});

// ========== ROTAS PARA TELEFONES ==========
Route::middleware(['auth'])->group(function () {
    Route::post('/telefones', [App\Http\Controllers\TelefoneController::class, 'store'])->name('telefones.store');
    Route::put('/telefones/{id}', [App\Http\Controllers\TelefoneController::class, 'update'])->name('telefones.update');
    Route::delete('/telefones/{id}', [App\Http\Controllers\TelefoneController::class, 'destroy'])->name('telefones.destroy');
    Route::post('/telefones/{id}/principal', [App\Http\Controllers\TelefoneController::class, 'setPrincipal'])->name('telefones.principal');
    Route::get('/telefones/{id}/dados', [App\Http\Controllers\TelefoneController::class, 'getDados'])->name('telefones.dados');
});

require __DIR__ . '/auth.php';