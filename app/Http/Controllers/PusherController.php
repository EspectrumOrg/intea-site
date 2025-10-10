<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Models\ChatPrivadoModel;
use App\Models\MensagemPrivadaModel;
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
    $usuario1 = auth()->id();
    $usuario2 = $request->usuario2_id;
    $texto = $request->message;

    // Busca ou cria conversa
    $conversa = \App\Models\ChatPrivadoModel::where(function($q) use ($usuario1, $usuario2){
        $q->where('usuario1_id', $usuario1)->where('usuario2_id', $usuario2);
    })->orWhere(function($q) use ($usuario1, $usuario2){
        $q->where('usuario1_id', $usuario2)->where('usuario2_id', $usuario1);
    })->first();

    if(!$conversa){
        $conversa = \App\Models\ChatPrivadoModel::create([
            'usuario1_id' => $usuario1,
            'usuario2_id' => $usuario2,
        ]);
    }

    // Salva mensagem
    $mensagem = \App\Models\MensagemPrivadaModel::create([
        'conversa_id' => $conversa->id,
        'remetente_id' => $usuario1,
        'texto' => $texto,
    ]);

    // Dispara Pusher
    broadcast(new \App\Events\PusherBroadcast($texto, $usuario1))->toOthers();

    return response()->json([
        'message' => $texto,
        'remetente_id' => $usuario1
    ]);
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
