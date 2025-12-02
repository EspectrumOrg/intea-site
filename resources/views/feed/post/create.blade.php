<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/post/create/style.css') }}">

<div class="form">
    <img
        src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
        alt="foto de perfil"
        class="foto-perfil-create">

    <form action="{{ route('post.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="textfield">
            <div id="hashtag-preview" class="hashtag-preview"></div>

            <textarea id="texto_postagem"
                name="texto_postagem"
                maxlength="280"
                rows="1"
                placeholder="Comece uma publicação" required></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('texto_postagem')" />

            {{-- Preview da imagem --}}
            <div id="image-preview" class="image-preview" style="display: none;">
                <img id="preview-img" src="" alt="Prévia da imagem">
                <button type="button" id="remove-image" class="remove-image">
                    <span class="material-symbols-outlined">
                        close
                    </span>
                </button>
            </div>

            {{-- Preview do vídeo --}}
            <div id="video-preview" class="video-preview" style="display:none;">
                <video id="preview-video" controls></video>

                <button type="button" id="remove-video" class="remove-video">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <div class="content">
            <div class="extras">
                <!-- imagem/gif -->
                <label for="caminho_imagem" class="upload-label">
                    <span class="material-symbols-outlined">image</span>
                </label>

                <input id="caminho_imagem" name="caminho_imagem" type="file" accept="image/*" class="input-file">

                <x-input-error class="mt-2" :messages="$errors->get('caminho_imagem')" />

                <!-- vídeo -->
                <label for="caminho_video" class="upload-label">
                    <span class="material-symbols-outlined">videocam</span>
                </label>

                <input id="caminho_video" name="caminho_video" type="file" accept="video/mp4,video/webm,video/ogg" class="input-file">

                <x-input-error class="mt-2" :messages="$errors->get('caminho_video')" />
            </div>

            <div class="contador">
                <span id="char-count">0</span>/280
            </div>

            <div class="botao-submit">
                <button type="submit" class="botao-postar">Postar</button>
            </div>
        </div>

    </form>
</div>

<!-- Preview da Imagem -->
<script>
    const inputFile = document.getElementById('caminho_imagem');
    const previewContainer = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-img');
    const removeButton = document.getElementById('remove-image');

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
        inputFile.value = ''; // limpa o input
        previewImage.src = '';
        previewContainer.style.display = 'none';
    });
</script>

<!-- Preview do Vídeo -->
<script>
    const inputVideo = document.getElementById('caminho_video');
    const videoPreviewContainer = document.getElementById('video-preview');
    const previewVideo = document.getElementById('preview-video');
    const removeVideoButton = document.getElementById('remove-video');

    inputVideo.addEventListener('change', () => {
        const file = inputVideo.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = e => {
                previewVideo.src = e.target.result;
                previewVideo.controls = true;
                previewVideo.muted = true; // autoplay seguro
                previewVideo.autoplay = true;

                videoPreviewContainer.style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    });

    removeVideoButton.addEventListener('click', () => {
        inputVideo.value = ''; // limpar input
        previewVideo.src = '';
        videoPreviewContainer.style.display = 'none';
    });
</script>


<!-- JS -->
<script src="{{ url('assets/js/posts/create/hashtag-create.js') }}"></script>
<script src="{{ url('assets/js/posts/create/char-count.js') }}"></script>