@extends('feed.post.template.layout')

@section('main')
<div class="container">
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

    <!-- Postagem em foco -->
    <div class="postagem-foco">
        <div class="usuario">
            <strong>{{ $postagem->usuario->nome }}</strong>
            <span>{{ $postagem->created_at->diffForHumans() }}</span>
        </div>
        <p>{{ $postagem->texto_postagem }}</p>

        <!-- Curtidas e comentários -->
        <div class="interacoes">
            <span>{{ $postagem->curtidas_count }} curtidas</span>
            <span>{{ $postagem->comentarios->count() }} comentários</span>
        </div>
    </div>

    <!-- Form de comentário -->
    <div class="form-comentario">
        <form action="{{ route('post.comentario', $postagem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="comentario" placeholder="Escreva um comentário..." required></textarea>
            <input type="file" name="caminho_imagem">
            <button type="submit">Comentar</button>
        </form>
    </div>

    <!-- Lista de comentários -->
    <div class="comentarios">
        @foreach($postagem->comentarios as $comentario)
        <div class="comentario">
            <strong>{{ $comentario->usuario->nome }}</strong>
            <span>{{ $comentario->created_at->diffForHumans() }}</span>
            <p>{{ $comentario->comentario }}</p>

            @if($comentario->imagens)
            @foreach($comentario->imagens as $imagem)
            <img src="{{ asset('storage/'.$imagem->caminho_imagem) }}" alt="Imagem comentário">
            @endforeach
            @endif
        </div>
        @endforeach
    </div>

</div>
@endsection