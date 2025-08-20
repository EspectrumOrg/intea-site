<div class="form">
    <form action="{{ route('post.create')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="textfield">
            <img src="{{ url('storage/'.Auth::user()->foto) }}" alt="conta">
            <input name="texto_postagem" type="text" placeholder="Comece uma publicação" value="{{ old('texto_postagem') }}" required autofocus autocomplete="nome">
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />
        </div>

        <div class="content">
            <div class="extras">
                <label for="caminho_imagem" class="upload-label">
                    <span>📷 Foto</span>
                </label>
                <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-file">
                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
            </div>
        </div>

        <div class="botao-submit">
            <button type="submit" class="botao-postar">Publicar</button>
        </div>
    </form>
</div>