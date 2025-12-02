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
            'icone_custom' => 'required_if:icone_type,custom|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
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
            'icone_custom.mimes' => 'Formatos permitidos: JPEG, PNG, JPG, GIF, SVG',
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
                // CORREÇÃO: Salvar ícone customizado CORRETAMENTE
                if ($request->hasFile('icone_custom')) {
                    // 1. Obter o arquivo
                    $file = $request->file('icone_custom');
                    
                    // 2. Gerar nome único
                    $filename = 'icone_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // 3. Salvar no diretório correto (sem 'arquivos/' no início)
                    $iconeCustomPath = $file->storeAs('interesses/icones', $filename, 'public');
                    
                    // 4. Log para debug
                    Log::info('Ícone salvo em: ' . $iconeCustomPath);
                    Log::info('Caminho completo: ' . storage_path('app/public/' . $iconeCustomPath));
                    
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
            Log::error('Trace: ' . $e->getTraceAsString());
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
            
            // Deletar o arquivo do ícone se existir
            if ($interesse->icone_custom && Storage::disk('public')->exists($interesse->icone_custom)) {
                Storage::disk('public')->delete($interesse->icone_custom);
            }
            
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
            'icone_custom' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
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
                if ($interesse->icone_custom && Storage::disk('public')->exists($interesse->icone_custom)) {
                    Storage::disk('public')->delete($interesse->icone_custom);
                    $interesse->icone_custom = null;
                }
            } else {
                // Manter ícone customizado existente ou fazer upload novo
                if ($request->hasFile('icone_custom')) {
                    // Deletar ícone antigo se existir
                    if ($interesse->icone_custom && Storage::disk('public')->exists($interesse->icone_custom)) {
                        Storage::disk('public')->delete($interesse->icone_custom);
                    }
                    
                    // CORREÇÃO: Salvar ícone corretamente
                    $file = $request->file('icone_custom');
                    $filename = 'icone_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $iconeCustomPath = $file->storeAs('interesses/icones', $filename, 'public');
                    
                    $interesse->icone_custom = $iconeCustomPath;
                    $interesse->icone = 'custom';
                    
                    Log::info('Ícone atualizado para: ' . $iconeCustomPath);
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
            Log::error('Trace: ' . $e->getTraceAsString());
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
        
        $usuarioEhDono = $dono && $usuario->id === $dono->id;
        $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
        
        if (!$usuarioEhDono && !$usuarioEhAdministrador) {
            return redirect()->route('interesses.show', $slug)
                ->with('error', 'Você não tem permissão para gerenciar moderadores.');
        }
        
        // Carregar moderadores com a tabela correta
        $moderadores = $interesse->moderadores()
            ->select('tb_usuario.*', 'interesse_moderadores.cargo', 'interesse_moderadores.created_at')
            ->get();
        
        $tendenciasPopulares = Tendencia::populares(7)->get();
        
        return view('interesses.moderadores', compact(
            'interesse', 
            'tendenciasPopulares',
            'usuarioEhDono', 
            'usuarioEhAdministrador',
            'moderadores',
            'dono'
        ));
    }
    
    /**
     * Adicionar moderador
     */
    public function adicionarModerador(Request $request, $slug)
    {
        try {
            Log::info('=== ADICIONAR MODERADOR INICIADO ===');
            Log::info('Slug: ' . $slug);
            Log::info('Usuário autenticado: ' . (Auth::id() ?? 'null'));
            Log::info('Dados recebidos: ' . json_encode($request->all()));
            
            $interesse = Interesse::where('slug', $slug)->firstOrFail();
            $usuario = Auth::user();
            
            if (!$usuario) {
                Log::error('Usuário não autenticado');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não autenticado'
                ], 401);
            }
            
            // Verificar permissões
            $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
            $usuarioEhDono = $dono && $usuario->id === $dono->id;
            $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
            
            if (!$usuarioEhDono && !$usuarioEhAdministrador) {
                Log::error('Usuário sem permissão. ID: ' . $usuario->id . ', Dono: ' . ($dono ? $dono->id : 'null'));
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Apenas o dono pode adicionar moderadores'
                ], 403);
            }
            
            $request->validate([
                'usuario_id' => 'required|exists:tb_usuario,id'
            ]);
            
            $novoModerador = Usuario::find($request->usuario_id);
            
            if (!$novoModerador) {
                Log::error('Usuário não encontrado: ' . $request->usuario_id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não encontrado'
                ], 404);
            }
            
            // Verificar se já é moderador
            if ($interesse->moderadores()->where('usuario_id', $novoModerador->id)->exists()) {
                Log::warning('Usuário já é moderador. ID: ' . $novoModerador->id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => $novoModerador->apelido . ' já é moderador deste interesse'
                ], 400);
            }
            
            // Não permitir adicionar o dono como moderador (já é)
            if ($dono && $novoModerador->id === $dono->id) {
                Log::warning('Tentativa de adicionar dono como moderador');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'O dono já possui privilégios totais'
                ], 400);
            }
            
            // Adicionar como moderador
            $interesse->moderadores()->attach($novoModerador->id, [
                'cargo' => 'moderador',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('✅ Moderador adicionado com sucesso!');
            Log::info('Interesse: ' . $interesse->nome);
            Log::info('Novo moderador: ' . $novoModerador->user);
            Log::info('Adicionado por: ' . $usuario->user);
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => $novoModerador->apelido . ' adicionado como moderador com sucesso!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação: ' . json_encode($e->errors()));
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Dados inválidos',
                'erros' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('ERRO: ' . $e->getMessage());
            Log::error('Arquivo: ' . $e->getFile());
            Log::error('Linha: ' . $e->getLine());
            
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro interno: ' . $e->getMessage(),
                'debug' => env('APP_DEBUG') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ] : null
            ], 500);
        }
    }
    
    /**
     * Remover moderador
     */
    public function removerModerador(Request $request, $slug)
    {
        try {
            Log::info('=== REMOVER MODERADOR INICIADO ===');
            Log::info('Slug: ' . $slug);
            Log::info('Usuário autenticado: ' . (Auth::id() ?? 'null'));
            Log::info('Dados recebidos: ' . json_encode($request->all()));
            
            $interesse = Interesse::where('slug', $slug)->firstOrFail();
            $usuario = Auth::user();
            
            if (!$usuario) {
                Log::error('Usuário não autenticado');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não autenticado'
                ], 401);
            }
            
            // Verificar permissões
            $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
            $usuarioEhDono = $dono && $usuario->id === $dono->id;
            $usuarioEhAdministrador = $usuario->tipo_usuario == 1;
            
            Log::info('Dono encontrado: ' . ($dono ? 'ID=' . $dono->id . ', Nome=' . $dono->user : 'Nenhum'));
            Log::info('Usuário é dono? ' . ($usuarioEhDono ? 'SIM' : 'NÃO'));
            Log::info('Usuário é admin? ' . ($usuarioEhAdministrador ? 'SIM' : 'NÃO'));
            
            if (!$usuarioEhDono && !$usuarioEhAdministrador) {
                Log::error('Usuário sem permissão. ID: ' . $usuario->id . ', Dono: ' . ($dono ? $dono->id : 'null'));
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Apenas o dono pode remover moderadores'
                ], 403);
            }
            
            $request->validate([
                'usuario_id' => 'required|exists:tb_usuario,id'
            ]);
            
            $moderador = Usuario::find($request->usuario_id);
            
            if (!$moderador) {
                Log::error('Moderador não encontrado: ' . $request->usuario_id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não encontrado'
                ], 404);
            }
            
            Log::info('Moderador a ser removido: ID=' . $moderador->id . ', Nome=' . $moderador->user);
            
            // Não permitir remover o dono
            if ($dono && $moderador->id === $dono->id) {
                Log::warning('Tentativa de remover o dono: ID=' . $moderador->id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Não é possível remover o dono do interesse'
                ], 400);
            }
            
            // Verificar se realmente é moderador
            $ehModerador = $interesse->moderadores()
                ->where('usuario_id', $moderador->id)
                ->exists();
                
            if (!$ehModerador) {
                Log::warning('Usuário não é moderador: ID=' . $moderador->id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => $moderador->apelido . ' não é moderador deste interesse'
                ], 400);
            }
            
            // Remover moderador
            $interesse->moderadores()->detach($moderador->id);
            
            Log::info('✅ Moderador removido com sucesso!');
            Log::info('Interesse: ' . $interesse->nome);
            Log::info('Moderador removido: ' . $moderador->user);
            Log::info('Removido por: ' . $usuario->user);
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => $moderador->apelido . ' removido como moderador com sucesso!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação: ' . json_encode($e->errors()));
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Dados inválidos',
                'erros' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('ERRO: ' . $e->getMessage());
            Log::error('Arquivo: ' . $e->getFile());
            Log::error('Linha: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro interno: ' . $e->getMessage(),
                'debug' => env('APP_DEBUG') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ] : null
            ], 500);
        }
    }
    
    public function transferirPropriedade(Request $request, $slug)
    {
        // INICIAR LOG COMPLETO
        Log::info('=== TRANSFERIR PROPRIEDADE INICIADO ===');
        Log::info('Slug: ' . $slug);
        Log::info('Usuário ID: ' . (Auth::id() ?? 'null'));
        Log::info('Dados recebidos: ' . json_encode($request->all()));
        Log::info('Token: ' . $request->bearerToken() ?? 'null');
        Log::info('Content-Type: ' . $request->header('Content-Type'));
        Log::info('X-CSRF-TOKEN: ' . $request->header('X-CSRF-TOKEN'));
        
        try {
            // 1. Encontrar interesse
            $interesse = Interesse::where('slug', $slug)->first();
            
            if (!$interesse) {
                Log::error('Interesse não encontrado: ' . $slug);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Interesse não encontrado: ' . $slug
                ], 404);
            }
            
            Log::info('Interesse encontrado: ID=' . $interesse->id . ', Nome=' . $interesse->nome);
            
            // 2. Usuário autenticado
            $usuario = Auth::user();
            if (!$usuario) {
                Log::error('Usuário não autenticado');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não autenticado'
                ], 401);
            }
            
            Log::info('Usuário autenticado: ID=' . $usuario->id . ', Nome=' . $usuario->user);
            
            // 3. Validar dados
            if (!$request->has('novo_dono_id')) {
                Log::error('Campo novo_dono_id não encontrado no request');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'ID do novo dono é obrigatório'
                ], 400);
            }
            
            $novoDonoId = $request->novo_dono_id;
            Log::info('Novo dono ID recebido: ' . $novoDonoId);
            
            // 4. Buscar novo dono
            $novoDono = Usuario::find($novoDonoId);
            if (!$novoDono) {
                Log::error('Novo dono não encontrado: ID=' . $novoDonoId);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Usuário selecionado não encontrado (ID: ' . $novoDonoId . ')'
                ], 404);
            }
            
            Log::info('Novo dono encontrado: ID=' . $novoDono->id . ', Nome=' . $novoDono->user);
            
            // 5. Verificar dono atual
            Log::info('Verificando dono atual...');
            $donoAtual = $interesse->moderadores()
                ->wherePivot('cargo', 'dono')
                ->first();
            
            Log::info('Dono atual encontrado: ' . ($donoAtual ? 'ID=' . $donoAtual->id : 'Nenhum'));
            
            if (!$donoAtual) {
                Log::warning('Interesse sem dono definido. Usando criador como dono.');
            }
            
            if ($donoAtual && $donoAtual->id !== $usuario->id) {
                Log::error('Usuário não é dono atual. Dono atual: ' . $donoAtual->id . ', Usuário: ' . $usuario->id);
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Apenas o dono atual pode transferir a propriedade. Dono atual: ' . ($donoAtual->apelido ?? $donoAtual->user)
                ], 403);
            }
            
            // 6. Não permitir transferir para si mesmo
            if ($novoDono->id === $usuario->id) {
                Log::error('Tentativa de transferir para si mesmo');
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Você já é o dono deste interesse'
                ], 400);
            }
            
            // 7. Verificar se novo dono já é moderador
            $jaEhModerador = $interesse->moderadores()
                ->where('usuario_id', $novoDono->id)
                ->exists();
            
            Log::info('Novo dono já é moderador? ' . ($jaEhModerador ? 'SIM' : 'NÃO'));
            
            // 8. TRANSFERIR PROPRIEDADE
            Log::info('Iniciando transferência...');
            
            // Remover cargo de dono do atual
            if ($donoAtual) {
                $interesse->moderadores()->updateExistingPivot($usuario->id, [
                    'cargo' => $jaEhModerador ? 'moderador' : 'ex-dono',
                    'updated_at' => now()
                ]);
                Log::info('Dono anterior atualizado para: ' . ($jaEhModerador ? 'moderador' : 'ex-dono'));
            }
            
            // Definir novo dono
            if ($jaEhModerador) {
                $interesse->moderadores()->updateExistingPivot($novoDono->id, [
                    'cargo' => 'dono',
                    'updated_at' => now()
                ]);
                Log::info('Moderador promovido a dono');
            } else {
                $interesse->moderadores()->attach($novoDono->id, [
                    'cargo' => 'dono',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info('Novo dono adicionado');
            }
            
            // 9. Garantir que novo dono segue o interesse
            if (!$interesse->seguidores()->where('usuario_id', $novoDono->id)->exists()) {
                $interesse->seguidores()->attach($novoDono->id);
                Log::info('Novo dono adicionado como seguidor');
            }
            
            // 10. Log de sucesso
            Log::info('✅ Transferência concluída com sucesso!');
            Log::info('Interesse: ' . $interesse->nome);
            Log::info('De: ' . $usuario->user . ' (ID: ' . $usuario->id . ')');
            Log::info('Para: ' . $novoDono->user . ' (ID: ' . $novoDono->id . ')');
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Propriedade transferida para ' . ($novoDono->apelido ?? $novoDono->user) . ' com sucesso!',
                'debug' => [
                    'interesse' => $interesse->nome,
                    'antigo_dono' => $usuario->user,
                    'novo_dono' => $novoDono->user,
                    'transferido_em' => now()->toDateTimeString()
                ]
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('ERRO DE BANCO DE DADOS: ' . $e->getMessage());
            Log::error('SQL: ' . $e->getSql());
            Log::error('Bindings: ' . json_encode($e->getBindings()));
            
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro de banco de dados: ' . $e->getMessage(),
                'debug' => env('APP_DEBUG') ? [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings()
                ] : null
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('ERRO GERAL: ' . $e->getMessage());
            Log::error('Arquivo: ' . $e->getFile());
            Log::error('Linha: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro interno: ' . $e->getMessage(),
                'debug' => env('APP_DEBUG') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * API para buscar todos interesses
     */
    public function todosInteresses(Request $request)
    {
        $limit = $request->get('limit', 20);
        
        $interesses = Interesse::ativos()
            ->withCount('seguidores')
            ->orderBy('seguidores_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($interesse) {
                return [
                    'id' => $interesse->id,
                    'nome' => $interesse->nome,
                    'slug' => $interesse->slug,
                    'descricao' => $interesse->descricao,
                    'icone' => $interesse->icone,
                    'icone_custom' => $interesse->icone_custom,
                    'cor' => $interesse->cor,
                    'seguidores_count' => $interesse->seguidores_count,
                    'contador_membros' => $interesse->contador_membros,
                    'tipo' => 'interesse'
                ];
            });
        
        return response()->json([
            'sucesso' => true,
            'interesses' => $interesses
        ]);
    }

    /**
     * API para servir arquivos do storage (solução temporária)
     */
    public function servirArquivo(Request $request, $path)
    {
        $storagePath = storage_path('app/public/' . $path);
        
        if (!file_exists($storagePath)) {
            Log::warning('Arquivo não encontrado: ' . $path);
            abort(404);
        }
        
        $mimeType = mime_content_type($storagePath);
        
        return response()->file($storagePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000'
        ]);
    }
}