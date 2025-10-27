    <div id="modal-comentar-{{ $comentario->id }}" class="modal hidden">
        <div class="modal-content">
            <button type="button" class="close" onclick="fecharModalComentar('{{ $comentario->id }}')">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="modal-content-content">
                <div class="form-comentar resposta">
                    <!-- Exibir comentário que será respondido -->
                    <div class="reply">
                        <div class="reply-content">
                            <img
                                src="{{ asset('storage/'.$comentario->usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                                alt="foto perfil"
                                style="object-fit: cover;"
                                class="user-photo">

                            <div class="dados-reply">
                                <div class="info-user-resposta">
                                    <h1>{{ $comentario->usuario->user }}</h1>
                                    <p>{{ $comentario->usuario->apelido }}</p>
                                    <p>{{ $comentario->created_at->diffForHumans() }}</p>
                                </div>

                                <div class="texto-comentario">
                                    <p class="comentario">{{ $comentario->comentario }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <img
                        src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                        alt="foto de perfil"
                        class="user-photo"
                        style="object-fit: cover;"
                        loading="lazy">

                    <h1> {{ $comentario->postagem->titulo}} </h1>

                    <form action="{{ route('post.comentario', ['tipo' => 'comentario','id' => $comentario->id]) }}" method="POST" class="form" enctype="multipart/form-data">
                        @csrf

                        <div class="textfield">
                            <div id="hashtag-preview-create-resposta-modal-{{$comentario->id}}" class="hashtag-preview"></div>

                            <textarea id="texto_resposta_create_modal_{{ $comentario->id }}"
                                name="comentario"
                                maxlength="280"
                                rows="3"
                                placeholder="Responda a publicação de {{ $comentario->postagem->usuario->user }}" required
                                style="width: 100%;"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('comentario')" />

                            {{-- Preview da imagem --}}
                            <div id="image-preview_create_resposta_modal_{{ $comentario->id }}" class="image-preview" style="display: none;">
                                <img id="preview-img_create_resposta_modal_{{ $comentario->id }}" src="" alt="Prévia da imagem">
                                <button type="button" id="remove-image_create_resposta_modal_{{ $comentario->id }}" class="remove-image">
                                    <span class="material-symbols-outlined">
                                        close
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="content">
                            <div class="extras">
                                <label for="caminho_imagem_modal" class="upload-label">
                                    <span class="material-symbols-outlined">image</span>
                                </label>
                                <input
                                    id="caminho_imagem_modal"
                                    name="caminho_imagem"
                                    type="file"
                                    accept="image/*"
                                    class="input-file">
                                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />
                            </div>

                            <div class="contador">
                                <span class="char-count-comentar">0</span>/280
                            </div>

                            <div class="botao-submit">
                                <button type="submit" class="botao-comentar">Publicar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Seleciona todos os modais de comentário (resposta)
            document.querySelectorAll('[id^="modal-comentar-"]').forEach(modal => {
                const idRespostaModal = modal.id.split('-').pop(); // Pega o ID do comentário 

                // Campos dinâmicos — IDs ajustados para o seu HTML real
                const textareaRespostaModal = document.getElementById(`texto_resposta_create_modal_${idRespostaModal}`);
                const previewHashtagRespostaModal = document.getElementById(`hashtag-preview-create-resposta-modal-${idRespostaModal}`);
                const inputFileRespostaModal = document.getElementById('caminho_imagem_modal'); // mesmo id para todos
                const previewContainerRespostaModal = document.getElementById(`image-preview_create_resposta_modal_${idRespostaModal}`);
                const previewImageRespostaModal = document.getElementById(`preview-img_create_resposta_modal_${idRespostaModal}`);
                const removeButtonRespostaModal = document.getElementById(`remove-image_create_resposta_modal_${idRespostaModal}`);
                const contadorRespostaModal = modal.querySelector('.char-count-comentar');

                // 1. Preview de hashtags + contador
                if (textareaRespostaModal && previewHashtagRespostaModal) {
                    textareaRespostaModal.addEventListener('input', () => {
                        const texto = textareaRespostaModal.value
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');

                        previewHashtagRespostaModal.innerHTML = texto + '\n';

                        if (contadorRespostaModal) {
                            contadorRespostaModal.textContent = textareaRespostaModal.value.length;
                        }
                    });
                }

                // 2. Preview da imagem
                if (inputFileRespostaModal && previewContainerRespostaModal && previewImageRespostaModal && removeButtonRespostaModal) {
                    inputFileRespostaModal.addEventListener('change', () => {
                        const file = inputFileRespostaModal.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = e => {
                                previewImageRespostaModal.src = e.target.result;
                                previewContainerRespostaModal.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    removeButtonRespostaModal.addEventListener('click', () => {
                        inputFileRespostaModal.value = '';
                        previewImageRespostaModal.src = '';
                        previewContainerRespostaModal.style.display = 'none';
                    });
                }
            });
        });
    </script>