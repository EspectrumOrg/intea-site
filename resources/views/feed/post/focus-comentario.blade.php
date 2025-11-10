<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/feed/postagem-read/comentario-focus.css') }}">

@extends('feed.post.template.layout')

@section('main')
<div class="container-comentario-focus">
    <div class="content">
        <div class="topo">
            <a class="voltar" href="{{ route('post.read', ['postagem' => $comentario->id_postagem]) }}">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="text">
                <h1>Comentário de {{$comentario->usuario->user}}</h1>
            </div>
        </div>
    </div>

    <!--------------------------------- Conteúdo Comentário Postagem -------------------------------------->
    <div class="comentario-foco">
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}" class="foto-user">
                <img
                    src="{{ asset('storage/'.$comentario->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                    alt="foto perfil">
            </a>
            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                    <h1>{{ $comentario->usuario->apelido }}</h1>
                </a>
                <h2>{{ $comentario->usuario->user }}</h2>
            </div>
        </div>

        <div class="dropdown"> <!--------- Dropdown --------->
            <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                <span class="material-symbols-outlined">more_horiz</span>
            </button>
            <ul class="dropdown-content">
                @if(Auth::id() === $comentario->usuario->id)
                <li>
                    <!-- Editar Comentário -->
                    <button type="button"
                        class="btn-acao editar btn-abrir-modal-edit-comentario"
                        onclick="abrirModalEditarComentario('{{ $comentario->id }}')">
                        <span class="material-symbols-outlined">edit</span>Editar
                    </button>
                </li>
                <li>
                    <!-- Excluir Comentário -->
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
                    <!-- Banir Usuário (caso admin) -->
                    <div class="form-excluir">
                        <button type="button" class=" btn-acao btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoUsuarioEspecifico('{{ $comentario->usuario->id }}')">
                            <span class="material-symbols-outlined">person_off</span>Banir
                        </button>
                    </div>
                    @else
                    <!-- Denunciar Usuário -->
                    <a class="btn-acao denunciar" href="javascript:void(0)" onclick="abrirModalDenunciaComentario('{{ $comentario->id }}')">
                        <span class="material-symbols-outlined">flag_2</span>Denunciar
                    </a>
                    @endif
                </li>
                <li>
                    <form action="{{ route('seguir.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $comentario->usuario_id }}">
                        <button type="submit" class="btn-acao seguir-btn">
                            <span class="material-symbols-outlined">person_add</span>Seguir {{ $comentario->usuario->user }}
                        </button>
                    </form>
                </li>
                @endif
            </ul>
        </div>

        <!-- Modal Edição desse comentario -->
        @include('feed.post.comentario.comentario-edit', ['comentario' => $comentario])

        <!-- Modal Criação de comentário ($comentario->id) -->
        @include('feed.post.create-resposta-modal', ['comentario' => $comentario])

        <!-- modal banir-->
        @include('layouts.partials.modal-banimento', ['usuario' => $comentario->usuario])

        <!-- Modal de denúncia (um para cada comentario) -->
        <div id="modal-denuncia-comentario-{{ $comentario->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <span class="close material-symbols-outlined" onclick="fecharModalDenunciaComentario('{{$comentario->id}}')">close</span>
                <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                    @csrf
                    <div class="form">
                        <input type="hidden" name="tipo" value="comentario">
                        <input type="hidden" name="id_alvo" value="{{ $comentario->id }}">
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
        <p class="texto-completo" id="texto-completo-{{ $comentario->id }}">{{ $comentario->comentario }}</p>
        @if ($comentario->image)
        <img src="{{ asset('storage/' . $comentario->image->caminho_imagem) }}" class="card-img-top" alt="Imagem da comentario">
        @endif
        <div class="dados">
            <h3>{{ $comentario->created_at->translatedFormat('g:i A . M j, Y') }}</h3>
        </div>
    </div>

    <!----------------------------- Curtidas e comentários ----------------------------------->
    <div class="dados-post">
        <div>
            <button type="button" class="button btn-comentar">
                <a>
                    <span class="material-symbols-outlined">chat_bubble</span>
                    <h1>{{ $comentario->comentarios_count }}</h1>
                </a>
            </button>
        </div>

        <form method="POST" action="{{ route('curtida.toggle') }}">
            @csrf
            <input type="hidden" name="tipo" value="comentario">
            <input type="hidden" name="id" value="{{ $comentario->id }}">
            <button type="submit" class="button btn-curtir {{ $comentario->curtidas_usuario ? 'curtido' : 'normal' }}">
                <span class="material-symbols-outlined">favorite</span>
                <h1>{{ $comentario->curtidas_count }}</h1>
            </button>
        </form>
    </div>

    <!-------------------------------------------- Form de resposta ---------------------------->
    <div class="form-create-resposta-comentario-focus">
        <img
            src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
            alt="foto de perfil"
            style="border-radius: 50%; object-fit:cover;"
            width="40"
            height="40"
            loading="lazy">

        <form action="{{ route('post.comentario', ['tipo' => 'comentario', 'id' => $comentario->id]) }}" method="POST" class="form" enctype="multipart/form-data">
            @csrf
            <div class="textfield">
                <div id="hashtag-preview-create-resposta-comentario-focus" class="hashtag-preview"></div>

                <textarea id="texto_resposta_comentario_focus"
                    name="comentario"
                    maxlength="280"
                    rows="4"
                    placeholder="Responda o comentário de {{ $comentario->usuario->user }}" required></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('comentario')" />

                {{-- Preview da imagem --}}
                <div id="image-preview-create-resposta-comentario-focus" class="image-preview" style="display: none;">
                    <img id="preview-img-create-resposta-comentario-focus" src="" alt="Prévia da imagem">
                    <button type="button" id="remove-image-create-resposta-comentario-focus" class="remove-image">
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </button>
                </div>
            </div>

            <div class="content">
                <div class="extras">
                    <label for="caminho_imagem_create_resposta_comentario_focus" class="upload-label">
                        <span class="material-symbols-outlined">image</span>
                    </label>
                    <input
                        id="caminho_imagem_create_resposta_comentario_focus"
                        name="caminho_imagem"
                        type="file"
                        accept="image/*"
                        class="input-file">
                    <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
                </div>

                <div class="contador">
                    <span id="char-count-create-resposta_comentario_focus">0</span>/280
                </div>

                <div class="botao-submit">
                    <button type="submit" class="botao-postar">Postar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- formulário para responder -->
    @include('feed.post.create-resposta-modal', ['comentario' => $comentario])

    <!-- Respostas -->
    <div class="comentarios">
        @foreach($comentario->respostas as $resposta)
        <div class="comentario">

            <div class="foto-comentario"> <!--foto-->
                <img
                    src="{{ asset('storage/'.$resposta->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    style="border-radius: 50%; object-fit:cover;"
                    width="40"
                    height="40"
                    loading="lazy">
            </div>

            <div class="dados"> <!--dados-->

                <div class="topo-resposta">
                    <div class="info-user-resposta">
                        <a href="{{ route('conta.index', ['usuario_id' => $resposta->usuario->id]) }}">
                            <strong>{{ $resposta->usuario->apelido }}</strong>
                        </a>
                        <strong>{{ $resposta->usuario->user }}</strong>
                        <span>{{ $resposta->created_at->diffForHumans() }}</span>
                    </div>


                    <div class="dropdown"> <!--------- Dropdown --------->
                        <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                            <span class="material-symbols-outlined">more_horiz</span>
                        </button>
                        <ul class="dropdown-content">
                            @if(Auth::id() === $resposta->usuario->id)
                            <li>
                                <button type="button"
                                    class="btn-acao editar btn-abrir-modal-edit-comentario"
                                    onclick="abrirModalEditarComentario('{{ $resposta->id }}')">
                                    <span class="material-symbols-outlined">edit</span>Editar
                                </button>
                            </li>
                            <li>
                                <form action="{{ route('post.destroy', $resposta->id) }}" method="POST" style="display:inline">
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
                                <!-- Botão que abre o modal -->
                                <div class="form-excluir">
                                    <button type="button" class="btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoUsuarioEspecifico('{{ $resposta->usuario->id }}')">
                                        <span class="material-symbols-outlined">person_off</span>Banir
                                    </button>
                                </div>
                                @else
                                <a class="btn-acao denunciar" href="javascript:void(0)" onclick="abrirModalDenunciaComentario('{{ $resposta->id }}')">
                                    <span class="material-symbols-outlined">flag_2</span>Denunciar
                                </a>
                                @endif
                            </li>
                            <li>
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $resposta->usuario_id }}">
                                    <button type="submit" class="btn-acao seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span>Seguir {{ $resposta->usuario->user }}
                                    </button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Modal Edição dessa resposta -->
                @include('feed.post.comentario.comentario-edit', ['comentario' => $resposta])

                <!-- modal banir-->
                @include('layouts.partials.modal-banimento', ['usuario' => $resposta->usuario])

                <!-- Modal de denúncia (um para cada resposta) -->
                <div id="modal-denuncia-comentario-{{ $resposta->id }}" class="modal-denuncia hidden">
                    <div class="modal-content">
                        <span class="close material-symbols-outlined" onclick="fecharModalDenunciaComentario('{{$resposta->id}}')">close</span>
                        <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                            @csrf
                            <div class="form">
                                <input type="hidden" name="tipo" value="comentario">
                                <input type="hidden" name="id_alvo" value="{{ $resposta->id }}">
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

                <p>{{ $resposta->comentario}}</p>

                @if(!empty($resposta->image))
                <img
                    class="imagem-comentario"
                    src="{{ asset('storage/' . $resposta->image->caminho_imagem) }}"
                    alt="Imagem respsota">
                @endif
                <!----------------------------- Curtidas -------------------->
                <div class="dados-post dados-post-resposta">
                    <form method="POST" action="{{ route('curtida.toggle') }}">
                        @csrf
                        <input type="hidden" name="tipo" value="comentario">
                        <input type="hidden" name="id" value="{{ $resposta->id }}">
                        <button type="submit" class="button btn-curtir {{ $resposta->curtidas_usuario ? 'curtido' : 'normal' }}">
                            <span class="material-symbols-outlined">favorite</span>
                            <h1>{{ $resposta->curtidas_count }}</h1>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection