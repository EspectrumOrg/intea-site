<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}">


<div id="modal-editar-comentario-{{ $comentario->id }}" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="close" onclick="fecharModalEditarComentario('{{$comentario->id}}')">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="modal-content-content">

            <div class="form-postar">
                <img
                    src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="user-photo"
                    loading="lazy">

                <form action="{{ route('comentario.update', $comentario->id) }}" method="POST" class="form form-editar" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="textfield">
                        <div id="hashtag-preview-comentario-edit-{{ $comentario->id }}" class="hashtag-preview"></div>

                        <textarea
                            id="texto_comentario_edit_{{ $comentario->id }}"
                            class="textarea-comentario-edit"
                            data-id="{{ $comentario->id}}"
                            name="comentario"
                            maxlength="280"
                            rows="3"
                            placeholder="Edite seu comentário" required
                            autofocus>{{ old('comentario', $comentario->comentario) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('comentario')" />

                        {{-- Preview da imagem --}}
                        @php
                        $imagem = $comentario->image;
                        @endphp

                        <input type="hidden" name="remover_imagem" id="remover_imagem_comentario{{ $comentario->id }}" value="0">

                        <div id="image_preview_comentario_edit_{{ $comentario->id }}"
                            class="image-preview"
                            @if ($imagem) style="display: block;" @else style="display: none;" @endif>

                            <img id="preview_img_comentario_edit_{{ $comentario->id }}"
                                src="{{ $imagem ? asset('storage/' . $imagem->caminho_imagem) : '' }}"
                                alt="Prévia da imagem">

                            <button type="button"
                                id="remove-image-comentario-edit-{{ $comentario->id }}"
                                class="remove-image">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="content">
                        <div class="extras">
                            <label for="caminho_imagem_edit_comentario_modal_{{ $comentario->id }}" class="upload-label">
                                <span class="material-symbols-outlined">image</span>
                            </label>
                            <input
                                id="caminho_imagem_edit_comentario_modal_{{ $comentario->id }}"
                                name="caminho_imagem"
                                type="file"
                                accept="image/*"
                                class="input-file">
                        </div>

                        <div class="contador">
                            <span class="char-count-comentario-edit" data-id="{{ $comentario->id}}"></span>
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
            const id = form.action.split('/').pop(); // pega o ID do post

            // Seleciona elementos com base no ID único
            const textareaComentarioEdit = document.getElementById(`texto_comentario_edit_${id}`);
            const previewHashtagComentarioEdit = document.getElementById(`hashtag-preview-comentario-edit-${id}`);
            const inputFileComentarioEdit = document.getElementById(`caminho_imagem_edit_comentario_modal_${id}`);
            const previewContainerComentarioEdit = document.getElementById(`image_preview_comentario_edit_${id}`);
            const previewImageComentarioEdit = document.getElementById(`preview_img_comentario_edit_${id}`);
            const removeButtonComentarioEdit = document.getElementById(`remove-image-comentario-edit-${id}`);

            // Hashtags coloridas
            if (textareaComentarioEdit && previewHashtagComentarioEdit) {
                textareaComentarioEdit.addEventListener('input', () => {
                    const text = textareaComentarioEdit.value
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/#(\w+)/g, '<span class="hashtag-preto">#$1</span>');
                    previewHashtagComentarioEdit.innerHTML = text + '\n';
                });
            }

            // Preview de imagem
            if (inputFileComentarioEdit && previewContainerComentarioEdit && previewImageComentarioEdit && removeButtonComentarioEdit) {
                inputFileComentarioEdit.addEventListener('change', () => {
                    const file = inputFileComentarioEdit.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            previewImageComentarioEdit.src = e.target.result;
                            previewContainerComentarioEdit.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                removeButtonComentarioEdit.addEventListener('click', () => {
                    inputFileComentarioEdit.value = '';
                    previewImageComentarioEdit.src = '';
                    previewContainerComentarioEdit.style.display = 'none';
                });
            }
        });
    });
</script>
<!-- Preview da Imagem -->
<script>
(function() {
    const comentarioId = "{{ $comentario->id }}";
    const inputFileComentarioEditModal = document.getElementById(`caminho_imagem_edit_comentario_modal_${comentarioId}`);
    const previewContainerComentarioEditModal = document.getElementById(`image_preview_comentario_edit_${comentarioId}`);
    const previewImageComentarioEditModal = document.getElementById(`preview_img_comentario_edit_${comentarioId}`);
    const removeButtonComentarioEditModal = document.getElementById(`remove-image-comentario-edit-${comentarioId}`);
    const removerInputComentarioEditModal = document.getElementById(`remover_imagem_comentario${comentarioId}`);

    // Trocar imagem
    inputFileComentarioEditModal.addEventListener('change', () => {
        const file = inputFileComentarioEditModal.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImageComentarioEditModal.src = e.target.result;
                previewContainerComentarioEditModal.style.display = 'block';
                removerInputComentarioEditModal.value = 0; // não remover, imagem nova
            };
            reader.readAsDataURL(file);
        }
    });

    // Remover imagem
    removeButtonComentarioEditModal.addEventListener('click', () => {
        inputFileComentarioEditModal.value = '';
        previewImageComentarioEditModal.src = '';
        previewContainerComentarioEditModal.style.display = 'none';
        removerInputComentarioEditModal.value = 1; // sinaliza para o controller apagar
    });
})();
</script>

<!-- JS -->
<script src="{{ url('assets/js/posts/comentario/modal-update-comentario.js') }}"></script>