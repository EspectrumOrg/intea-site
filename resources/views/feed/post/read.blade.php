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
                <img src="{{ $postagem->usuario->foto ? asset('storage/'.$postagem->usuario->foto) : asset('assets/images/logos/contas/user.png') }}" alt="foto perfil">
            </a>
            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                    <h1>{{ Str::limit($postagem->usuario->apelido ?? 'Desconhecido', 25, '...') }}</h1>
                </a>
                <h2>{{ $postagem->usuario->user }}</h2>
            </div>
        </div>

        <div class="dropdown"> <!--------- Dropdown --------->
            <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                <span class="material-symbols-outlined">more_horiz</span>
            </button>
            <ul class="dropdown-content">
                @if(Auth::id() === $postagem->usuario_id)
                <li>
                    <button type="button" class="btn-acao editar" onclick="abrirModalEditar('{{ $postagem->id }}')">
                        <img src="{{ asset('assets/images/logos/symbols/site-claro/write.png') }}">Editar
                    </button>
                </li>
                <li>
                    <form action="{{ route('post.destroy', $postagem->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-acao excluir">
                            <img src="{{ asset('assets/images/logos/symbols/site-claro/trash.png') }}">Excluir
                        </button>
                    </form>
                </li>
                @else
                <li><a style="border-radius: 15px 15px 0 0;" href="javascript:void(0)" onclick="abrirModalDenuncia('{{ $postagem->id }}')"><img src="{{ asset('assets/images/logos/symbols/site-claro/flag.png') }}">Denunciar</a></li>
                <li>
                    <form action="{{ route('seguir.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $postagem->usuario_id }}">
                        <button type="submit" class="seguir-btn">
                            <img src="{{ asset('assets/images/logos/symbols/site-claro/follow.png') }}">Seguir {{ $postagem->usuario->user }}
                        </button>
                    </form>
                </li>
                @endif
            </ul>
        </div>

        <!-- Modal Edição dessa postagem -->
        <div id="modal-editar-{{ $postagem->id }}" class="modal hidden">
            <div class="modal-content">
                <button type="button" class="close" onclick="fecharModalEditar('{{ $postagem->id }}')">&times;</button>
                <div class="modal-content-content">
                    @include('feed.post.edit', ['postagem' => $postagem])
                </div>
            </div>
        </div>

        <!-- Modal Criação de comentário ($postagem->id) -->
        <div id="modal-comentar-{{ $postagem->id }}" class="modal hidden">
            <div class="modal-content">
                <button type="button" class="close" onclick="fecharModalComentar('{{ $postagem->id }}')">&times;</button>
                <div class="modal-content-content">
                    @include('feed.post.create-comentario-modal', ['postagem' => $postagem])
                </div>
            </div>
        </div>

        <!-- Modal de denúncia (um para cada postagem) -->
        <div id="modal-denuncia-postagem-{{ $postagem->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <span class="close" onclick="fecharModalDenuncia('{{$postagem->id}}')">&times;</span>

                <form method="POST" style="width: 100%;" action="{{ route('post.denuncia', [$postagem->id, Auth::user()->id]) }}">
                    @csrf
                    <div class="form">
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

    <!----------------------------- Curtidas e comentários ----------------------------------->
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

            <form method="POST" action="{{ route('post.curtida', $postagem->id) }}">
                @csrf
                <button type="submit" class="button btn-curtir {{ $postagem->curtidas_usuario ? 'curtido' : 'normal' }}">
                    <span class="material-symbols-outlined">favorite</span>
                    <h1>{{ $postagem->curtidas_count }}</h1>
                </button>
            </form>
        </div>
    </div>
</div>

<!-------------------------------------------- Form de comentário ---------------------------->
<div class="form-comentario">
    <form action="{{ route('post.comentario', ['tipo' => 'postagem', 'id' => $postagem->id]) }}" method="POST" enctype="multipart/form-data">
        <div style="display: flex;">
            <div class="foto-perfil">
                @if (!empty(Auth::user()->foto))
                <img src="{{ asset('storage/'.Auth::user()->foto) }}" alt="foto perfil">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
                @endif
            </div>
            @csrf
            <div style="width: 100%;">
                <div>
                    <textarea
                        id="texto_comentario"
                        name="comentario"
                        maxlength="280"
                        rows="4"
                        placeholder="Responda a publicação de {{ $postagem->usuario->user }}" required></textarea>
                </div>

                <div style="display: flex; justify-content:space-between; padding: 0 1rem">
                    <div class="extras">
                        <label for="caminho_imagem" class="upload-label">
                            <img src="{{ url('assets/images/logos/symbols/image.png') }}" class="card-img-top" alt="adicionar imagem">
                        </label>
                        <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-file">
                        <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
                    </div>

                    <div class="contador">
                        <span id="char-count-comentario">0</span>/280
                    </div>

                    <div class="botao-submit">
                        <button type="submit" class="botao-postar">Publicar</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<!------------------------------ Lista de comentários ----------------------------------------------------------->
<div class="comentarios">
    @foreach($postagem->comentarios->whereNull('id_comentario_pai') as $comentario)
    <div class="comentario">
        <a href="{{ route('comentario.focus', ['id' => $comentario->id]) }}" class="post-overlay"></a>

        <div class="foto-comentario"> <!--foto-->
            @if (!empty($comentario->usuario->foto))
            <img src="{{ asset('storage/' . $comentario->usuario->foto) }}" alt="foto perfil">
            @else
            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
            @endif
        </div>

        <div class="dados"> <!--dados-->
            <strong>{{ $comentario->usuario->nome }}</strong>
            <span>{{ $comentario->created_at->diffForHumans() }}</span>
            <p>{{ $comentario->comentario }}</p>

            @if(!empty($comentario->image))
            <img src="{{ asset('storage/' . $comentario->image->caminho_imagem) }}" alt="Imagem comentário">
            @endif

            <div class="interacoes">
                <div class="corpo">
                    <div>
                        <button type="button" onclick="toggleForm('{{ $comentario->id }}')" class="button">
                            <a href="javascript:void(0)" onclick="abrirModalComentar('{{ $comentario->id }}')"><img src="{{ asset('assets/images/logos/symbols/site-claro/coment.png') }}"></a>
                            <h1>{{ $comentario->comentarios_count }}</h1>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('post.curtida', $comentario->id) }}">
                        @csrf
                        <button type="submit" class="button">
                            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (!! $comentario->curtidas_usuario ? 'like-preenchido.png' : 'like.png')) }}">
                            <h1>{{ $comentario->curtidas_count }}</h1>
                        </button>
                    </form>
                </div>

            </div>
        </div>

        @if($comentario->respostas->isNotEmpty()) <!--resposta-->
        <div class="respostas ms-4 mt-2 border-start ps-3">
            @foreach($comentario->respostas as $resposta)
            <div class="resposta mb-2">
                <a href="{{ route('comentario.focus', $resposta->id) }}">
                    <strong>{{ $resposta->usuario->user }}</strong>
                </a>
                <p>{{ $resposta->comentario }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- modal resposta comentário-->
    <div id="modal-comentar-{{ $comentario->id }}" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="close" onclick="fecharModalComentar('{{ $comentario->id }}')">&times;</button>
            <div class="modal-content-content">
                @include('feed.post.create-resposta-modal', ['comentario' => $comentario])
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection