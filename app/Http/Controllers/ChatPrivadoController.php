<?php

namespace App\Http\Controllers;

use App\Models\ChatPrivado;
use App\Models\ChatPrivadoModel;
use App\Models\MensagemPrivada;
use App\Models\MensagemPrivadaModel;
use Illuminate\Http\Request;

class ChatPrivadoController extends Controller
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

    public function enviarMensagem(Request $request)
    {
        $usuario1 = $request->usuario1_id;
        $usuario2 = $request->usuario2_id;
        $texto = $request->texto;

        // Verifica se já existe conversa entre os dois usuários
        $conversa = ChatPrivado::where(function ($query) use ($usuario1, $usuario2) {
            $query->where('usuario1_id', $usuario1)
                ->where('usuario2_id', $usuario2);
        })
            ->orWhere(function ($query) use ($usuario1, $usuario2) {
                $query->where('usuario1_id', $usuario2)
                    ->where('usuario2_id', $usuario1);
            })
            ->first();

        // Se não existir, cria nova conversa
        if (!$conversa) {
            $conversa = ChatPrivado::create([
                'usuario1_id' => $usuario1,
                'usuario2_id' => $usuario2,
            ]);
        }

        // Insere a mensagem
        $mensagem = MensagemPrivada::create([
            'conversa_id' => $conversa->id,
            'remetente_id' => $usuario1,
            'texto' => $texto,
        ]);

        return response()->json([
            'status' => 'ok',
            'conversa_id' => $conversa->id,
            'mensagem' => $mensagem,
        ]);
    }
public function buscarUsuarioschat(Request $request)
{
    $usuarioId = auth()->id(); // Usuário logado
    $search = $request->input('q'); // Texto digitado

    $conversas = ChatPrivado::where('usuario1_id', $usuarioId)
        ->orWhere('usuario2_id', $usuarioId)
        ->get();

    $usuariosConversando = $conversas->map(function($c) use ($usuarioId) {
        return $c->usuario1_id == $usuarioId ? $c->usuario2_id : $c->usuario1_id;
    })->unique()->toArray();

    if(empty($usuariosConversando)) {
        return response()->json([]);
    }

    $usuarios = \App\Models\Usuario::whereIn('id', $usuariosConversando)
        ->where('user', 'like', "%{$search}%")
        ->get(['id', 'user', 'foto']);

    return response()->json($usuarios);
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
