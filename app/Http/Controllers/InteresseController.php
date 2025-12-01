<?php

namespace App\Http\Controllers;

use App\Models\Interesse;
use App\Models\Postagem;
use App\Models\Tendencia;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InteresseController extends Controller
{
    public function show($slug)
    {
        $interesse = Interesse::where('slug', $slug)
            ->withCount(['seguidores', 'postagens'])
            ->firstOrFail();
        
        $usuario = Auth::user();
        
        // Postagens com mais curtidas
        $postagensMaisCurtidas = $interesse->postagensMaisCurtidas(20);
        
        // Usuários populares
        $usuariosPopulares = $interesse->usuariosPopulares(6);
        
        // Postagens destacadas
        $postagensDestacadas = $interesse->postagensDestacadas(5);
        
        // Verificar se usuário segue o interesse
        $usuarioSegue = $usuario ? $interesse->usuarioSegue($usuario->id) : false;
        
        // Verificar se usuário é moderador
        $usuarioEhModerador = $usuario ? $interesse->moderadores()->where('usuario_id', $usuario->id)->exists() : false;
        
        // Verificar se usuário é dono
        $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
        $usuarioEhDono = $dono && $usuario && $usuario->id === $dono->id;
        
        // Estatísticas do interesse
        $estatisticas = $interesse->obterEstatisticas();
        
        // Tendencias populares para sidebar
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.show', compact(
            'interesse', 
            'postagensMaisCurtidas', 
            'usuariosPopulares',
            'postagensDestacadas',
            'usuarioSegue',
            'usuarioEhModerador',
            'usuarioEhDono',
            'dono',
            'estatisticas',
            'tendenciasPopulares'
        ));
    }

    public function seguir(Request $request, $id)
    {
        $interesse = Interesse::findOrFail($id);
        $usuario = Auth::user();
        
        if (!$usuario->segueInteresse($interesse->id)) {
            $usuario->seguirInteresse($interesse->id, $request->boolean('notificacoes', true));
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Agora você segue ' . $interesse->nome,
                'dados' => [
                    'segue' => true,
                    'contador_membros' => $interesse->contador_membros
                ]
            ]);
        }
        
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Você já segue este interesse'
        ], 400);
    }

    public function deixarSeguir($id)
    {
        $interesse = Interesse::findOrFail($id);
        $usuario = Auth::user();
        
        if ($usuario->segueInteresse($interesse->id)) {
            $usuario->deixarSeguirInteresse($interesse->id);
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Você deixou de seguir ' . $interesse->nome,
                'dados' => [
                    'segue' => false,
                    'contador_membros' => $interesse->contador_membros
                ]
            ]);
        }
        
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Você não segue este interesse'
        ], 400);
    }

    public function index()
    {
        $interesses = Interesse::ativos()
            ->withCount('seguidores')
            ->orderBy('seguidores_count', 'desc')
            ->paginate(12)
            ->onEachSide(1)
            ->withQueryString();

        $usuario = Auth::user();
        $interessesUsuario = $usuario ? $usuario->interesses->pluck('id')->toArray() : [];
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.index', compact('interesses', 'interessesUsuario', 'tendenciasPopulares'));
    }

    public function postagens($slug, Request $request)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        
        $postagens = $interesse->postagensVisiveis()
                    ->with(['usuario', 'imagens', 'interesses'])
                    ->withCount(['curtidas', 'comentarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return response()->json([
            'sucesso' => true,
            'interesse' => $interesse,
            'postagens' => $postagens
        ]);
    }

    public function sugeridos()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuário não autenticado'
            ], 401);
        }
        
        $interessesSugeridos = $usuario->obterInteressesSugeridos(6);
        
        return response()->json([
            'sucesso' => true,
            'interesses' => $interessesSugeridos
        ]);
    }

    public function categorizarPostagem(Request $request, $interesseId)
    {
        $request->validate([
            'postagem_id' => 'required|exists:postagens,id',
            'observacao' => 'nullable|string|max:500'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $postagem = Postagem::findOrFail($request->postagem_id);
        
        if ($postagem->pertenceAoInteresse($interesse->id)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Postagem já está neste interesse'
            ], 400);
        }
        
        $postagem->categorizarInteresse(
            $interesse->id,
            'manual',
            Auth::id(),
            $request->observacao
        );
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Postagem categorizada com sucesso',
            'interesse' => $interesse
        ]);
    }

    /**
     * Mostrar formulário de criação de interesse
     */
    public function create()
    {
        $tendenciasPopulares = Tendencia::populares(7)->get();
        return view('interesses.create', compact('tendenciasPopulares'));
    }

    /**
     * Salvar novo interesse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:50|unique:interesses,nome',
            'descricao' => 'required|string|max:200',
            'sobre' => 'nullable|string|max:1000',
            'icone_type' => 'required|in:default,custom',
            'icone' => 'required_if:icone_type,default|string|max:50',
            'icone_custom' => 'required_if:icone_type,custom|image|mimes:jpeg,png,jpg,svg|max:1024',
            'cor' => 'required|string|size:7',
        ], [
            'nome.required' => 'O nome do interesse é obrigatório',
            'nome.max' => 'O nome não pode ter mais de 50 caracteres',
            'nome.unique' => 'Já existe um interesse com este nome',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.max' => 'A descrição não pode ter mais de 200 caracteres',
            'icone.required_if' => 'Selecione um ícone padrão',
            'icone_custom.required_if' => 'Faça upload de um ícone customizado',
            'icone_custom.image' => 'O arquivo deve ser uma imagem',
            'icone_custom.mimes' => 'Formatos permitidos: JPEG, PNG, JPG, SVG',
            'icone_custom.max' => 'O ícone não pode ter mais de 1MB',
            'cor.required' => 'Selecione uma cor',
        ]);

        try {
            // Criar slug único
            $slug = Str::slug($request->nome);
            $counter = 1;
            $originalSlug = $slug;
            
            while (Interesse::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Processar ícone
            $icone = null;
            $iconeCustomPath = null;
            
            if ($request->icone_type === 'default') {
                $icone = $request->icone;
            } else {
                // Salvar ícone customizado
                if ($request->hasFile('icone_custom')) {
                    $iconeCustomPath = $request->file('icone_custom')->store('arquivos/interesses/icones', 'public');
                    $icone = 'custom';
                }
            }

            // Criar interesse
            $interesse = Interesse::create([
                'nome' => $request->nome,
                'slug' => $slug,
                'descricao' => $request->descricao,
                'sobre' => $request->sobre,
                'icone' => $icone,
                'icone_custom' => $iconeCustomPath,
                'cor' => $request->cor,
                'contador_membros' => 1,
                'contador_postagens' => 0,
                'destaque' => false,
                'ativo' => true,
                'moderacao_ativa' => true,
                'limite_alertas_ban' => 3,
                'dias_expiracao_alerta' => 30,
            ]);

            // O criador automaticamente segue o interesse
            Auth::user()->seguirInteresse($interesse->id, true);

            // Tornar o criador moderador DONO
            $interesse->moderadores()->attach(Auth::id(), [
                'cargo' => 'dono',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('interesses.show', $interesse->slug)
                ->with('success', 'Interesse criado com sucesso! Você é o dono e moderador.');

        } catch (\Exception $e) {
            Log::error('Erro ao criar interesse: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao criar interesse: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Pesquisar interesses
     */
    public function pesquisar(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('interesses.index');
        }

        $interesses = Interesse::ativos()
            ->where(function($q) use ($query) {
                $q->where('nome', 'LIKE', "%{$query}%")
                  ->orWhere('descricao', 'LIKE', "%{$query}%")
                  ->orWhere('sobre', 'LIKE', "%{$query}%");
            })
            ->withCount('seguidores')
            ->orderBy('seguidores_count', 'desc')
            ->paginate(12);

        $usuario = Auth::user();
        $interessesUsuario = $usuario ? $usuario->interesses->pluck('id')->toArray() : [];
        $tendenciasPopulares = Tendencia::populares(7)->get();

        return view('interesses.pesquisa', compact(
            'interesses', 
            'interessesUsuario', 
            'tendenciasPopulares',
            'query'
        ));
    }

    /**
     * Excluir interesse
     */
    public function destroy($slug)
{
    try {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        if (!$usuario) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }
            return redirect()->route('login');
        }
        
        // Verificar se usuário é dono ou administrador
        $dono = $interesse->moderadores()
            ->wherePivot('cargo', 'dono')
            ->first();
        
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Você não tem permissão para deletar este interesse.'
                ], 403);
            }
            return redirect()->back()
                ->with('error', 'Você não tem permissão para deletar este interesse.');
        }
        
        $nomeInteresse = $interesse->nome;
        $interesse->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Interesse '{$nomeInteresse}' deletado com sucesso!",
                'redirect' => route('interesses.index')
            ]);
        }
        
        return redirect()->route('interesses.index')
            ->with('success', "Interesse '{$nomeInteresse}' deletado com sucesso!");
            
    } catch (\Exception $e) {
        Log::error('Erro ao deletar interesse: ' . $e->getMessage());
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao deletar interesse: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'Erro ao deletar interesse: ' . $e->getMessage());
    }
}
    
    /**
     * Mostrar formulário de edição
     */
    public function edit($slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        if (!$usuario) {
            return redirect()->route('login');
        }
        
        // Verificar se usuário é dono ou administrador
        $dono = $interesse->moderadores()
            ->wherePivot('cargo', 'dono')
            ->first();
        
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            return redirect()->route('interesses.show', $slug)
                ->with('error', 'Você não tem permissão para editar este interesse.');
        }
        
        $tendenciasPopulares = Tendencia::populares(7)->get();
        
        return view('interesses.edit', compact('interesse', 'tendenciasPopulares'));
    }
    
    /**
     * Atualizar interesse
     */
    public function update(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        if (!$usuario) {
            return redirect()->route('login');
        }
        
        // Verificar se usuário é dono ou administrador
        $dono = $interesse->moderadores()
            ->wherePivot('cargo', 'dono')
            ->first();
        
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            return redirect()->route('interesses.show', $slug)
                ->with('error', 'Você não tem permissão para editar este interesse.');
        }
        
        $request->validate([
            'nome' => 'required|string|max:50|unique:interesses,nome,' . $interesse->id,
            'descricao' => 'required|string|max:200',
            'sobre' => 'nullable|string|max:1000',
            'icone_type' => 'required|in:default,custom',
            'icone' => 'required_if:icone_type,default|string|max:50',
            'icone_custom' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
            'cor' => 'required|string|size:7',
            'moderacao_ativa' => 'nullable|boolean',
        ]);
        
        try {
            // Atualizar slug se o nome mudou
            if ($request->nome != $interesse->nome) {
                $interesse->slug = Str::slug($request->nome);
            }
            
            // Processar ícone
            if ($request->icone_type === 'default') {
                $interesse->icone = $request->icone;
                // Se estava usando ícone customizado, deletar o arquivo
                if ($interesse->icone_custom) {
                    Storage::disk('public')->delete($interesse->icone_custom);
                    $interesse->icone_custom = null;
                }
            } else {
                // Manter ícone customizado existente ou fazer upload novo
                if ($request->hasFile('icone_custom')) {
                    // Deletar ícone antigo se existir
                    if ($interesse->icone_custom) {
                        Storage::disk('public')->delete($interesse->icone_custom);
                    }
                    
                    $iconeCustomPath = $request->file('icone_custom')->store('arquivos/interesses/icones', 'public');
                    $interesse->icone_custom = $iconeCustomPath;
                    $interesse->icone = 'custom';
                }
                // Se não enviou novo arquivo, manter o existente
            }
            
            // Atualizar outros campos
            $interesse->nome = $request->nome;
            $interesse->descricao = $request->descricao;
            $interesse->sobre = $request->sobre;
            $interesse->cor = $request->cor;
            $interesse->moderacao_ativa = $request->boolean('moderacao_ativa', $interesse->moderacao_ativa);
            
            $interesse->save();
            
            return redirect()->route('interesses.show', $interesse->slug)
                ->with('success', 'Interesse atualizado com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar interesse: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao atualizar interesse: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Gerenciar moderadores
     */
   public function moderadores($slug)
{
    $interesse = Interesse::where('slug', $slug)->firstOrFail();
    $usuario = Auth::user();
    
    if (!$usuario) {
        return redirect()->route('login');
    }
    
    // Verificar se usuário é dono ou administrador
    $dono = $interesse->moderadores()
        ->wherePivot('cargo', 'dono')
        ->first();
    
    $usuarioEhDono = $dono && $usuario->id === $dono->id; // ← ADICIONE ESTA LINHA
    $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
    
    if (!$usuarioEhDono && !$usuarioEhAdministrador) {
        return redirect()->route('interesses.show', $slug)
            ->with('error', 'Você não tem permissão para gerenciar moderadores.');
    }
    
    $tendenciasPopulares = Tendencia::populares(7)->get();
    
    return view('interesses.moderadores', compact(
        'interesse', 
        'tendenciasPopulares',
        'usuarioEhDono', 
        'usuarioEhAdministrador',
    ));
}
    
    /**
     * Adicionar moderador
     */
    public function adicionarModerador(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        // Verificar permissões
        $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Sem permissão'
            ], 403);
        }
        
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id'
        ]);
        
        $novoModerador = Usuario::find($request->usuario_id);
        
        // Verificar se já é moderador
        if ($interesse->moderadores()->where('usuario_id', $novoModerador->id)->exists()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuário já é moderador'
            ], 400);
        }
        
        // Adicionar como moderador
        $interesse->moderadores()->attach($novoModerador->id, [
            'cargo' => 'moderador',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Moderador adicionado com sucesso'
        ]);
    }
    
    /**
     * Remover moderador
     */
    public function removerModerador(Request $request, $slug)
    {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        // Verificar permissões
        $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Sem permissão'
            ], 403);
        }
        
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id'
        ]);
        
        $moderador = Usuario::find($request->usuario_id);
        
        // Não permitir remover o dono
        if ($moderador->id === $dono->id) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Não é possível remover o dono do interesse'
            ], 400);
        }
        
        // Remover moderador
        $interesse->moderadores()->detach($moderador->id);
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Moderador removido com sucesso'
        ]);
    }

    public function transferirPropriedade(Request $request, $slug)
{
    try {
        $interesse = Interesse::where('slug', $slug)->firstOrFail();
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuário não autenticado'
            ], 401);
        }
        
        // Verificar se usuário é dono atual
        $donoAtual = $interesse->moderadores()
            ->wherePivot('cargo', 'dono')
            ->first();
        
        if (!$donoAtual || $donoAtual->id !== $usuario->id) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Apenas o dono atual pode transferir a propriedade'
            ], 403);
        }
        
        $request->validate([
            'novo_dono_id' => 'required|exists:usuarios,id'
        ]);
        
        $novoDono = Usuario::findOrFail($request->novo_dono_id);
        
        // Não permitir transferir para si mesmo
        if ($novoDono->id === $usuario->id) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Você já é o dono deste interesse'
            ], 400);
        }
        
        // Verificar se novo dono já é moderador
        $jaEhModerador = $interesse->moderadores()
            ->where('usuario_id', $novoDono->id)
            ->exists();
        
        // Remover o cargo de dono do atual
        $interesse->moderadores()->updateExistingPivot($usuario->id, [
            'cargo' => $jaEhModerador ? 'moderador' : 'ex-dono'
        ]);
        
        // Se novo dono já é moderador, atualizar cargo
        if ($jaEhModerador) {
            $interesse->moderadores()->updateExistingPivot($novoDono->id, [
                'cargo' => 'dono',
                'updated_at' => now()
            ]);
        } else {
            // Se não é moderador, adicionar como dono
            $interesse->moderadores()->attach($novoDono->id, [
                'cargo' => 'dono',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Garantir que novo dono segue o interesse
        if (!$interesse->usuarioSegue($novoDono->id)) {
            $interesse->adicionarSeguidor($novoDono->id);
        }
        
        // Registrar histórico da transferência
        Log::info("Interesse {$interesse->nome} transferido de {$usuario->user} para {$novoDono->user}");
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Propriedade transferida para ' . ($novoDono->apelido ?? $novoDono->user) . ' com sucesso!'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erro ao transferir propriedade: ' . $e->getMessage());
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Erro ao transferir propriedade: ' . $e->getMessage()
        ], 500);
    }
}
}