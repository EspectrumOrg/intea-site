<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutistaController;
use App\Http\Controllers\ComentarioPostagemController;
use App\Http\Controllers\DenunciaPostagemController;
use App\Http\Controllers\ComunidadeController;
use App\Http\Controllers\CurtidaPostagemController;
use App\Http\Controllers\PostagemController;
use App\Http\Controllers\ProfissionalSaudeController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ProfileController;
use App\Models\ProfissionalSaude;
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
// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
// Tipo Conta
Route::get('/cadastro', function () {
    return view('auth.register');
})->name('cadastro.index');

// Cadastro de Admin
Route::resource("admin", AdminController::class)->names("admin");
// Cadastro de Autista
Route::resource("autista", AutistaController::class)->names("autista");
// Cadastro de Comunidade
Route::resource("comunidade", ComunidadeController::class)->names("comunidade");
// Cadastro de Profissional de Saúde
Route::resource("profissional", ProfissionalSaudeController::class)->names("profissional");
// Cadastro de Responsável
Route::resource("responsavel", ResponsavelController::class)->names("responsavel");

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
    // comentario postagem
    Route::post('/feed/{id}/comentario', [ComentarioPostagemController::class, 'store'])->name('post.comentario');
    // denuncia postagem
    Route::post('/feed/{id_postagem}/denuncia/{id_usuario}', [DenunciaPostagemController::class, 'post'])->name('post.denuncia');
    //Mensagem
    Route::get('/mensagem', function () {
        return view('mensagens.painelmensagem');
    })->name('cadastro.index');

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

    Route::resource("usuario", UsuarioController::class)
        ->names("usuario")
        ->parameters(["usuario" => "usuarios"]);
    Route::delete('/usuario/{usuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
    Route::patch('/usuarios/{usuario}/desbanir', [UsuarioController::class, 'desbanir'])->name('usuario.desbanir');

    Route::resource("denuncia", DenunciaPostagemController::class)
        ->names("denuncia")
        ->parameters(["denuncia" => "denuncias"]);
    Route::delete('/denuncia/{denuncia}', [DenunciaPostagemController::class, 'destroy'])->name('denuncia.destroy');
});

require __DIR__ . '/auth.php';