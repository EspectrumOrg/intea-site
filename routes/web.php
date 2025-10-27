<?php

use App\Http\Controllers\UsuarioController;
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
use App\Http\Controllers\PusherController;
use App\Mail\Contato;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return view('landpage');
})->name('landpage');

Route::post('/contato', [ContatoController::class, 'store'])->name('contato.store');
//contato via email, tanto para guests quanto logados

Route::get('/feed/configuracao/config', function () {
    $user = Auth::user();
    return view(
        'feed.configuracao.config',
        compact('user')
    );
})->name('configuracao.config');



// somente para quem não está logado --------------------------------------------------------------------------------------------------------------------------------------------------------------+
Route::get('/login', function () { // Login
    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/cadastro', function () { // Tipo Conta
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



// Usuário Logado PADRÃO --------------------------------------------------------------------------------------------------------------------------------------------------------------+
Route::middleware('auth')->group(function () {

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

    Route::get('/buscar', [UsuarioController::class, 'buscarUsuarios'])->name('buscar.usuarios');


    // Grupo
    Route::get('/grupo', [GruposControler::class, 'exibirGrupos'])->name('grupo.index');
    Route::post('/grupo/entrar/{grupoId}', [GruposControler::class, 'entrarNoGrupo'])->name('grupo.entrar');
    Route::post('/grupo/criar', [GruposControler::class, 'criarGrupo'])->name('grupos.inserir');

    Route::get('/chat-test', function () {
        return view('chat-test'); // Se tiver uma view
        // ou
        return file_get_contents(resource_path('views/chat-test.php'));
    });

    // Denúncias
    Route::post('/denuncia', [DenunciaController::class, 'store'])->name('denuncia.store');

    // Seguir
    Route::post('/seguir/{user}', [SeguirController::class, 'store'])->name('seguir.store');
    Route::post('/seguir', [SeguirController::class, 'store'])->name('seguir.store');

    // Mensagens
    Route::get('/mensagem', function () {
        return view('mensagens.painelmensagem');
    })->name('cadastro.index');

    // Conta e denúncias de usuário
    Route::get('/conta/{usuario_id}', [ContaController::class, 'index'])->name('conta.index');
    Route::post('/conta/{id_usuario_denunciado}/denuncia/{id_usuario_denunciante}', [DenunciaController::class, 'post'])->name('usuario.denuncia');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/usuario/{id}/seguindo/count', [SeguirController::class, 'countSeguindo']);
    Route::get('/usuario/{id}/seguidores/count', [SeguirController::class, 'countSeguidores']);
    Route::get('/usuario/{id}/seguindo', [SeguirController::class, 'listarSeguindo'])
        ->name('usuario.listar.seguindo');
    // Lista os usuários que seguem este usuário
    Route::get('/usuario/{id}/seguidores', [SeguirController::class, 'listarSeguidores'])

        ->name('usuario.listar.seguidores');

    Route::get('/buscar-usuarios-chat', [ChatPrivadoController::class, 'buscarUsuarioschat'])->name('buscar.usuarios.chat');

    Route::get('/conversas', [UsuarioController::class, 'teste'])->name('teste');
    Route::get('/chat', [PusherController::class, 'webzap'])->name('chat.dashboard');

    Route::get('/chat/carregar', [PusherController::class, 'carregarChat'])->name('chat.carregar');

    Route::post('/broadcast', [PusherController::class, 'broadcast'])->name('broadcast');

    // Atualizar visibilidade de usuário
    Route::patch('/usuario/update-privacidade', [\App\Http\Controllers\UsuarioController::class, 'update_privacidade'])
        ->name('usuario.update_privacidade');
});


// Profissional de Saúde Logado --------------------------------------------------------------------------------------------------------------------------------------------------------------+
Route::middleware('auth', 'is_profissional')->group(function () {
    Route::get('/pagina_saude', function () {
        return view('paginas/profissional_saude/inicio_profissional_saude');
    })->name('pagina_saude');
});



// Apenas Admin --------------------------------------------------------------------------------------------------------------------------------------------------------------+
Route::middleware(['auth', 'is_admin'])->group(function () {

    // Cadastro de Admin
    Route::resource("admin", AdminController::class)->names("admin");

    // Usuário
    Route::resource("usuario", UsuarioController::class)
        ->names("usuario")
        ->parameters(["usuario" => "usuarios"]);
    Route::delete('/usuario/{usuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
    Route::patch('/usuarios/{usuario}/desbanir', [UsuarioController::class, 'desbanir'])->name('usuario.desbanir');

    // Checagem denúncias
    Route::resource("denuncia", DenunciaController::class)
        ->names("denuncia")
        ->parameters(["denuncia" => "denuncias"]);
    Route::delete('/denuncia/{denuncia}', [DenunciaController::class, 'banirUsuario'])->name('denuncia.destroy');
    Route::put('/denuncia/{denuncia}/resolve', [DenunciaController::class, 'resolve'])->name('denuncia.resolve');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('auth')
        ->name('dashboard.index');
});

// Novo sistema de perfil (3 abas)
Route::get('/perfil/{usuario_id?}', [ContaController::class, 'show'])->name('profile.show');

// Certifique-se de que estas rotas existem:
Route::get('/tendencias/{slug}', [TendenciaController::class, 'show'])->name('tendencias.show');
Route::get('/tendencias', [TendenciaController::class, 'index'])->name('tendencias.index');

Route::get('/api/tendencias', [TendenciaController::class, 'apiTendencias'])->name('api.tendencias');
Route::get('/api/tendencias/search', [TendenciaController::class, 'search'])->name('api.tendencias.search');


// rotas para edição dos dados do autista via responsavel
// routes/web.php

Route::middleware('auth')->group(function () {
    Route::get('/autistas/{id}/editar', [App\Http\Controllers\ResponsavelController::class, 'edit_autista'])->name('autistas.edit_autista');
    Route::patch('/autistas/{id}', [App\Http\Controllers\ResponsavelController::class, 'update_autista'])->name('autistas.update_autista');
});


require __DIR__ . '/auth.php';
