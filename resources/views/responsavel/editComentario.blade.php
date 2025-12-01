<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}">

<!-- Modal de Edição de Comentário -->
<div id="modal-editar-comentario-{{ $comentario->id }}" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="close" onclick="fecharModalEditarComentario('{{ $comentario->id }}')">
            <span class="material-symbols-outlined">close</span>
        </button>

        <div class="modal-content-content">
            <div class="form-postar">
                <!-- Foto do usuário -->
                <img
                    src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="user-photo"
                    loading="lazy">

                <!-- Formulário -->
                <form action="{{ route('comentario.update', $comentario->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="textfield">
                        <div id="hashtag-preview-comentario-edit-{{ $comentario->id }}" class="hashtag-preview"></div>

                        <textarea
                            id="texto_comentario_edit-{{ $comentario->id }}"
                            class="textarea-comentario-edit"
                            data-id="{{ $comentario->id }}"
                            name="comentario"
                            maxlength="1000"
                            rows="3"
                            placeholder="Edite seu comentário" required
                            autofocus>{{ old('comentario', $comentario->comentario) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('comentario')" />

                        @php
                            $imagem = $comentario->image;
                        @endphp

                        <input type="hidden" name="remover_imagem" id="remover_imagem_{{ $comentario->id }}" value="0">

                        <div id="image-preview_comentario_edit-{{ $comentario->id }}"
                             class="image-preview"
                             @if ($imagem) style="display: block;" @else style="display: none;" @endif>

                            <img id="preview-img_comentario_edit-{{ $comentario->id }}"
                                 src="{{ $imagem ? asset('storage/' . $imagem->caminho) : '' }}"
                                 alt="Prévia da imagem">

                            <button type="button"
                                    id="remove-image_comentario_edit-{{ $comentario->id }}"
                                    class="remove-image">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="content">
                        <div class="extras">
                            <label for="caminho_imagem_edit_modal_{{ $comentario->id }}" class="upload-label">
                                <span class="material-symbols-outlined">image</span>
                            </label>
                            <input
                                id="caminho_imagem_edit_modal_{{ $comentario->id }}"
                                name="caminho_imagem"
                                type="file"
                                accept="image/*"
                                class="input-file">
                        </div>

                        <div class="contador">
                            <span class="char-count-comentario-edit" data-id="{{ $comentario->id }}"></span>
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

<!-- Script para funcionalidade do modal -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const comentarioId = "{{ $comentario->id }}";

    const textarea = document.getElementById(`texto_comentario_edit-${comentarioId}`);
    const previewHashtag = document.getElementById(`hashtag-preview-comentario-edit-${comentarioId}`);
    const inputFile = document.getElementById(`caminho_imagem_edit_modal_${comentarioId}`);
    const previewContainer = document.getElementById(`image-preview_comentario_edit-${comentarioId}`);
    const previewImage = document.getElementById(`preview-img_comentario_edit-${comentarioId}`);
    const removeButton = document.getElementById(`remove-image_comentario_edit-${comentarioId}`);
    const removerInput = document.getElementById(`remover_imagem_${comentarioId}`);

    // Preview de hashtags coloridas
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
                    removerInput.value = 0; // imagem nova
                };
                reader.readAsDataURL(file);
            }
        });

        removeButton.addEventListener('click', () => {
            inputFile.value = '';
            previewImage.src = '';
            previewContainer.style.display = 'none';
            removerInput.value = 1; // sinaliza para o controller remover
        });
    }
});

// Funções de abrir/fechar modal
function abrirModalEditarComentario(id) {
    const modal = document.getElementById(`modal-editar-comentario-${id}`);
    if (modal) modal.classList.remove('hidden');
}
function fecharModalEditarComentario(id) {
    const modal = document.getElementById(`modal-editar-comentario-${id}`);
    if (modal) modal.classList.add('hidden');
}
</script>
