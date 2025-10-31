<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContatoSuporte;
use App\Models\RespostaSuporte;
use App\Mail\Contato;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Facades\Auth;

class ContatoController extends Controller
{
    public function index(Request $request)
    { //View
        $query = ContatoSuporte::query();

        // Busca por email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");;
        }

        // Filtros
        if ($request->filled('assunto')) {
            $query->where('assunto', $request->assunto);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc');
        $query->orderBy('id', $ordem);

        // Paginação
        $contatos = $query->paginate(10);

        return view('admin.suporte.index', compact('contatos'));
    }

    public function store(Request $request)
    { //Salvar
        $request->validate(
            [
                'email' => 'required|email',
                'name' => 'required|max:255',
                'assunto' => 'required|max:255',
                'mensagem' => 'required|max:755',
            ],
            [
                'email.email' => 'email inválido',
                'name.max' => 'nome não podem passar de 255 caracteres',
                'assunto.max' => 'assunto não pode passar de 255 caracteres',
                'mensagem.max' => 'mensagem não pode passar de 755 caracteres',
            ]
        );

        ContatoSuporte::create([
            'email' => $request->email,
            'name' => $request->name,
            'assunto' => $request->assunto,
            'mensagem' => $request->mensagem,
        ]);

        $email = new Contato(
            $request->email,
            $request->name,
            $request->assunto,
            $request->mensagem,
        );

        Mail::to(env('MAIL_FROM_ADDRESS'))->send($email);

        return redirect()->route('landpage', ['#contact'])->with('success', 'Email enviado com sucesso!');
    }

    public function resposta(Request $request)
    { // Email de resposta
        $request->validate([
            'destinatario' => 'required|email',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|max:2000',
        ]);

        $resposta = RespostaSuporte::create([
            'destinatario' => $request->destinatario,
            'assunto' => $request->assunto,
            'mensagem' => $request->mensagem,
        ]);

        Mail::to($request->destinario)->send(new \App\Mail\RespostaSuporteMail($resposta));

        return back()->with('success', 'Resposta enviada com sucesso!');
    }
}
