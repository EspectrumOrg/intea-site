<div class="form-editar">
    <form action="{{ route('post.update', $postagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="textfield-editar">

            <div style="display: flex; flex-direction: row; width: 100%;">
                @if (!empty(Auth::user()->foto))
                <img src="{{ url('storage/'.Auth::user()->foto) }}" alt="conta">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
                @endif

                <textarea name="texto_postagem" id="post-textarea" maxlength="255" placeholder="Edite sua publicação" required autofocus>{{ old('texto_postagem', $postagem->texto_postagem) }}</textarea>
            </div>
            <div id="char-count">0/255</div>
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />
        </div>

        <div class="botao-submit-editar">
            <div class="content-editar">
                <label for="caminho_imagem" class="upload-label-editar">
                    <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-editar">

                </label>
            </div>
            <button type="submit" class="botao-postar-editar">Atualizar</button>
        </div>
    </form>
</div>