@extends('feed.post.template.layout')

@section('main')
<div class="container-read">
    <div class="content">
        <div class="topo">
            <a class="voltar" href="{{ route('post.read', ['postagem' => $comentario->id_postagem]) }}">
                <img src="{{ asset('assets/images/logos/symbols/back-button.png') }}">
            </a>
            <div class="text">
                <h1>Postagem</h1>
            </div>
        </div>

        <!--------------------------------- Conteúdo Comentário Postagem -------------------------------------->
        <div class="postagem-foco">
            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                    <img src="{{ asset('storage/'.$comentario->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                        alt="foto perfil" class="rounded-circle" width="50" height="50">
                </a>
                <div>
                    <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                        <h1>{{ $comentario->usuario->user }}</h1>
                    </a>
                    <h2>{{ $comentario->usuario->user }}</h2>
                </div>
            </div>
            <!--<p>{{ $comentario->comentario }}</p>
            @if($comentario->image)
            <img src="{{ asset('storage/'.$comentario->imagens->first()->caminho_imagem) }}" class="img-fluid rounded">
            @endif-->
        </div>

        <div class="image">
            <p class="texto-completo" id="texto-completo-{{ $comentario->id }}">{{ $comentario->comentario }}</p>
            @if ($comentario->image)
            <img src="{{ asset('storage/' . $comentario->image->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
            @endif
            <div class="dados">
                <h3>{{ $comentario->created_at->translatedFormat('g:i A . M j, Y') }}</h3>
            </div>
        </div>

        <!----------------------------- Curtidas e comentários ----------------------------------->
        <div class="interacoes">
            <div class="corpo">
                <div class="comment">
                    <img src="{{ asset('assets/images/logos/symbols/site-claro/coment.png') }}">
                    <h1>{{ $comentario->comentarios_count }}</h1>
                </div>

                <form method="POST" action="{{ route('comentario.curtida', $comentario->id) }}">
                    @csrf
                    <button type="submit" class="button">
                        <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (!! $comentario->curtidas_usuario ? 'like-preenchido.png' : 'like.png')) }}">
                        <h1>{{ $comentario->curtidas_count }}</h1>
                    </button>
                </form>
            </div>
        </div>

        <!-- formulário para responder -->
        @include('feed.post.create-resposta-modal', ['comentario' => $comentario])

        <!-- Respostas -->
        <h5 class="mt-4">Respostas</h5>
        @foreach($comentario->respostas as $resposta)
        <div class="resposta border-start ps-3 mb-3">
            <div class="d-flex align-items-center mb-2">
                <img src="{{ asset('storage/'.$resposta->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                    alt="foto perfil" class="rounded-circle" width="40" height="40">
                <div class="ms-2">
                    <a href="{{ route('conta.index', ['usuario_id' => $resposta->usuario->id]) }}">
                        <strong>{{ $resposta->usuario->user }}</strong>
                    </a>
                    <p class="text-muted mb-0">{{ $resposta->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <p>{{ $resposta->comentario }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection