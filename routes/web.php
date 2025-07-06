<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutistaController;
use App\Http\Controllers\ComunidadeController;
use App\Http\Controllers\cuidadorController;
use App\Http\Controllers\ProfissionalSaudeController;
use App\Http\Controllers\ResponsavelController;
use App\Models\Autista;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
use App\Models\Usuario;
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
    return view('index'); // Página inicial
})->name('index');

Route::get('/cadastro', function () {
    return view('cadastro.index'); // Cadastro de usuário
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
Route::post('/cadastro', [ProfissionalSaudeController::class, 'store'])->name('cadastro.store.profissionalsaude');

Route::post('/cadastro/responsavel', [cuidadorController::class, 'store'])->name('cadastro.store.responsavel');
Route::get('/usuarios', [cuidadorController::class, 'apiTodosUsuarios'])->name('api.todos.usuarios');
