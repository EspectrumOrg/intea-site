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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/index', function () {
    return view('index');
})->name('index');



Route::get('/cadastro', function () {
    return view('auth.register'); // Cadastro de usuário
})->name('cadastro.index');
Route::get('/cadastro/responsavel', function () {
    return view('cadastro.create-responsavel');
})->name('cadastro.responsavel');



// Cadastro de Admin
Route::get('/cadastro/admin', [AdminController::class, 'create'])->name('cadastro.admin');
Route::post('/cadastro', [AdminController::class, 'store'])->name('cadastro.store.admin');
// Cadastro de Autista
Route::get('/cadastro/autista', [AutistaController::class, 'create'])->name('cadastro.autista');
Route::post('/cadastro/autista', [AutistaController::class, 'store'])->name('cadastro.store.autista');
// Cadastro de Comunidade
Route::get('/cadastro/comunidade', [ComunidadeController::class, 'create'])->name('cadastro.comunidade');
Route::post('/cadastro/comunidade', [ComunidadeController::class, 'store'])->name('cadastro.store.comunidade');
// Cadastro de Profissional de Saúde
Route::get('/cadastro/profissionalsaude', [ProfissionalSaudeController::class, 'create'])->name('cadastro.profissionalsaude');
Route::post('/cadastro/profissionalsaude', [ProfissionalSaudeController::class, 'store'])->name('cadastro.store.profissionalsaude');
// Cadastro de Responsável
Route::get('/perfilResponsavel', [ResponsavelController::class, 'perfil'])->name('perfilr');

Route::get('/cadastro/responsavel', [ResponsavelController::class, 'create'])->name('cadastro.responsavel');
Route::post('/cadastro', [ResponsavelController::class, 'store'])->name('cadastro.store.responsavel');


// Usuário Logado PADRÃO
Route::middleware('auth')->group(function () {

    Route::resource("dashboard", PostagemController::class)
        ->names("post")
        ->parameters(["dashboard" => "post"]);
    // curtida postagem
    Route::post('/dashboard/{id}/curtida', [CurtidaPostagemController::class, 'toggleCurtida'])->name('post.curtida');
    // comentario postagem
    Route::post('/dashboard/{id}/comentario', [ComentarioPostagemController::class, 'store'])->name('post.comentario');
    // denuncia postagem
    Route::post('/dashboard/{id_postagem}/denuncia/{id_usuario}', [DenunciaPostagemController::class, 'post'])->name('post.denuncia');

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

Route::middleware('auth', 'is_profissional')->group(function () {

    Route::resource("usuario", UsuarioController::class)
        ->names("usuario")
        ->parameters(["usuarios" => "usuario"]);
});

require __DIR__ . '/auth.php';
