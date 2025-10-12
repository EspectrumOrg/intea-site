<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/style.css') }}">

<div class="form">
    @if (!empty(Auth::user()->foto))
    <img src="{{ url('storage/'.Auth::user()->foto) }}"
        alt="conta"
        style="border-radius: 50%;"
        width="40"
        height="40"
        loading="lazy">
    @else
    <img src="{{ url('assets/images/logos/contas/user.png') }}"
        alt="foto perfil"
        style="border-radius: 50%;"
        width="40"
        height="40"
        loading="lazy">
    @endif
    <form action="{{ route('post.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="textfield">
            <textarea id="texto_postagem"
                name="texto_postagem"
                maxlength="280"
                rows="1"
                placeholder="Comece uma publicação"
                required></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />
        </div>

        <div class="content">
            <div class="extras">
                <label for="caminho_imagem" class="upload-label">
                    <img src="{{ url('assets/images/logos/symbols/image.png') }}" class="card-img-top" alt="adicionar imagem">
                </label>
                <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-file">
                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
            </div>

            <div class="contador">
                <span id="char-count">0</span>/280
            </div>

            <div class="botao-submit">
                <button type="submit" class="botao-postar">Publicar</button>
            </div>
        </div>

    </form>
</div>