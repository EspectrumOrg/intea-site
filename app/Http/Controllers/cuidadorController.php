<?php

namespace App\Http\Controllers;
use App\Models\usuarioModel;
use App\Models\cuidadorModel;
use App\Models\foneUsuarioModel;

use Illuminate\Http\Request;

class cuidadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
  $usuario = usuarioModel::create([
            'nomeUsuario' => $request->nomeUsuario,
            'emailUsuario' => $request->emailUsuario,
            'senhaUsuario' => bcrypt($request->senhaUsuario),
            'cpfUsuario' => $request->cpfUsuario,
            'generoUsuario' => $request->generoUsuario,
            'dataNascUsuario' => $request->dataNascUsuario,
            'cepUsuario' => $request->cepUsuario,
            'logradouroUsuario' => $request->logradouroUsuario,
            'enderecoUsuario' => $request->enderecoUsuario,
            'ruaUsuario' => $request->ruaUsuario,
            'bairroUsuario' => $request->bairroUsuario,
            'numeroUsuario' => $request->numeroUsuario,
            'cidadeUsuario' => $request->cidadeUsuario,
            'estadoUsuario' => $request->estadoUsuario,
            'complementoUsuario' => $request->complementoUsuario,
        ]);

        $cuidador = cuidadorModel::create([
            'idusuario' => $usuario->id,
            'cipteiaAutista' => $request->cipteiaAutista,
        ]);
         $fone=foneUsuarioModel::create([
        'idusuario' => $usuario->id,
        'numerousuario' => $request->foneUsuario,
    ]);

    }

      
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
