<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}">

<div id="modal-postar" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="close" onclick="fecharModalPostar()">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="modal-content-content">

            <div class="form-postar">
                <img
                    src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="user-photo"
                    loading="lazy">

                <form action="{{ route('post.store') }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="textfield">
                        <div id="hashtag-preview-create-modal" class="hashtag-preview"></div>

                        <textarea id="texto_postagem_create_modal"
                            name="texto_postagem"
                            class="post-textarea-create-modal"
                            maxlength="280"
                            rows="3"
                            placeholder="Comece uma publicação" required></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />

                        {{-- Preview da imagem --}}
                        <div id="image-preview_create_modal" class="image-preview" style="display: none;">
                            <img id="preview-img_create_modal" src="" alt="Prévia da imagem">
                            <button type="button" id="remove-image_create_modal" class="remove-image">
                                <span class="material-symbols-outlined">
                                    close
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="content">
                        <div class="extras">
                            <label for="caminho_imagem_create_modal" class="upload-label">
                                <span class="material-symbols-outlined">image</span>
                            </label>
                            <input
                                id="caminho_imagem_create_modal"
                                name="caminho_imagem"
                                type="file"
                                accept="image/*"
                                class="input-file">
                            <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
                        </div>

                        <div class="contador">
                            <span class="char-count" id="char-count-create-modal">0</span>/280
                        </div>

                        <div class="botao-submit">
                            <button type="submit" class="botao-postar">Postar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview da Imagem -->
<script>
    const inputFileCreateModal = document.getElementById('caminho_imagem_create_modal');
    const previewContainerCreateModal = document.getElementById('image-preview_create_modal');
    const previewImageCreateModal = document.getElementById('preview-img_create_modal');
    const removeButtonCreateModal = document.getElementById('remove-image_create_modal');

    inputFileCreateModal.addEventListener('change', () => {
        const file = inputFileCreateModal.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImageCreateModal.src = e.target.result;
                previewContainerCreateModal.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeButtonCreateModal.addEventListener('click', () => {
        inputFileCreateModal.value = ''; // limpa o input
        previewImageCreateModal.src = '';
        previewContainerCreateModal.style.display = 'none';
    });
</script>

<!-- JS -->
<script src="{{ url('assets/js/posts/create/modal.js') }}"></script>
<script src="{{ url('assets/js/posts/create/hashtag-create-modal.js') }}"></script>
<script src="{{ url('assets/js/posts/create/char-count-modal.js') }}"></script>