<div class="form">
    <form action="{{ route('post.create')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="textfield">
            <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="conta">
            <input name="texto_postagem" type="text" placeholder="Comece uma publicação" value="{{ old('texto_postagem') }}" required autofocus autocomplete="nome">
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />
        </div>

        <div class="content">

            <div class="extras">
                <h3>Foto</h3>
                <input name="caminho_imagem" type="file"  accept="image/*" placeholder="Insira uma imagem">
                <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />
            </div>
        </div>

        <button type="submit" class="botao-postar">Publicar</button>
    </form>
</div>