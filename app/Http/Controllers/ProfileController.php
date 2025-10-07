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

        $posts = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count') // mais curtidas primeiro
            ->take(5) // pega só os 5 mais curtidos
            ->get();

        switch ($user->tipo_usuario) {
            case 2:
                $dadosespecificos = $user->autista;
                break;
            case 4:
                $dadosespecificos = $user->profissional_saude;
                break;
            case 5:
                $dadosespecificos = $user->responsavel;
                break;
        }

        return view('profile.show', compact('dadosespecificos', 'generos', 'telefones', 'user', 'posts'));
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