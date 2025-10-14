<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Models\ChatPrivado;
use App\Models\MensagemPrivada;
use Illuminate\Http\Request;

class PusherController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
    public function index($usuario2)
    {
        $usuario1 = auth()->id(); // usuário logado

        // Buscar conversa existente ou criar depois ao enviar mensagem
        $conversa = ChatPrivado::where(function ($query) use ($usuario1, $usuario2) {
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
            $mensagens = MensagemPrivada::where('conversa_id', $conversa->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }
        return view('feed.chats.chat', compact('mensagens', 'usuario2'));
    }
 public function broadcast(Request $request)
{
    $usuario1 = auth()->id();
    $usuario2 = $request->usuario2_id;
    $texto = $request->message;

    // Busca ou cria conversa
    $conversa = \App\Models\ChatPrivado::where(function($q) use ($usuario1, $usuario2){
        $q->where('usuario1_id', $usuario1)->where('usuario2_id', $usuario2);
    })->orWhere(function($q) use ($usuario1, $usuario2){
        $q->where('usuario1_id', $usuario2)->where('usuario2_id', $usuario1);
    })->first();

    if(!$conversa){
        $conversa = \App\Models\ChatPrivado::create([
            'usuario1_id' => $usuario1,
            'usuario2_id' => $usuario2,
        ]);
    }

    // Salva mensagem
    $mensagem = \App\Models\MensagemPrivada::create([
        'conversa_id' => $conversa->id,
        'remetente_id' => $usuario1,
        'texto' => $texto,
    ]);

    // Busca a foto do usuário remetente
    $remetente = \App\Models\Usuario::find($usuario1);
    $foto = $remetente->foto ?? 'default.jpg';

    // Dispara Pusher passando a foto
    broadcast(new \App\Events\PusherBroadcast($texto, $usuario1, $foto))->toOthers();

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
