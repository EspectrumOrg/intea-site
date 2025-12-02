@extends('feed.post.template.layout')

@section('main')
@php
use App\Models\Tendencia;

if (!function_exists('formatarHashtags')) {
function formatarHashtags($texto) {
return preg_replace_callback(
'/#(\w+)/u',
function ($matches) {
$tag = e($matches[1]);

// Busca a hashtag no banco
$tendencia = Tendencia::where('hashtag', '#'.$tag)->first();

// Define a URL final (se existir a tendência no banco, vai pra rota certa)
$url = $tendencia
? route('tendencias.show', $tendencia->slug)
: url('/hashtags/' . $tag);

// Retorna o link formatado
return "<a href=\"{$url}\" class=\"hashtag\">#{$tag}</a>";
},
e($texto)
);
}
}
@endphp

<div class="container-post">
    <div class="feed-header principal" style="border-left: 4px solid #3B82F6;">
        <div class="feed-info">
            <span>Feed Principal - Todas as postagens</span>
            @include("feed.post.partials.topo-seguindo")
        </div>
    </div>

    <div class="create-post">
        @include("feed.post.create")
    </div>


    <div class="content-post">
        <!-- verifica se ta no feed ou no seguindo -->
        @if($postagens->isEmpty())
        @if(request()->routeIs('post.index'))
        <div class="empty-feed">
            <span class="material-symbols-outlined">feed</span>
            <h3>Nenhuma postagem no feed</h3>
            <p>Seja o primeiro a postar ou siga mais pessoas!</p>
        </div>
        @elseif(request()->routeIs('post.seguindo'))
        <div class="empty-feed">
            <span class="material-symbols-outlined">group</span>
            <h3>Nenhuma postagem de pessoas que você segue</h3>
            <p>Comece a seguir usuários para ver suas postagens aqui!</p>
        </div>
        @endif
        @else

        <!-- Para cada postagem -->
        @foreach($postagens as $postagem)
        <div class="corpo-post">
            <!-- Badge de Interesses da Postagem -->
            @if($postagem->interesses->count() > 0)
            <div class="post-interesses">
                @foreach($postagem->interesses->take(2) as $interesse)
                <a href="{{ route('post.interesse', $interesse->slug) }}"
                    class="interesse-badge-mini"
                    style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                    <span class="material-symbols-outlined" style="font-size: 14px;">{{ $interesse->icone }}</span>
                    {{ $interesse->nome }}
                </a>
                @endforeach
                @if($postagem->interesses->count() > 2)
                <span class="mais-interesses">+{{ $postagem->interesses->count() - 2 }}</span>
                @endif
            </div>
            @endif

            <a href="{{ route('post.read', ['postagem' => $postagem->id]) }}" class="post-overlay"></a>

            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                    <img
                        src="{{ $postagem->usuario->foto ? url('storage/' . $postagem->usuario->foto) : asset('assets/images/logos/contas/user.png') }}"
                        alt="foto de perfil"
                        style="border-radius: 50%; object-fit:cover;"
                        width="40"
                        height="40"
                        loading="lazy">
                </a>
            </div>

            <div class="corpo-content">
                <div class="topo"> <!-- info conta -->
                    <div class="info-perfil">
                        <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                            <h1>{{ Str::limit($postagem->usuario->apelido ?? 'Desconhecido', 25, '...') }}</h1>
                        </a>
                        <h2>{{ $postagem->usuario->user }} . {{ $postagem->created_at->shortAbsoluteDiffForHumans() }}</h2>
                    </div>

                    <div class="dropdown"> <!-- OPÇÕES POSTAGEM (COISO QUE FICA NOS PONTINHOS PRETOS LÁ) ========-->
                        <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                            <span class="material-symbols-outlined">more_horiz</span>
                        </button>
                        <ul class="dropdown-content">
                            <!-- Postagem do usuário --------------------->
                            @if(Auth::id() === $postagem->usuario_id)
                            <li>
                                <button type="button"
                                    class="btn-acao editar"
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
                            <!-- Postagem de terceiro --------------------->
                            <li>
                                @if( Auth::user()->tipo_usuario === 1 )
                                <!-- Banir Usuário (caso tipo admin)-->
                                <div class="form-excluir">
                                    <button type="button" class="btn-acao btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoUsuarioEspecifico('{{ $postagem->usuario->id }}')">
                                        <span class="material-symbols-outlined">person_off</span>Banir
                                    </button>
                                </div>
                                @else
                                <!-- Denunciar Usuário -->
                                <a class="btn-acao denunciar" href="javascript:void(0)" onclick="abrirModalDenuncia('{{ $postagem->id }}')">
                                    <span class="material-symbols-outlined">flag_2</span>Denunciar
                                </a>
                                @endif
                            </li>
                            <!-- Parte de seguir (do nicolas) -->
                            <li>
                                @php
                                $authUser = Auth::user();
                                $isFollowing = $authUser->seguindo->contains($postagem->usuario_id);
                                $pedidoFeito = \App\Models\Notificacao::where('solicitante_id', $authUser->id)
                                ->where('alvo_id', $postagem->usuario_id)
                                ->where('tipo', 'seguir')
                                ->exists();
                                @endphp

                                @if ($authUser->id !== $postagem->usuario_id)

                                {{-- Se já segue → sempre mostrar Deixar de seguir --}}
                                @if ($isFollowing)
                                <form action="{{ route('seguir.destroy', $postagem->usuario_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-acao deixar-btn">
                                        <span class="material-symbols-outlined">person_remove</span> Deixar de seguir
                                    </button>
                                </form>

                                {{-- Usuário privado (não está seguindo) --}}
                                @elseif ($postagem->usuario->visibilidade == 0)
                                @if ($pedidoFeito)
                                <form action="{{ route('seguir.cancelar', $postagem->usuario_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-acao seguir-btn">
                                        <span class="material-symbols-outlined">hourglass_empty</span> Pedido enviado
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $postagem->usuario_id }}">
                                    <button type="submit" class="btn-acao seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Pedir para seguir
                                    </button>
                                </form>
                                @endif

                                {{-- Usuário público (não está seguindo) --}}
                                @else
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $postagem->usuario_id }}">
                                    <button type="submit" class="btn-acao seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Seguir {{ $postagem->usuario->user }}
                                    </button>
                                </form>
                                @endif

                                @endif
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- conteudo postagem -->
                <div class="conteudo-post">
                    <div class="coment-perfil">
                        <p class="texto-curto" id="texto-{{ $postagem->id }}">
                            {!! formatarHashtags(Str::limit($postagem->texto_postagem, 150, '')) !!}
                            @if (strlen($postagem->texto_postagem) > 150)
                            <span class="mostrar-mais" onclick="toggleTexto('{{ $postagem->id }}', this)">...mais</span>
                            @endif
                        </p>

                        <p class="texto-completo" id="texto-completo-{{ $postagem->id }}" style="display: none;">
                            {!! formatarHashtags($postagem->texto_postagem) !!}
                            <span class="mostrar-mais" onclick="toggleTexto('{{ $postagem->id }}', this)">...menos</span>
                        </p>
                    </div>

                    <div class="image-post">
                        @if ($postagem->imagens->isNotEmpty() && $postagem->imagens->first()->caminho_imagem)
                        <img src="{{ asset('storage/' . $postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                        @endif

                        @if($postagem->video)
                        <video controls class="video-postagem">
                            <source src="{{ asset('storage/' . $postagem->video->caminho_video) }}" type="video/mp4">
                            Seu navegador não suporta vídeo.
                        </video>
                        @endif
                    </div>


                    <!-- curtidas e comentários ---------------------------------------------------------------------------------->
                    <div class="dados-post">
                        <div>
                            <button type="button" onclick="toggleForm('{{ $postagem->id }}')" class="button btn-comentar">
                                <a href="javascript:void(0)" onclick="abrirModalComentar('{{ $postagem->id }}')">
                                    <span class="material-symbols-outlined">chat_bubble</span>
                                    <h1>{{ $postagem->comentarios_count }}</h1>
                                </a>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('curtida.toggle') }}">
                            @csrf
                            <input type="hidden" name="tipo" value="postagem">
                            <input type="hidden" name="id" value="{{ $postagem->id}}">
                            <button type="submit" class="button btn-curtir {{ $postagem->curtidas_usuario ? 'curtido' : 'normal' }}">
                                <span class="material-symbols-outlined">favorite</span>
                                <h1>{{ $postagem->curtidas_count }}</h1>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Edição dessa postagem -->
        @include('feed.post.edit', ['postagem' => $postagem])

        <!-- Modal Criação de comentário ($postagem->id) -->
        @include('feed.post.create-comentario-modal', ['postagem' => $postagem])

        <!-- modal banir-->
        @include('layouts.partials.modal-banimento', ['usuario' => $postagem->usuario])


        <!-- Modal de denúncia (um para cada postagem) -->
        <div id="modal-denuncia-postagem-{{ $postagem->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <h3 class="texto-next-close-button">Coletando informações</h3>
                <span class="close"
                    onclick="fecharModalDenuncia('{{$postagem->id}}')">
                    <span class="material-symbols-outlined">close</span>
                </span>

                <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                    @csrf
                    <input type="hidden" name="tipo" value="postagem">
                    <input type="hidden" name="id_alvo" value="{{ $postagem->id }}">

                    @include('layouts.partials.modal-denuncia')
                </form>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ativar navegação de feed
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-feed').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>

<!-- JS -->
<script src="{{ url('assets/js/posts/create/modal.js') }}"></script>
@endsection