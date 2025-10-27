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


        <!-- Modal de denúncia (um para cada postagem) -->
        <div id="modal-denuncia-postagem-{{ $postagem->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <span class="close"
                    onclick="fecharModalDenuncia('{{$postagem->id}}')">
                    <span class="material-symbols-outlined">close</span>
                </span>

                <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                    @csrf
                    <div class="form">
                        <input type="hidden" name="tipo" value="postagem">
                        <input type="hidden" name="id_alvo" value="{{ $postagem->id }}">
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
</div>

<!-- APAGAR CASO NÃO NECESSÁRIO
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.form-editar').forEach(form => {
            const id = form.querySelector('form').action.split('/').pop(); // pega o ID do post

            // Seleciona elementos com base no ID único
            const textarea = document.getElementById(`texto_postagem_edit_${id}`);
            const previewHashtag = document.getElementById(`hashtag-preview-postagem-edit-${id}`);
            const inputFile = document.getElementById(`caminho_imagem_postagem_edit_${id}`);
            const previewContainer = document.getElementById(`image-preview_postagem_edit_${id}`);
            const previewImage = document.getElementById(`preview-img_postagem_edit_${id}`);
            const removeButton = document.getElementById(`remove-image_postagem_edit_${id}`);

            // Hashtags coloridas
            if (textarea && previewHashtag) {
                textarea.addEventListener('input', () => {
                    const text = textarea.value
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
                    previewHashtag.innerHTML = text + '\n';
                });
            }

            // Preview de imagem
            if (inputFile && previewContainer && previewImage && removeButton) {
                inputFile.addEventListener('change', () => {
                    const file = inputFile.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            previewImage.src = e.target.result;
                            previewContainer.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                removeButton.addEventListener('click', () => {
                    inputFile.value = '';
                    previewImage.src = '';
                    previewContainer.style.display = 'none';
                });
            }
        });
    });
</script>
-->


<!-- JS -->
<script src="{{ url('assets/js/posts/create/modal.js') }}"></script>