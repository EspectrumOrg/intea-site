<div class="form-comentar resposta">
    {{-- Exibir comentário que será respondido --}}
    <div class="reply">
        <img src="{{ asset('storage/'.$comentario->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
            alt="foto perfil" class="user-photo">
        <div class="info-reply">
            <div class="dados-reply">
                <h1>{{ $comentario->usuario->nome }}</h1>
                <p>{{ $comentario->usuario->user }}</p>
                <p>{{ $comentario->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <p class="mt-2">{{ $comentario->comentario }}</p>
    </div>

    @if (!empty(Auth::user()->foto))
    <img class="user-photo" src="{{ url('storage/'.Auth::user()->foto) }}" alt="conta">
    @else
    <img class="user-photo" src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
    @endif

    <h1> {{ $comentario->postagem->titulo}} </h1>

    <form action="{{ route('post.comentario', ['tipo' => 'comentario','id' => $comentario->id]) }}" method="POST" class="form" enctype="multipart/form-data">
        @csrf
        <div class="textfield-comentar">
            <textarea class="post-textarea-comentar"
                name="comentario"
                maxlength="280"
                rows="3"
                placeholder="Responda a publicação de {{ $comentario->postagem->usuario->user }}" required
                style="width: 100%;"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('comentario')" />
        </div>

        <div class="content">
            <div class="extras">
                <label for="caminho_imagem_modal" class="upload-label">
                    <img src="{{ url('assets/images/logos/symbols/image.png') }}" class="card-img-top" alt="adicionar imagem">
                </label>
                <input id="caminho_imagem_modal" name="caminho_imagem" type="file" accept="image/*" class="input-file">
                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
            </div>

            <div class="contador">
                <span class="char-count-comentar">0</span>/280
            </div>

            <div class="botao-submit">
                <button type="submit" class="botao-comentar">Publicar</button>
            </div>
        </div>
    </form>
</div>