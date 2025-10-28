<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/feed/postagem-read/style.css') }}">

@extends('feed.post.template.layout')

@section('main')

<div class="container-read">
    <div class="content">
        <div class="topo">
            <a class="voltar" href="{{ route('post.index') }}">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="text">
                <h1>Postagem</h1>
            </div>
        </div>
    </div>

    <!--------------------------------- Conteúdo Postagem -------------------------------------->
    <div class="postagem-foco">
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}" class="foto-user">
                <img
                    src="{{ $postagem->usuario->foto ? asset('storage/'.$postagem->usuario->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto perfil"
                    class="foto-user-padrao">
            </a>
            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                    <h1>{{ Str::limit($postagem->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                </a>
                <h2>{{ $postagem->usuario->apelido }}</h2>
            </div>
        </div>

        <div class="dropdown"> <!--------- Dropdown --------->
            <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                <span class="material-symbols-outlined">more_horiz</span>
            </button>
            <ul class="dropdown-content">
                @if(Auth::id() === $postagem->usuario_id)
                <li>
                    <button type="button"
                        class="btn-acao editar btn-abrir-modal-edit-postagem"
                        onclick="abrirModalEditar('{{ $postagem->id }}')">
                        <span class="material-symbols-outlined">edit</span>Editar
                    </button>
                </li>
                <li>
                    <form action="{{ route('post.destroy', $postagem->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-acao excluir">
                            <span class="material-symbols-outlined">delete</span>Excluir
                        </button>
                    </form>
                </li>
                @else
                <!-- Caso não tenha sido quem postou --------------------->
                <li>
                    @if( Auth::user()->tipo_usuario === 1 )
                    <form action="{{ route('usuario.destroy', $postagem->usuario_id) }}" method="post" class="form-excluir">
                        @csrf
                        @method("delete")
                        <button type="submit" onclick="return confirm('Você tem certeza que deseja banir esse usuário?');" class="btn-excluir-usuario">
                            <span class="material-symbols-outlined">person_off</span>
                            Banir usuário
                        </button>
                    </form>
                    @else
                    <a style="display: flex; gap:1rem; border-radius: 15px 15px 0 0;" href="javascript:void(0)" onclick="abrirModalDenuncia('{{ $postagem->id }}')">
                        <span class="material-symbols-outlined">flag_2</span>Denunciar
                    </a>
                    @endif
                </li>
                <li>
                    <form action="{{ route('seguir.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $postagem->usuario_id }}">
                        <button type="submit" class="seguir-btn">
                            <span class="material-symbols-outlined">person_add</span>Seguir {{ $postagem->usuario->user }}
                        </button>
                    </form>
                </li>
                @endif
            </ul>
        </div>

        <!-- Modal Edição dessa postagem -->
        @include('feed.post.edit', ['postagem' => $postagem])

        <!-- Modal Criação de comentário ($postagem->id) -->
        <div id="modal-comentar-{{ $postagem->id }}" class="modal hidden">
            <div class="modal-content">
                <button type="button" class="close" onclick="fecharModalComentar('{{ $postagem->id }}')">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="modal-content-content">
                    @include('feed.post.create-comentario-modal', ['postagem' => $postagem])
                </div>
            </div>
        </div>

        <!-- Modal de denúncia (um para cada postagem) -->
        <div id="modal-denuncia-postagem-{{ $postagem->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <span class="close material-symbols-outlined" onclick="fecharModalDenuncia('{{$postagem->id}}')">close</span>
                <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                    @csrf
                    <div class="form">
                        <input type="hidden" name="tipo" value="postagem">
                        <input type="hidden" name="id_alvo" value="{{ $postagem->id }}">
                        <label class="form-label">Motivo Denúncia</label>
                        <select class="form-select" id="motivo_denuncia" name="motivo_denuncia" required>
                            <option value="">Tipo</option>
                            <option value="spam">Spam</option>
                            <option value="desinformação">Desinformação</option>
                            <option value="conteudo_explicito">Conteúdo Explícito</option>
                            <option value="discurso_de_odio">Discurso de Ódio</option>
                        </select>
                    </div>

                    <div class="form-label">
                        <input class="form-control" name="texto_denuncia" type="text" placeholder="Explique o porquê da denúncia" value="{{ old('texto_denuncia') }}" required autocomplete="off">
                        <x-input-error class="mt-2" :messages="$errors->get('texto_denuncia')" />
                    </div>

                    <div style="display: flex; justify-content: end;">
                        <button type="submit">Denunciar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="image">
        <p class="texto-completo" id="texto-completo-{{ $postagem->id }}">{{ $postagem->texto_postagem }}</p>
        @if ($postagem->imagens->isNotEmpty() && $postagem->imagens->first()->caminho_imagem)
        <img src="{{ asset('storage/' . $postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
        @endif
        <div class="dados">
            <h3>{{ $postagem->created_at->translatedFormat('g:i A . M j, Y') }}</h3>
        </div>
    </div>

    <!----------------------------- Curtidas e comentários Postagem---------------------------->
    <div class="interacoes">
        <div class="corpo">
            <div class="comment">
                <button type="button" class="button btn-comentar">
                    <a>
                        <span class="material-symbols-outlined">chat_bubble</span>
                        <h1>{{ $postagem->comentarios_count }}</h1>
                    </a>
                </button>
            </div>

            <form method="POST" action="{{ route('curtida.toggle') }}">
                @csrf
                <input type="hidden" name="tipo" value="postagem">
                <input type="hidden" name="id" value="{{ $postagem->id }}">
                <button type="submit" class="button btn-curtir {{ $postagem->curtidas_usuario ? 'curtido' : 'normal' }}">
                    <span class="material-symbols-outlined">favorite</span>
                    <h1>{{ $postagem->curtidas_count }}</h1>
                </button>
            </form>
        </div>
    </div>
</div>

<!-------------------------------------------- Form de comentário ---------------------------->
<div class="form-create-comentario">
    <img
        src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
        alt="foto de perfil"
        class="foto-user-padrao"
        loading="lazy">

    <form action="{{ route('post.comentario', ['tipo' => 'postagem', 'id' => $postagem->id]) }}" method="POST" class="form" enctype="multipart/form-data">
        @csrf
        <div class="textfield">
            <div id="hashtag-preview-create-comentario" class="hashtag-preview"></div>

            <textarea id="texto_comentario"
                name="comentario"
                maxlength="280"
                rows="4"
                placeholder="Responda a publicação de {{ $postagem->usuario->user }}" required></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />

            {{-- Preview da imagem --}}
            <div id="image-preview-create-comentario" class="image-preview" style="display: none;">
                <img id="preview-img-create-comentario" src="" alt="Prévia da imagem">
                <button type="button" id="remove-image-create-comentario" class="remove-image">
                    <span class="material-symbols-outlined">
                        close
                    </span>
                </button>
            </div>
        </div>

        <div class="content">
            <div class="extras">
                <label for="caminho_imagem_create_comentario" class="upload-label">
                    <span class="material-symbols-outlined">image</span>
                </label>
                <input
                    id="caminho_imagem_create_comentario"
                    name="caminho_imagem"
                    type="file"
                    accept="image/*"
                    class="input-file">
                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
            </div>

            <div class="contador">
                <span id="char-count-create-comentario">0</span>/280
            </div>

            <div class="botao-submit">
                <button type="submit" class="botao-postar">Postar</button>
            </div>
        </div>
    </form>
</div>
<!------------------------------ Lista de comentários ------------------------------------------------------------------------------------------------------------------------->
<div class="comentarios">
    @foreach($postagem->comentarios->whereNull('id_comentario_pai') as $comentario)
    <div class="comentario">
        <a href="{{ route('comentario.focus', ['id' => $comentario->id]) }}" class="comentario-overlay"></a>


        <div class="foto-perfil">
            <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                <img
                    src="{{ $comentario->usuario->foto ? url('storage/' . $comentario->usuario->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="foto-user-padrao"
                    loading="lazy">
            </a>
        </div>

        <div class="corpo-content" style="width: 100%;">
            <div class="topo"> <!-- info conta -->
                <div class="info-perfil">
                    <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                        <h1>{{ Str::limit($comentario->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                    </a>
                    <h2>{{ $comentario->usuario->user }} . {{ $comentario->created_at->shortAbsoluteDiffForHumans() }}</h2>
                </div>

                <div class="dropdown"> <!-- opções comentario -->
                    <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                        <span class="material-symbols-outlined">more_horiz</span>
                    </button>
                    <ul class="dropdown-content">
                        @if(Auth::id() === $comentario->usuario->id)
                        <li>
                            <button type="button"
                                class="btn-acao editar btn-abrir-modal-edit-comentario"
                                onclick="abrirModalEditarComentario('{{ $comentario->id }}')">
                                <span class="material-symbols-outlined">edit</span>Editar
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('post.destroy', $comentario->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-acao excluir">
                                    <span class="material-symbols-outlined">delete</span>Excluir
                                </button>
                            </form>
                        </li>
                        @else
                        <!-- Caso não tenha sido quem postou --------------------->
                        <li>
                            @if( Auth::user()->tipo_usuario === 1 )
                            <form action="{{ route('usuario.destroy', $comentario->usuario->id) }}" method="post" class="form-excluir">
                                @csrf
                                @method("delete")
                                <button type="submit" onclick="return confirm('Você tem certeza que deseja banir esse usuário?');" class="btn-excluir-usuario">
                                    <span class="material-symbols-outlined">person_off</span>
                                    Banir usuário
                                </button>
                            </form>
                            @else
                            <a style="display: flex; gap:1rem; border-radius: 15px 15px 0 0;" href="javascript:void(0)" onclick="abrirModalDenunciaComentario('{{ $comentario->id }}')">
                                <span class="material-symbols-outlined">flag_2</span>Denunciar
                            </a>
                            @endif
                        </li>
                        <li>
                            <form action="{{ route('seguir.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $comentario->usuario->id }}">
                                <button type="submit" class="seguir-btn">
                                    <span class="material-symbols-outlined">person_add</span>Seguir {{ $comentario->usuario->user }}
                                </button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- conteudo comentario -->
            <div class="conteudo-post">
                <div class="coment-perfil">
                    <p class="texto-curto" id="texto-{{ $comentario->id }}">
                        {{ $comentario->comentario }}
                        @if (strlen($comentario->comentario) > 150)
                        <span class="mostrar-mais" onclick="toggleTexto('{{ $comentario->id }}', this)">...mais</span>
                        @endif
                    </p>

                    <p class="texto-completo" id="texto-completo-{{ $comentario->id }}" style="display: none;">
                        $comentario->comentario
                        <span class="mostrar-mais" onclick="toggleTexto('{{ $comentario->id }}', this)">...menos</span>
                    </p>
                </div>

                <div class="image-post">
                    @if ($comentario->image)
                    <img src="{{ asset('storage/' . $comentario->image->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                    @endif
                </div>


                <!-- curtidas e comentários ---------------------------------------------------------------------------------->
                <div class="dados-post interacoes">
                    <form method="POST" action="{{ route('curtida.toggle') }}">
                        @csrf
                        <input type="hidden" name="tipo" value="comentario">
                        <input type="hidden" name="id" value="{{ $comentario->id}}">
                        <button type="submit" class="button btn-curtir {{ $comentario->curtidas_usuario ? 'curtido' : 'normal' }}">
                            <span class="material-symbols-outlined">favorite</span>
                            <h1>{{ $comentario->curtidas_count }}</h1>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- modal resposta comentário------------------------------------------------>
    @include('feed.post.create-resposta-modal', ['comentario' => $comentario])

    <!-- Modal Edição dessa comentario -->
    @include('feed.post.comentario.comentario-edit', ['comentario' => $comentario])

    <!-- Modal de denúncia (um para cada postagem) -->
    <div id="modal-denuncia-comentario-{{ $comentario->id }}" class="modal-denuncia hidden">
        <div class="modal-content">
            <span class="close"
                onclick="fecharModalDenunciaComentario('{{$comentario->id}}')">
                <span class="material-symbols-outlined">close</span>
            </span>

            <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                @csrf
                <div class="form">
                    <input type="hidden" name="tipo" value="comentario">
                    <input type="hidden" name="id_alvo" value="{{ $comentario->id }}">
                    <label class="form-label">Motivo Denúncia</label>
                    <select class="form-select" id="motivo_denuncia" name="motivo_denuncia" required>
                        <option value="">Tipo</option>
                        <option value="spam">Spam</option>
                        <option value="desinformacao">Desinformação</option>
                        <option value="conteudo_explicito">Conteúdo Explícito</option>
                        <option value="discurso_de_odio">Discurso de Ódio</option>
                    </select>
                </div>

                <div class="form-label">
                    <input class="form-control" name="texto_denuncia" type="text" placeholder="Explique o porquê da denúncia" value="{{ old('texto_denuncia') }}" required autocomplete="off">
                    <x-input-error class="mt-2" :messages="$errors->get('texto_denuncia')" />
                </div>

                <div style="display: flex; justify-content: end;">
                    <button type="submit">Denunciar</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection