<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}">

<div id="modal-editar-postagem-{{ $postagem->id }}" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="close" onclick="fecharModalEditarPostagem('{{$postagem->id}}')">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="modal-content-content">

            <div class="form-postar">
                <img
                    src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="user-photo"
                    loading="lazy">

                <form action="{{ route('post.update', $postagem->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="textfield">
                        <div id="hashtag-preview-postagem-edit-{{ $postagem->id }}" class="hashtag-preview"></div>

                        <textarea
                            id="texto_postagem_edit-{{$postagem->id}}"
                            class="textarea-postagem-edit"
                            data-id="{{ $postagem->id}}"
                            name="texto_postagem"
                            maxlength="280"
                            rows="3"
                            placeholder="Edite sua publicação" required
                            autofocus>{{ old('texto_postagem', $postagem->texto_postagem) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />

                        {{-- Preview da imagem --}}
                        @php
                        $imagem = $postagem->imagens->first();
                        @endphp

                        <input type="hidden" name="remover_imagem" id="remover_imagem_{{$postagem->id}}" value="0">

                        <div id="image-preview_postagem_edit-{{ $postagem->id }}"
                            class="image-preview"
                            @if ($imagem) style="display: block;" @else style="display: none;" @endif>

                            <img id="preview-img_postagem_edit-{{ $postagem->id }}"
                                src="{{ $imagem ? asset('storage/' . $imagem->caminho_imagem) : '' }}"
                                alt="Prévia da imagem">

                            <button type="button"
                                id="remove-image_postagem_edit-{{$postagem->id}}"
                                class="remove-image">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="content">
                        <div class="extras">
                            <label for="caminho_imagem_edit_modal_{{ $postagem->id }}" class="upload-label">
                                <span class="material-symbols-outlined">image</span>
                            </label>
                            <input
                                id="caminho_imagem_edit_modal_{{ $postagem->id }}"
                                name="caminho_imagem"
                                type="file"
                                accept="image/*"
                                class="input-file">
                        </div>

                        <div class="contador">
                            <span class="char-count-postagem-edit" data-id="{{ $postagem->id}}"></span>
                        </div>

                        <div class="botao-submit">
                            <button type="submit" class="botao-postar">Atualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.form-editar').forEach(form => {
            const id = form.querySelector('form').action.split('/').pop(); // pega o ID do post

            // Seleciona elementos com base no ID único
            const textarea = document.getElementById(`texto_postagem_edit_${id}`);
            const previewHashtag = document.getElementById(`hashtag-preview-postagem-edit-${id}`);
            const inputFile = document.getElementById(`caminho_imagem_postagem_edit_${id}`);
            const previewContainer = document.getElementById(`image-preview_postagem_edit_${id}`);
            const previewImage = document.getElementById(`preview-img_postagem_edit_${id}`);
            const removeButton = document.getElementById(`remove-image_postagem_edit_${id}`);

            // Hashtags coloridas
            if (textarea && previewHashtag) {
                textarea.addEventListener('input', () => {
                    const text = textarea.value
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
                    previewHashtag.innerHTML = text + '\n';
                });
            }

            // Preview de imagem
            if (inputFile && previewContainer && previewImage && removeButton) {
                inputFile.addEventListener('change', () => {
                    const file = inputFile.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            previewImage.src = e.target.result;
                            previewContainer.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                removeButton.addEventListener('click', () => {
                    inputFile.value = '';
                    previewImage.src = '';
                    previewContainer.style.display = 'none';
                });
            }
        });
    });
</script>
<!-- Preview da Imagem ------------------------------------------------>
<script>
(function() {
    const postId = "{{ $postagem->id }}";
    const inputFile = document.getElementById(`caminho_imagem_edit_modal_${postId}`);
    const previewContainer = document.getElementById(`image-preview_postagem_edit-${postId}`);
    const previewImage = document.getElementById(`preview-img_postagem_edit-${postId}`);
    const removeButton = document.getElementById(`remove-image_postagem_edit-${postId}`);
    const removerInput = document.getElementById(`remover_imagem_${postId}`);

    // Trocar imagem
    inputFile.addEventListener('change', () => {
        const file = inputFile.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
                removerInput.value = 0; // não remover, imagem nova
            };
            reader.readAsDataURL(file);
        }
    });

    // Remover imagem
    removeButton.addEventListener('click', () => {
        inputFile.value = '';
        previewImage.src = '';
        previewContainer.style.display = 'none';
        removerInput.value = 1; // sinaliza para o controller apagar
    });
})();
</script>


<!-- JS -->
<script src="{{ url('assets/js/posts/update/modal-update.js') }}"></script>
<script src="{{ url('assets/js/posts/update/hashtag-postagem-edit.js') }}"></script>