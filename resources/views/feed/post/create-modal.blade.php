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

                    <div class="interesse-selection" id="interesseSelection" style="display: none; margin: 1rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 2px dashed #e5e7eb;">
                        <label for="interesse_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                            <span class="material-symbols-outlined" style="vertical-align: middle;">category</span>
                            <span id="labelInteresse">Postar no interesse:</span>
                        </label>
                        <select name="interesse_id" id="interesse_id" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; background: white;">
                            <option value="">Feed Geral (todos verão)</option>
                            @foreach(Auth::user()->interesses as $interesse)
                                <option value="{{ $interesse->id }}">{{ $interesse->nome }}</option>
                            @endforeach
                        </select>
                        <small id="ajudaInteresse" style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.8rem;">
                            Escolha um interesse específico para esta postagem
                        </small>
                    </div>

                    <button type="button" id="toggleInteresse" class="btn-interesse-toggle" style="background: #FF8C42; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; margin: 0.5rem 0; transition: all 0.3s;">
                        <span class="material-symbols-outlined">category</span>
                        Postar em Interesse Específico
</button>
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