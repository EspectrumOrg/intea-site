<div class="form-postar">
    <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="textfield-postar">

            <div style="display: flex; flex-direction: row; width: 100%;">
                @if (!empty(Auth::user()->foto))
                <img src="{{ url('storage/'.Auth::user()->foto) }}" alt="conta">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
                @endif

                <textarea name="texto_postagem" id="post-textarea" maxlength="255" placeholder="Edite sua publicação" required autofocus></textarea>
            </div>
            <div id="char-count">0/255</div>
        </div>

        <div class="botao-submit-postar">
            <div class="content-postar">
                <label for="caminho_imagem" class="upload-label-postar">
                    <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-postar">
                </label>
            </div>
            <button type="submit" class="botao-postar-postar">Postar</button>
        </div>
    </form>
</div>