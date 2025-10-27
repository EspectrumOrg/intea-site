<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Genero;
use App\Models\FoneUsuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Postagem;

class ProfileController extends Controller
{
    private $genero;
    private $telefone;

    public function __construct(Genero $genero, FoneUsuario $telefone)
    {
        $this->genero = $genero;
        $this->telefone = $telefone;
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $generos = $this->genero->all();
        $user = Auth::user();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();
        $dadosespecificos = null;
        $autista = null;

        // 🔥 Postagens populares (as mais curtidas)
        $postsPopulares = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // 📜 Postagens do usuário logado
        $userPosts = Postagem::where('usuario_id', $user->id)->get();

        // ❤️ Postagens curtidas pelo usuário
        $likedPosts = Postagem::whereHas('curtidas', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->get();

        // 🔍 Dados específicos por tipo de usuário
        switch ($user->tipo_usuario) {
            case 1:
                $dadosespecificos = $user->admin;
                break;
            case 2:
                $dadosespecificos = $user->autista;
                break;
            case 3:
                $dadosespecificos = $user->comunidade;
                break;
            case 4:
                $dadosespecificos = $user->profissional_saude;
                break;
            case 5:
                $dadosespecificos = $user->responsavel;
                $autista = $user->responsavel->autistas()->first() ?? null;
                break;
        }

        // ✅ Retorna para a view com todas as variáveis necessárias
        return view('profile.show', compact(
            'dadosespecificos',
            'generos',
            'telefones',
            'user',
            'userPosts',
            'likedPosts',
            'postsPopulares',
            'autista',
            
        ));
    }

    
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $generos = $this->genero->all();
        $user = Auth::user();
        $request->user()->fill($request->validated());
        if ($request->hasFile('foto')) {
            // salva em storage/app/arquivos/perfil/fotos
            $path = $request->file('foto')->store('arquivos/perfil/fotos', 'public');
            // salva o caminho no banco
            $user->foto = $path;
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.show', compact('generos'))->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account. logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->status_conta = 0;

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
