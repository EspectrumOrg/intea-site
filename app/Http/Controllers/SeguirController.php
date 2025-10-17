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

        if ($user->id != $userIdToFollow) {
            $isAlreadyFollowing = $user->seguindo()->where('tb_usuario.id', $userIdToFollow)->exists();

            if (!$isAlreadyFollowing) {
                $user->seguindo()->attach($userIdToFollow);
            }
        }

        return redirect()->back()->with('success', 'Você está seguindo o usuário!');
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
