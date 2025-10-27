<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/comentario/modal.css') }}">

<div id="modal-comentar-{{ $postagem->id }}" class="modal-comentar modal hidden">
    <div class="modal-content">
        <button type="button"
            class="close"
            onclick="fecharModalComentar('{{ $postagem->id }}')">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="modal-content-content">

            <div class="form-comentar">
                <img
                    src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                    alt="foto de perfil"
                    class="user-photo"
                    loading="lazy">

                <h1> {{ $postagem->titulo}} </h1>

                <form action="{{ route('post.comentario', ['tipo' => 'postagem', 'id' => $postagem->id]) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="textfield">
                        <div id="hashtag-preview-create-comentario-modal-{{$postagem->id}}" class="hashtag-preview"></div>

                        <textarea id="texto_comentario_create_modal_{{ $postagem->id }}"
                            name="comentario"
                            maxlength="280"
                            rows="3"
                            placeholder="Responda a publicação de {{ $postagem->usuario->user }}" required
                            style="width: 100%;"></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('comentario')" />

                        {{-- Preview da imagem --}}
                        <div id="image-preview_create_comentario_modal_{{ $postagem->id }}" class="image-preview" style="display: none;">
                            <img id="preview-img_create_comentario_modal_{{ $postagem->id }}" src="" alt="Prévia da imagem">
                            <button type="button" id="remove-image_create_comentario_modal_{{ $postagem->id }}" class="remove-image">
                                <span class="material-symbols-outlined">
                                    close
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="content">
                        <div class="extras">
                            <label for="caminho_imagem_comentario_modal_{{ $postagem->id }}" class="upload-label">
                                <span class="material-symbols-outlined">image</span>
                            </label>
                            <input
                                id="caminho_imagem_comentario_modal_{{ $postagem->id }}"
                                name="caminho_imagem"
                                type="file"
                                accept="image/*"
                                class="input-file">
                            <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
                        </div>

                        <div class="contador">
                            <span class="char-count">0</span>/280
                        </div>

                        <div class="botao-submit">
                            <button type="submit" class="botao-comentar">Comentar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Para cada postagem, adiciona listeners únicos
    document.querySelectorAll('[id^="modal-comentar-"]').forEach(modal => {
        const idCreateComentarioModal = modal.id.split('-').pop(); // pega o ID do post

        // Campos dinâmicos
        const textareaCreateComentarioModal = document.getElementById(`texto_comentario_create_modal_${idCreateComentarioModal}`);
        const previewHashtagCreateComentarioModal = document.getElementById(`hashtag-preview-create-comentario-modal-${idCreateComentarioModal}`);
        const inputFileCreateComentarioModal = document.getElementById(`caminho_imagem_comentario_modal_${idCreateComentarioModal}`);
        const previewContainerCreateComentarioModal = document.getElementById(`image-preview_create_comentario_modal_${idCreateComentarioModal}`);
        const previewImageCreateComentarioModal = document.getElementById(`preview-img_create_comentario_modal_${idCreateComentarioModal}`);
        const removeButtonCreateComentarioModal = document.getElementById(`remove-image_create_comentario_modal_${idCreateComentarioModal}`);
        const contadorCreateComentarioModal = modal.querySelector('.char-count');

        // 1. Preview de hashtags e contador
        if (textareaCreateComentarioModal && previewHashtagCreateComentarioModal) {
            textareaCreateComentarioModal.addEventListener('input', () => {
                const textCreateComentarioModal = textareaCreateComentarioModal.value
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');

                previewHashtagCreateComentarioModal.innerHTML = textCreateComentarioModal + '\n';

                // contador de caracteres
                if (contadorCreateComentarioModal)
                    contadorCreateComentarioModal.textContent = textareaCreateComentarioModal.value.length;
            });
        }

        // 2. Preview de imagem
        if (inputFileCreateComentarioModal && previewContainerCreateComentarioModal && previewImageCreateComentarioModal && removeButtonCreateComentarioModal) {
            inputFileCreateComentarioModal.addEventListener('change', () => {
                const file = inputFileCreateComentarioModal.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewImageCreateComentarioModal.src = e.target.result;
                        previewContainerCreateComentarioModal.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeButtonCreateComentarioModal.addEventListener('click', () => {
                inputFileCreateComentarioModal.value = '';
                previewImageCreateComentarioModal.src = '';
                previewContainerCreateComentarioModal.style.display = 'none';
            });
        }
    });
});
</script>

<!-- comentario -->
<script src="{{ url('assets/js/posts/comentario/modal.js') }}"></script>