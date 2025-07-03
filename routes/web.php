<?php

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


Route::get('/cadastro/autista', function() {
    return view('cadastro.create-autista'); // Cadastro de usuário autista
})->name('cadastro.autista');

Route::get('/cadastro/comunidade', function() {
    return view('cadastro.create-comunidade'); // Cadastro de usuário da comunidade
})->name('cadastro.comunidade');

Route::get('/cadastro/psicologo', function() {
    return view('cadastro.create-psicologo'); // Cadastro de profissional da saúde
})->name('cadastro.profissional');

Route::get('/cadastro/responsavel', function() {
    return view('cadastro.create-responsavel'); // Cadastro de responsável
})->name('cadastro.responsavel');