@extends('feed.post.template.layout')

@section('main')
<div class="container-read">
    <div class="content">
        <div class="topo">
            <a class="voltar" href="{{ route('post.read', $comentario->postagem_id) }}">
                <img src="{{ asset('assets/images/logos/symbols/back-button.png') }}">
            </a>
            <h1>Comentário em Foco</h1>
        </div>

        <div class="comentario-foco border p-3 rounded mb-3">
            <div class="d-flex align-items-center mb-2">
                <img src="{{ asset('storage/'.$comentario->usuario->foto ?? 'assets/images/logos/contas/user.png') }}" 
                     alt="foto perfil" class="rounded-circle" width="50" height="50">
                <div class="ms-2">
                    <a href="{{ route('conta.index', ['usuario_id' => $comentario->usuario->id]) }}">
                        <strong>{{ $comentario->usuario->user }}</strong>
                    </a>
                    <p class="text-muted mb-0">{{ $comentario->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <p>{{ $comentario->comentario }}</p>
            @if($comentario->imagens->isNotEmpty())
                <img src="{{ asset('storage/'.$comentario->imagens->first()->caminho_imagem) }}" class="img-fluid rounded">
            @endif
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
