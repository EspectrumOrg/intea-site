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
        $conversa = \App\Models\ChatPrivado::where(function ($q) use ($usuario1, $usuario2) {
            $q->where('usuario1_id', $usuario1)->where('usuario2_id', $usuario2);
        })->orWhere(function ($q) use ($usuario1, $usuario2) {
            $q->where('usuario1_id', $usuario2)->where('usuario2_id', $usuario1);
        })->first();

        if (!$conversa) {
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

public function webzap(Request $request)
{
    $usuarioLogado = auth()->id();
    $usuario2 = $request->usuario2; // usuário selecionado via link ou query string

    // Pega os IDs dos usuários que você segue
    $usuariosSeguindoIds = \App\Models\seguirModel::where('segue_id', $usuarioLogado)
        ->pluck('seguindo_id')
        ->toArray();

    // Busca os usuários que você segue
    $usuariosSeguindo = \App\Models\Usuario::whereIn('id', $usuariosSeguindoIds)->get();

    // Busca conversas existentes do usuário logado
    $conversas = \App\Models\ChatPrivado::where('usuario1_id', $usuarioLogado)
        ->orWhere('usuario2_id', $usuarioLogado)
        ->get();

    // Mapeia os usuários com quem você tem conversas
    $conversasComUsuarios = $conversas->map(function($c) use ($usuarioLogado) {
        $outroUsuarioId = $c->usuario1_id == $usuarioLogado ? $c->usuario2_id : $c->usuario1_id;
        return \App\Models\Usuario::find($outroUsuarioId);
    })->unique()->filter();

    // Se houver um usuario2 vindo da URL/query, busca ele para abrir o chat
    $usuarioSelecionado = null;
    if ($usuario2) {
        $usuarioSelecionado = \App\Models\Usuario::find($usuario2);
    }

    return view('feed.chats.testechat', compact(
        'usuariosSeguindo', 
        'conversas', 
        'usuarioLogado', 
        'conversasComUsuarios',
        'usuarioSelecionado' // envia para o blade para abrir automaticamente
    ));
}
    /**
     * Rota AJAX para carregar chat
     */
    public function carregarChat(Request $request)
    {
        $usuario1 = auth()->id();
        $usuario2 = $request->usuario2;

        $usuario = \App\Models\Usuario::find($usuario2);

        $conversa = \App\Models\ChatPrivado::where(function ($q) use ($usuario1, $usuario2) {
            $q->where('usuario1_id', $usuario1)->where('usuario2_id', $usuario2);
        })->orWhere(function ($q) use ($usuario1, $usuario2) {
            $q->where('usuario1_id', $usuario2)->where('usuario2_id', $usuario1);
        })->first();

        $mensagens = [];
        if ($conversa) {
            $mensagens = \App\Models\MensagemPrivada::where('conversa_id', $conversa->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    $remetente = \App\Models\Usuario::find($msg->remetente_id);
                    return [
                        'remetente_id' => $msg->remetente_id,
                        'message' => $msg->texto,
                        'foto' => $remetente->foto ?? 'default.jpg',
                    ];
                });
        }

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'user' => $usuario->user,
                'foto' => $usuario->foto ?? 'default.jpg',
            ],
            'mensagens' => $mensagens
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
