<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use App\Models\FoneUsuario;
use App\Models\Postagem;
use App\Models\Usuario;
use App\Models\Autista;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResponsavelPainelController extends Controller
{
    private $genero;
    private $telefone;

    public function __construct(Genero $genero, FoneUsuario $telefone)
    {
        $this->genero = $genero;
        $this->telefone = $telefone;
    }

    /**
     * Exibe o painel do responsável
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $generos = $this->genero->all();
        $telefones = $this->telefone->where('usuario_id', $user->id)->get();

        $autista = null;
        $dadosespecificos = null;

        if ($user->tipo_usuario == 5 && $user->responsavel) {
            // Pega o primeiro autista vinculado
            $autista = $user->responsavel->autistas()->with('usuario')->first();
            $dadosespecificos = $autista; // A view vai usar
        }

        // 🔥 Postagens populares
        $postsPopulares = Postagem::withCount('curtidas')
            ->orderByDesc('curtidas_count')
            ->take(5)
            ->get();

        // 📜 Postagens do autista
        $userPosts = $autista && $autista->usuario
            ? Postagem::where('usuario_id', $autista->usuario->id)->get()
            : collect();

        // ❤️ Postagens curtidas pelo autista
        $likedPosts = $autista && $autista->usuario
            ? Postagem::whereHas('curtidas', function ($q) use ($autista) {
                $q->where('usuario_id', $autista->usuario->id);
            })->get()
            : collect();

        return view('responsavel.painel', compact(
            'user',
            'generos',
            'telefones',
            'dadosespecificos',
            'userPosts',
            'likedPosts',
            'postsPopulares',
            'autista'
        ));
    }

    /**
     * Atualiza dados do responsável e do autista vinculado
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validação dos dados do usuário responsável
        $validated = $request->validate([
            'user' => 'required|string|max:255|unique:tb_usuario,user,' . $user->id,
            'email' => 'required|email|unique:tb_usuario,email,' . $user->id,
            'apelido' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|exists:tb_genero,id',
        ]);

        // Remover foto antiga se solicitado
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            $validated['foto'] = 'assets/images/logos/contas/user.png';
        }
        // Upload de nova foto
        else if ($request->hasFile('foto')) {
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            $path = $request->file('foto')->store('profiles', 'public');
            $validated['foto'] = $path;
        } else {
            unset($validated['foto']);
        }

        $user->update($validated);

        // Atualiza dados do autista vinculado, se houver
        $this->updateDadosEspecificos($user, $request);

        return Redirect::route('responsavel.painel')->with('status', 'profile-updated');
    }

    /**
     * Atualiza dados específicos para o autista vinculado
     */
    private function updateDadosEspecificos(Usuario $user, Request $request)
    {
        if ($user->tipo_usuario !== 5 || !$user->responsavel) {
            return;
        }

        $autista = $user->responsavel->autistas()->with('usuario')->first();
        if (!$autista) {
            return;
        }

        // Validação dos campos do autista
        $autistaData = $request->validate([
            'cipteia_autista' => 'nullable|string|max:255',
            'status_cipteia_autista' => 'nullable|string|max:255',
            'rg_autista' => 'nullable|string|max:255',
        ]);

        $autista->update($autistaData);
    }

    /**
     * Desativa a conta do responsável
     */
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
