<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}">

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

<!-- Conteúdo principal com scroll -->
<div class="container-post">
    <div class="create-post">
        @include("feed.post.create")
    </div>

    <div class="content-post">
        @foreach($postagens as $postagem)
        <div class="corpo-post">
            <a href="{{ route('post.read', ['postagem' => $postagem->id]) }}" class="post-overlay"></a>

            <div class="foto-perfil">
                <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                    <img
                        src="{{ $postagem->usuario->foto ? url('storage/' . $postagem->usuario->foto) : asset('assets/images/logos/contas/user.png') }}"
                        alt="foto de perfil"
                        style="border-radius: 50%;"
                        width="40"
                        height="40"
                        loading="lazy">
                </a>
            </div>

            <div class="corpo-content">
                <div class="topo"> <!-- info conta -->
                    <div class="info-perfil">
                        <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                            <h1>{{ Str::limit($postagem->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                        </a>
                        <h2>{{ $postagem->usuario->user }} . {{ $postagem->created_at->shortAbsoluteDiffForHumans() }}</h2>
                    </div>

                    <div class="dropdown"> <!-- opções postagem -->
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
                    </div>


                    <!-- bottom ---------------------------------------------------------------------------------->
                    <div class="dados-post">
                        <div>
                            <button type="button" onclick="toggleForm('{{ $postagem->id }}')" class="button btn-comentar">
                                <a href="javascript:void(0)" onclick="abrirModalComentar('{{ $postagem->id }}')">
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
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Criação de postagem -->
<div id="modal-postar" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="close" onclick="fecharModalPostar()">&times;</button>
        <div class="modal-content-content">
            @include('feed.post.create-modal')
        </div>
    </div>
</div>