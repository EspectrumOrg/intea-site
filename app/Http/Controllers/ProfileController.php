<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Models\Postagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Usuario;


class ProfileController extends Controller
{
    private $genero;
    private $telefone;

    public function __construct(Genero $genero, FoneUsuario $telefone)
    {
        $this->genero = $genero;
        $this->telefone = $telefone;
    }

    public function edit(Request $request)
    {
        /** @var \App\Models\Usuario $user */

        $user = Auth::user();
        $generos = $this->genero->all();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();

         $seguindo = $user->seguindo()->get();

        $seguidores = $user->seguidores()->get();
        // Carregar dados específicos
        $dadosespecificos = null;
        $autista = null;
        // Calcular idade
        $maiorDeIdade = false;

        if ($user->data_nascimento) {
            $idade = \Carbon\Carbon::parse($user->data_nascimento)->age;
            $maiorDeIdade = $idade >= 18;
        }

        // Postagens (as mais curtidas)
        $postsPopulares = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // Postagens
        $userPosts = Postagem::where('usuario_id', $user->id)->get();

        // Postagens curtidas
        $likedPosts = Postagem::whereHas('curtidas', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->get();

        // Comentários curtidos
        $likedComments = Comentario::whereHas('curtidas', function ($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->get();

        // Dados específicos por tipo de usuário
        switch ($user->tipo_usuario) {
            case 1: $dadosespecificos = $user->admin; break;
            case 2: $dadosespecificos = $user->autista; break;
            case 3: $dadosespecificos = $user->comunidade; break;
            case 4: $dadosespecificos = $user->profissionalsaude; break;
            case 5:
                $dadosespecificos = $user->responsavel;
                $autistas = $user->responsavel ? $user->responsavel->autistas : collect();
                break;
        }

        return view('profile.edit', compact(
            'user',
            'generos',
            'telefones',
            'dadosespecificos',
            'userPosts',
            'likedPosts',
            'likedComments',
            'postsPopulares',
            'autista',
            'maiorDeIdade',
             'seguindo',
            'seguidores'
        ));
    }

    /**
     * Update the user's profile information.
     */
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validação
        $validated = $request->validate([
            'user' => 'required|string|max:255|unique:tb_usuario,user,' . $user->id,
            'email' => 'required|email|unique:tb_usuario,email,' . $user->id,
            'apelido' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|exists:tb_genero,id',
        ]);

        // Verificar se o usuário quer remover a foto
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            // Use o caminho da imagem padrão em vez de null
            $validated['foto'] = 'assets/images/logos/contas/user.png';
        }
        // Upload da nova foto
        else if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }

            $path = $request->file('foto')->store('profiles', 'public');
            $validated['foto'] = $path;
        } else {
            // Mantém a foto atual se não houve alteração
            unset($validated['foto']);
        }

        // Atualizar usuário
        $user->update($validated);

        // Atualizar dados específicos
        $this->updateDadosEspecificos($user, $request);

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    private function updateDadosEspecificos($user, Request $request)
    {
        switch ($user->tipo_usuario) {
            case 2: // Autista
                if ($user->autista) {
                    $user->autista->update($request->only(['cipteia_autista','status_cipteia_autista','rg_autista']));
                }
                break;

            case 5: // Responsável
                if ($user->responsavel) {
                    foreach ($user->responsavel->autistas as $autista) {
                        $autista->update($request->only(['cipteia_autista','status_cipteia_autista','rg_autista']));
                    }
                }
                break;

            case 4: // Profissional de Saúde
                if ($user->profissionalsaude) {
                    $user->profissionalsaude->update($request->only(['tipo_registro','registro_profissional','cipteia_autista']));
                }
                break;
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        $user->status_conta = 0;
        $user->save();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
