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
});

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
// Cadastro de Responsável
Route::get('/cadastro/responsavel', [ResponsavelController::class, 'create'])->name('cadastro.responsavel');
Route::post('/cadastro', [ResponsavelController::class, 'store'])->name('cadastro.store.responsavel');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
