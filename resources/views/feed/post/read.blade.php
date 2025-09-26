@extends('feed.post.template.layout')

@section('main')
<div class="container-read">
    <div class="content">
        <div class="topo">
            <a class="voltar" href="{{ route('post.index') }}">
                <img src="{{ asset('assets/images/logos/symbols/back-button.png') }}">
            </a>
            <div class="text">
                <h1>Postagem</h1>
            </div>
        </div>
    </div>

    <!--------------------------------- Conteúdo Postagem -------------------------------------->
    <div class="postagem-foco">
        <div class="foto-perfil">
            <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                @if (!empty($postagem->usuario->foto))
                <img src="{{ asset('storage/'.$postagem->usuario->foto) }}" alt="foto perfil">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
                @endif
            </a>
            <div>
                <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                    <h1>{{ Str::limit($postagem->usuario->apelido ?? 'Desconhecido', 25, '...') }}</h1>
                </a>
                <h2>{{ $postagem->usuario->user }}</h2>
            </div>
        </div>

        <div class="dropdown">
            <button class="menu-opcoes">
                <img src="{{ asset('assets/images/logos/symbols/site-claro/three-dots.png') }}">
            </button>
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
                <img src="{{ asset('assets/images/logos/symbols/site-claro/coment.png') }}">
                <h1>{{ $postagem->comentarios_count }}</h1>
            </div>

            <form method="POST" action="{{ route('post.curtida', $postagem->id) }}">
                @csrf
                <button type="submit" class="button">
                    <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (!! $postagem->curtidas_usuario ? 'like-preenchido.png' : 'like.png')) }}">
                    <h1>{{ $postagem->curtidas_count }}</h1>
                </button>
            </form>
        </div>
    </div>
</div>

<!-------------------------------------------- Form de comentário ---------------------------->
<div class="form-comentario">
    <form action="{{ route('post.comentario', $postagem->id) }}" method="POST" enctype="multipart/form-data">
        <div style="display: flex;">
            <div class="foto-perfil">
                @if (!empty(Auth::user()->foto))
                <img src="{{ asset('storage/'.Auth::user()->foto) }}" alt="foto perfil">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
                @endif
            </div>
            @csrf
            <textarea
                id="texto_comentario"
                name="comentario"
                maxlength="280"
                rows="4"
                placeholder="Responda a publicação de {{ $postagem->usuario->user }}" required></textarea>
        </div>

        <div style="display: flex; justify-content:space-between;">
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

    </form>
</div>

<!------------------------------ Lista de comentários ----------------------------------------------------------->
<div class="comentarios">
    @foreach($postagem->comentarios as $comentario)
    <div class="comentario">

        <div class="foto-comentario">
            @if (!empty($comentario->usuario->foto))
            <img src="{{ asset('storage/' . $comentario->usuario->foto) }}" alt="foto perfil">
            @else
            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
            @endif
        </div>

        <div class="dados">
            <strong>{{ $comentario->usuario->nome }}</strong>
            <span>{{ $comentario->created_at->diffForHumans() }}</span>
            <p>{{ $comentario->comentario }}</p>

            @if(!empty($comentario->image))
            <img src="{{ asset('storage/' . $comentario->image->caminho_imagem) }}" alt="Imagem comentário">
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection