<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\Contato;
use Illuminate\Support\Facades\Mail;
Use Illuminate\Http\Facades\Auth;

class ContatoController extends Controller
{
    public function store(Request $request)
    { //Salvar
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|max:255',
            'assunto' => 'required|max:255',
            'mensagem' => 'required|max:755',
        ]);

        $email = new Contato(
            $request->email,
            $request->name,
            $request->assunto,
            $request->mensagem,
        );

        Mail::to(env('MAIL_FROM_ADDRESS'))->send($email);

         return redirect()->route('landpage')->with('success', 'Email enviado com sucesso!');
    }
}
