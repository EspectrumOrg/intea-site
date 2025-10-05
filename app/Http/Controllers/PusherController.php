<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use Illuminate\Http\Request;

class PusherController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $usuario1 = auth()->id();  
    $usuario2 = $usuario1 == 1 ? 2 : 1; // Ajuste dinâmico para teste

    $conversa = \App\Models\ChatPrivadoModel::where(function ($query) use ($usuario1, $usuario2) {
            $query->where('usuario1_id', $usuario1)
                  ->where('usuario2_id', $usuario2);
        })
        ->orWhere(function ($query) use ($usuario1, $usuario2) {
            $query->where('usuario1_id', $usuario2)
                  ->where('usuario2_id', $usuario1);
        })
        ->first();

    $mensagens = [];

    if ($conversa) {
        $mensagens = \App\Models\MensagemPrivadaModel::where('conversa_id', $conversa->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

return view('chat', compact('mensagens', 'usuario2'));
}
    public function broadcast(Request $request)
    {
        broadcast(new PusherBroadcast($request->get('message')))->toOthers();
        return view('broadcast', ['message' => $request->get('message')]);
    }

    public function receive(Request $request)
    {
        return view('receive', ['message' => $request->get('message')]);
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
        //
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
