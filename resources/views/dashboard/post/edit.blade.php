<div class="form-editar">
    <form action="{{ route('post.update', $postagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="textfield-editar">
            @if (!empty(Auth::user()->foto))
            <img src="{{ url('storage/'.Auth::user()->foto) }}" alt="conta">
            @else
            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
            @endif

            <input name="texto_postagem" type="text"
                value="{{ old('texto_postagem', $postagem->texto_postagem) }}"
                placeholder="Edite sua publicação" required autofocus>
        </div>

        <div class="content-editar">
            <label for="caminho_imagem" class="upload-label-editar">
                📷 Alterar Foto
            </label>
            <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-ditar">
        </div>

        <div class="botao-submit-editar">
            <button type="submit" class="botao-postar-editar">Atualizar</button>
        </div>
    </form>
</div>