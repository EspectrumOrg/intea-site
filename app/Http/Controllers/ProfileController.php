<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Genero;
use App\Models\Autista;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private $genero;

    public function __construct(Genero $genero)
    {
        $this->genero = $genero;
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $generos = $this->genero->all();
        $user = Auth::user();
        $dadosespecificos = null;

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

        return view('profile.edit', compact('dadosespecificos', 'generos', 'user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $generos = $this->genero->all();
        $user = Auth::user();

        /*$user->update($request->only([
            'nome',
            'user',
            'apelido',
            'email',
            'cpf',
            'genero',
            'data_nascimento',
            'cep',
            'logradouro',
            'endereco',
            'rua',
            'bairro',
            'numero',
            'cidade',
            'estado',
            'complemento',
        ]));

        switch ($user->tipo_usuario) {
            case 2:
                $user->autista()->update($request->only(['cipteia_autista', 'rg_autista', 'status_cipteia']));
                break;
            case 4:
                $user->profissionalsaude()->update($request->only(['tipo_registro', 'registro_profissional']));
                break;
            case 5:
                $user->responsavel()->update($request->only(['cipteia_autista']));
                break;
        }*/


        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit', compact('generos'))->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
