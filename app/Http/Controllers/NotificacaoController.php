<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacao;
use App\Models\Tendencia;

class NotificacaoController extends Controller
{
    // Lista todas as notificações que o usuário recebeu
   

public function index()
{
    $notificacoes = \App\Models\Notificacao::where('alvo_id', auth()->id())
        ->whereIn('tipo', ['seguindo_voce', 'solicitacao_aceita', 'seguir']) 
        ->orderBy('created_at', 'desc')
        ->get();

    $tendenciasPopulares = Tendencia::populares(7)->get();

    return view('notificacao.notificacao', compact('notificacoes', 'tendenciasPopulares'));
}



public function destroy($id)
{
    $notificacao = \App\Models\Notificacao::where('alvo_id', auth()->id())
        ->where('id', $id)
        ->firstOrFail();

    $notificacao->delete();

    return redirect()->back()->with('success', 'Notificação removida.');
}


   public function aceitar($id)
{
    /** @var \App\Models\Usuario $user */
    $user = auth()->user();
    $notificacao = Notificacao::findOrFail($id);

    // Verifica se a notificação é realmente para esse usuário
    if ($notificacao->alvo_id != $user->id) {
        return redirect()->back()->with('error', 'Você não pode aceitar esta solicitação!');
    }

    // Verifica se já existe o vínculo (para evitar duplicação)
    $jaSegue = $user->seguidores()
        ->where('tb_usuario.id', $notificacao->solicitante_id)
        ->exists();

    if (!$jaSegue) {
        $user->seguidores()->attach($notificacao->solicitante_id);
    }

    // Remove a notificação após aceitar
    $notificacao->delete();

    return redirect()->back()->with('success', 'Solicitação de seguir aceita!');
}
    // Recusar solicitação
    public function recusar($id)
    {
        $user = auth()->user();
        $notificacao = Notificacao::findOrFail($id);

        if ($notificacao->alvo_id != $user->id) {
            return redirect()->back()->with('error', 'Você não pode recusar esta solicitação!');
        }

        $notificacao->delete();

        return redirect()->back()->with('success', 'Solicitação de seguir recusada.');
    }
}
