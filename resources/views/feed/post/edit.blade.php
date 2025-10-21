<div class="form-editar">
    <img
        src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
        alt="foto de perfil"
        class="user-photo"
        loading="lazy">

    <form action="{{ route('post.update', $postagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="textfield">
            <div id="hashtag-preview-postagem-edit-{{ $postagem->id }}" class="hashtag-preview"></div>

            <textarea 
                id="texto_postagem_edit-{{ $postagem->id }}"
                name="texto_postagem"
                maxlength="280"
                rows="3"
                placeholder="Edite sua publicação" required
                autofocus>{{ old('texto_postagem', $postagem->texto_postagem) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />


            {{-- Preview da imagem --}}
            <div id="image-preview_postagem_edit-{{ $postagem->id }}" class="image-preview" style="display: none;">
                <img id="preview-img_postagem_edit-{{ $postagem->id }}" src="" alt="Prévia da imagem">
                <button type="button" id="remove-image_postagem_edit-{{ $postagem->id }}" class="remove-image">
                    <span class="material-symbols-outlined">
                        close
                    </span>
                </button>
            </div>
        </div>

        <div class="content">
            <div class="extras">
                <label for="caminho_imagem" class="upload-label">
                    <span class="material-symbols-outlined">image</span>
                </label>
                <input
                    id="caminho_imagem_postagem_edit-{{ $postagem->id }}"
                    name="caminho_imagem"
                    type="file"
                    accept="image/*"
                    class="input-editar">
                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
            </div>

            <div class="contador">
                <span class="char-count">0</span>/280
            </div>

            <div class="botao-submit">
                <button type="submit" class="botao-postar-editar">Atualizar</button>
            </div>
        </div>
    </form>
</div>

<!-- Preview da Imagem 
<script>
    const inputFilePostagemEdit = document.getElementById('caminho_imagem_postagem_edit');
    const previewContainerPostagemEdit = document.getElementById('image-preview_postagem_edit');
    const previewImagePostagemEdit = document.getElementById('preview-img_postagem_edit');
    const removeButtonPostagemEdit = document.getElementById('remove-image_postagem_edit');

    inputFilePostagemEdit.addEventListener('change', () => {
        const file = inputFilePostagemEdit.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImagePostagemEdit.src = e.target.result;
                previewContainerPostagemEdit.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeButtonPostagemEdit.addEventListener('click', () => {
        inputFilePostagemEdit.value = ''; // limpa o input
        previewImagePostagemEdit.src = '';
        previewContainerPostagemEdit.style.display = 'none';
    });
</script>

JS 
<script src="{{ url('assets/js/posts/update/hashtag-postagem-edit.js') }}"></script>-->