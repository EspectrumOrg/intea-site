<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutistaController;
use App\Http\Controllers\ChatPrivadoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ComunidadeController;
use App\Http\Controllers\CurtidaPostagemController;
use App\Http\Controllers\DenunciaPostagemController;
use App\Http\Controllers\DenunciaUsuarioController;
use App\Http\Controllers\GruposControler;
use App\Http\Controllers\PostagemController;
use App\Http\Controllers\ProfissionalSaudeController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeguirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PusherController;
use App\Models\ProfissionalSaude;
use App\Mail\Contato;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!

    Register
|
*/


// Início
Route::get('/', function () {
    return view('landpage');
})->name('landpage');

Route::post('/contato', [ContatoController::class, 'store'])->name('contato.store');

// somente para quem não está logado
Route::get('/login', function () { // Login
    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/cadastro', function () { // Tipo Conta
    return view('auth.register');
})->middleware('guest')->name('cadastro.index');

Route::get('/grupo', [GruposControler::class, 'exibirGrupos'])->name('grupo.index');
Route::post('/grupo/entrar/{grupoId}', [GruposControler::class, 'entrarNoGrupo'])->name('grupo.entrar');

Route::post('/broadcast', [PusherController::class, 'broadcast']);
Route::post('/receive', [PusherController::class, 'receive']);
Route::get('/chat', [PusherController::class, 'index']);
Route::post('/enviar-mensagem', [ChatPrivadoController::class, 'enviarMensagem']);




// Cadastro de Autista
Route::resource("autista", AutistaController::class)->names("autista");
// Cadastro de Comunidade
Route::resource("comunidade", ComunidadeController::class)->names("comunidade");
// Cadastro de Profissional de Saúde
Route::resource("profissional", ProfissionalSaudeController::class)->names("profissional");
// Cadastro de Responsável
Route::resource("responsavel", ResponsavelController::class)->names("responsavel");


//arrumar rotas depois 
Route::get('/grupo', [GruposControler::class, 'exibirGrupos'])->name('grupo.index');
Route::post('/grupo/entrar/{grupoId}', [GruposControler::class, 'entrarNoGrupo'])->name('grupo.entrar');


Route::post('/broadcast', [PusherController::class, 'broadcast']);
Route::post('/receive', [PusherController::class, 'receive']);
Route::get('/chat', [PusherController::class, 'index']);

/* Sua Parte Nicola ------------------ 
Route::get('/cadastro/responsavel', function () {
    return view('cadastro.create-responsavel');
})->name('cadastro.responsavel');

Route::get('/perfilResponsavel', [ResponsavelController::class, 'perfil'])->name('perfilr');
Route::get('/cadastro/responsavel', [ResponsavelController::class, 'create'])->name('cadastro.responsavel');
Route::post('/cadastro', [ResponsavelController::class, 'store'])->name('cadastro.store.responsavel');
*/


// Usuário Logado PADRÃO
Route::middleware('auth')->group(function () {

    Route::resource("feed", PostagemController::class)
        ->names("post")
        ->parameters(["feed" => "post"]);
    // curtida postagem
    Route::post('/feed/{id}/curtida', [CurtidaPostagemController::class, 'toggleCurtida'])->name('post.curtida');
    // comentario postagem e reply comentário
    Route::post('/feed/{tipo}/{id}', [ComentarioController::class, 'store'])->name('post.comentario');
    Route::get('/feed/{id}/foco', [ComentarioController::class, 'focus'])->name('comentario.focus');
    Route::get('/feed/{postagem}', [PostagemController::class, 'show'])->name('post.read');
    // denuncia postagem
    Route::post('/feed/{id_postagem}/denuncia/{id_usuario}', [DenunciaPostagemController::class, 'post'])->name('post.denuncia');


    Route::post('/seguir/{user}', [SeguirController::class, 'store'])->name('seguir.store')->middleware('auth');

    Route::post('/seguir', [SeguirController::class, 'store'])->name('seguir.store');


    //Mensagem
    Route::get('/mensagem', function () {
        return view('mensagens.painelmensagem');
    })->name('cadastro.index');

    Route::get('/conta/{usuario_id}', [ContaController::class, 'index'])->name('conta.index');
    // denuncia usuário
    Route::post('/conta/{id_usuario_denunciado}/denuncia/{id_usuario_denunciante}', [DenunciaUsuarioController::class, 'post'])->name('usuario.denuncia');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Profissional de Saúde Logado 
Route::middleware('auth', 'is_profissional')->group(function () {

    Route::get('/pagina_saude', function () {
        return view('paginas/profissional_saude/inicio_profissional_saude');
    })
        ->name('pagina_saude');
});


// Apenas Admin
Route::middleware(['auth', 'is_admin'])->group(function () {

    // Cadastro de Admin
    Route::resource("admin", AdminController::class)->names("admin");
    // Usuário
    Route::resource("usuario", UsuarioController::class)
        ->names("usuario")
        ->parameters(["usuario" => "usuarios"]);
    Route::delete('/usuario/{usuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
    Route::patch('/usuarios/{usuario}/desbanir', [UsuarioController::class, 'desbanir'])->name('usuario.desbanir');
    // Denúncia postagem
    Route::resource("denuncia", DenunciaPostagemController::class)
        ->names("denuncia")
        ->parameters(["denuncia" => "denuncias"]);
    Route::delete('/denuncia/{denuncia}', [DenunciaPostagemController::class, 'destroy'])->name('denuncia.destroy');
    Route::put('/denuncia/{denuncia}/resolve', [DenunciaPostagemController::class, 'resolve'])->name('denuncia.resolve');
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('auth')
        ->name('dashboard.index');
});



Route::post('/grupo/inserir', [GruposControler::class, 'criarGrupo'])->name('grupos.inserir');

/*Rota para o novo sistema de perfil com 3 abas (usa ContaController)*/
Route::get('/perfil/{usuario_id?}', [ContaController::class, 'show'])->name('profile.show');




require __DIR__ . '/auth.php';
