<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;




class SeguirController extends Controller
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
    /** @var \App\Models\Usuario $user */
    $user = auth()->user();
    $userIdToFollow = $request->input('user_id');

    if ($user->id == $userIdToFollow) {
        return redirect()->back()->with('error', 'Você não pode seguir a si mesmo!');
    }

    $userToFollow = \App\Models\Usuario::find($userIdToFollow);

    if (!$userToFollow) {
        return redirect()->back()->with('error', 'Usuário não encontrado!');
    }
    if ($userToFollow->visibilidade == 0) {
        \App\Models\Notificacao::create([
            'solicitante_id' => $user->id,
            'alvo_id' => $userIdToFollow,
            'tipo' => 'seguir',
        ]);

        return redirect()->back()->with('success', 'Solicitação de seguir enviada!');
    }

    // Conta pública → segue diretamente
    $isAlreadyFollowing = $user->seguindo()->where('tb_usuario.id', $userIdToFollow)->exists();

    if (!$isAlreadyFollowing) {
        $user->seguindo()->attach($userIdToFollow);
    }

    return redirect()->back()->with('success', 'Você está seguindo o usuário!');
}

       // Contar quantos usuários o usuário está seguindo
    public function countSeguindo($id)
    {
        $user = Usuario::findOrFail($id);
        $count = $user->seguindo()->count(); // quantos ele segue
        return response()->json(['seguindo' => $count]);
    }

     public function countSeguidores($id)
    {
        $user = Usuario::findOrFail($id);
        $count = $user->seguidores()->count(); // quantos o seguem
        return response()->json(['seguidores' => $count]);
    }

public function listarSeguindo($id)
{
    $user = Usuario::findOrFail($id);
    $seguindo = $user->seguindo()->get(); // lista de usuários que ele segue
    return response()->json($seguindo);
}

public function listarSeguidores($id)
{
    $user = Usuario::findOrFail($id);
    $seguidores = $user->seguidores()->get(); // lista de usuários que o seguem
    return response()->json($seguidores);
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
    /** @var \App\Models\Usuario $user */
    $user = auth()->user();

    // Verifica se o usuário está realmente seguindo o outro
    $isFollowing = $user->seguindo()->where('tb_usuario.id', $id)->exists();

    if (!$isFollowing) {
        return redirect()->back()->with('error', 'Você não está seguindo esse usuário!');
    }

    // Remove o vínculo de "seguindo"
    $user->seguindo()->detach($id);

    return redirect()->back()->with('success', 'Você deixou de seguir o usuário!');
}
}
