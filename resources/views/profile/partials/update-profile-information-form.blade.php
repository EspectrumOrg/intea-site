<section class="perfil-section">
    <header class="header">

        <!--  <div class="foto-perfil-container">
            <div class="foto-perfil-wrapper">
                @if (!empty($user->foto) && $user->foto != 'assets/images/logos/contas/user.png')
                <img src="{{ asset('storage/'.$user->foto) }}" class="foto-perfil-img" alt="foto perfil">
                @else
                <img src="{{ url('assets/images/logos/contas/user.png') }}" class="foto-perfil-img" alt="foto perfil">
                @endif
            </div>
        </div>

-->

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informações do Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Atualize as informações do seu perfil.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" id="profileForm">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ $user->email ?? old('email') }}" required autocomplete="username" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Seu endereço de email não é verificado.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Clique aqui para enviar o e-mail de verificação.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto Perfil</label>

            <!-- Preview Container -->
            <div class="image-preview-container mb-3">
                <div class="preview-wrapper" onclick="document.getElementById('foto').click()">
                    @if (!empty($user->foto))
                    <img src="{{ asset('storage/'.$user->foto) }}" class="preview-image" alt="Preview da foto" id="imagePreview">
                    @else
                    <img src="{{ url('assets/images/logos/contas/user.png') }}" class="preview-image" alt="Preview da foto" id="imagePreview">
                    @endif
                    <div class="preview-overlay">
                        <span class="preview-text">Clique para alterar</span>
                    </div>
                </div>
            </div>

            <!-- Input File Escondido -->
            <input id="foto" name="foto" type="file" style="display: none;" accept="image/*" onchange="previewImage(this)">

            <!-- Container dos Botões -->
            <div class="photo-buttons-container">
                <button type="button" class="btn-choose-photo" onclick="document.getElementById('foto').click()">
                    <i class="fas fa-camera"></i>
                    <span>Escolher Foto</span>
                </button>

                <!-- Botão sempre visível, controle de exibição via JavaScript -->
                <button type="button" class="btn-remove-photo" id="removePhotoButton" 
                        style="{{ (!$user->foto) ? 'display: none;' : '' }}" onclick="removePhoto()">
                    <i class="fas fa-trash"></i>
                    <span>Remover Foto</span>
                </button>
            </div>

            <div class="form-text">
                Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 2MB
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="4" cols="50">{{ $user->descricao ?? old('descricao') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('descricao')" />
        </div>

        <div class="mb-3">
            <label for="apelido" class="form-label">Apelido</label>
            <input id="apelido" name="apelido" type="text" class="form-control" value="{{ $user->apelido ?? old('apelido')}}" autocomplete="apelido" />
            <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome User</label>
            <input id="user" name="user" type="text" class="form-control" value="{{ $user->user ?? old('user')}}" required autocomplete="user" />
            <x-input-error class="mt-2" :messages="$errors->get('user')" />
        </div>

        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de Conta</label>
            <input id="tipo_usuario" name="tipo_usuario" type="text" class="form-control"
                value="@switch($user->tipo_usuario)
                @case(1) Admin @break
                @case(2) Autista @break
                @case(3) Comunidade @break
                @case(4) Profissional de Saúde @break
                @case(5) Responsável @break
               @endswitch"
                readonly />
        </div>

        {{-- adicionar campos específicos para cada tipo de conta (tirando admin e comunidade)--}}
        @if ($user->tipo_usuario === 2 && $dadosespecificos)
        <div class="mb-3">
            <label for="cipteia_autista" class="form-label"><strong>Autista</strong></label>
            <input id="cipteia_autista" name="cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->cipteia_autista ?? old('cipteia_autista')}}" required autocomplete="cipteia_autista" />
        </div>
        <div class="mb-3">
            <label for="status_cipteia_autista" class="form-label"><strong>Status Cipteia Autista</strong></label>
            <input id="status_cipteia_autista" name="status_cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->status_cipteia_autista ?? old('status_cipteia_autista')}}" required autocomplete="status_cipteia_autista" />
        </div>
       
        @if (isset($dadosespecificos->responsavel_id))
        <div class="mb-3">
            <label for="responsavel" class="form-label"><strong>Responsável</strong></label>
            <h1 value="{{ $dadosespecificos->responsavel_id ?? old('responsavel_id')}}" required autocomplete="responsavel_id"></h1>
        </div>
        @endif
        @endif

        @if ($user->tipo_usuario === 4 && $dadosespecificos)
        <div class="mb-3">
            <label for="tipo_registro" class="form-label"><strong>Tipo Registro</strong></label>
            <input id="tipo_registro" name="tipo_registro" type="text" class="form-control" value="{{ $dadosespecificos->tipo_registro ?? old('tipo_registro')}}" required autocomplete="tipo_registro" />
        </div>
        <div class="mb-3">
            <label for="registro_profissional" class="form-label"><strong>Registro Profissional</strong></label>
            <input id="registro_profissional" name="registro_profissional" type="text" class="form-control" value="{{ $dadosespecificos->registro_profissional ?? old('registro_profissional')}}" required autocomplete="registro_profissional" />
        </div>
        @endif

        @if ($user->tipo_usuario === 4 && $dadosespecificos)
        <div class="mb-3">
            <label for="cipteia_autista" class="form-label"><strong>Cipteia Autista</strong></label>
            <input id="cipteia_autista" name="cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->cipteia_autista ?? old('cipteia_autista')}}" required autocomplete="cipteia_autista" />
        </div>
        @endif

        <div class="mb-3">
            <label for="genero_id" class="form-label">Gênero</label>
            <select type="text" class="form-select" id="genero_id" name="genero_id">
                <option value="">--- Selecione ---</option>
                @foreach($generos as $item)
                <option value="{{ $item->id }}" {{ isset($user) && $item->id === $user->genero ? "selected='selected'": "" }}>{{ $item->titulo }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data Nascimento</label>
            <input id="data_nascimento" name="data_nascimento" type="date" class="form-control" value="{{ $user->data_nascimento ?? old('data_nascimento')}}" required autocomplete="data_nascimento" />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <div class="flex">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
            @if (session('status') === 'profile-updated')
            <p class="text-success">{{ __('Informações atualizadas com sucesso.') }}</p>
            @endif
        </div>
    </form>

    <style>
        .foto-perfil-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .foto-perfil-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        }

        .foto-perfil-wrapper:hover {
            border-color: #3b82f6;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: scale(1.02);
        }

        .foto-perfil-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .foto-perfil-img:hover {
            transform: scale(1.05);
        }

        /* Tamanhos para diferentes dispositivos */
        @media (min-width: 768px) {
            .foto-perfil-wrapper {
                width: 140px;
                height: 140px;
            }
        }

        @media (min-width: 1024px) {
            .foto-perfil-wrapper {
                width: 160px;
                height: 160px;
            }
        }

        @media (min-width: 1280px) {
            .foto-perfil-wrapper {
                width: 180px;
                height: 180px;
            }
        }

        /* Para telas muito pequenas */
        @media (max-width: 480px) {
            .foto-perfil-wrapper {
                width: 100px;
                height: 100px;
                border-width: 3px;
            }
        }

        /* Efeito de loading suave */
        .foto-perfil-img {
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Estado vazio/placeholder */
        .foto-perfil-wrapper:has(.foto-perfil-img[src*="user.png"]) {
            background: linear-gradient(135deg, #f1f5f9, #cbd5e1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .foto-perfil-wrapper:has(.foto-perfil-img[src*="user.png"]) .foto-perfil-img {
            width: 60%;
            height: 60%;
            object-fit: contain;
            opacity: 0.6;
        }

        .image-preview-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .preview-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid #e2e8f0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .preview-wrapper:hover {
            border-color: #3b82f6;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .preview-wrapper:hover .preview-overlay {
            opacity: 1;
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(59, 130, 246, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .preview-text {
            color: white;
            font-size: 0.8rem;
            text-align: center;
            padding: 0.5rem;
            font-weight: 500;
        }

        /* Container dos Botões */
        .photo-buttons-container {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        /* Botão Escolher Foto */
        .btn-choose-photo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .btn-choose-photo:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
        }

        .btn-choose-photo:active {
            transform: translateY(0);
        }

        .btn-choose-photo i {
            font-size: 1rem;
        }

        /* Botão Remover Foto */
        .btn-remove-photo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        .btn-remove-photo:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
        }

        .btn-remove-photo:active {
            transform: translateY(0);
        }

        .btn-remove-photo i {
            font-size: 1rem;
        }

        /* Texto informativo */
        .form-text {
            text-align: center;
            margin-top: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* Animação para quando a imagem é carregada */
        .image-loaded {
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Responsividade */
        @media (max-width: 640px) {
            .photo-buttons-container {
                flex-direction: column;
                align-items: center;
            }

            .btn-choose-photo,
            .btn-remove-photo {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }

            .preview-wrapper {
                width: 120px;
                height: 120px;
            }
        }
    </style>

    <script>
        // Variável para controlar se há uma foto selecionada
        let hasSelectedPhoto = false;
        const userHasOriginalPhoto = {{ !empty($user->foto) ? 'true' : 'false' }};

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            const removeButton = document.getElementById('removePhotoButton');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('image-loaded');
                    
                    // Mostra o botão de remover quando uma nova foto é selecionada
                    hasSelectedPhoto = true;
                    removeButton.style.display = 'inline-flex';

                    // Remove o campo remove_photo se existir (caso o usuário tenha clicado em remover antes)
                    let existingRemoveField = document.getElementById('remove_photo');
                    if (existingRemoveField) {
                        existingRemoveField.remove();
                    }

                    setTimeout(() => {
                        preview.classList.remove('image-loaded');
                    }, 500);
                }

                reader.readAsDataURL(file);
            }
        }

        function removePhoto() {
            const preview = document.getElementById('imagePreview');
            const fileInput = document.getElementById('foto');
            const removeButton = document.getElementById('removePhotoButton');
            
            // Reseta para a imagem padrão
            preview.src = "{{ url('assets/images/logos/contas/user.png') }}";
            fileInput.value = '';

            // Marca que a foto foi removida
            hasSelectedPhoto = false;

            // Adiciona campo hidden para remover a foto no backend
            let existingRemoveField = document.getElementById('remove_photo');
            if (!existingRemoveField) {
                let removeField = document.createElement('input');
                removeField.type = 'hidden';
                removeField.name = 'remove_photo';
                removeField.value = '1';
                removeField.id = 'remove_photo';
                document.getElementById('profileForm').appendChild(removeField);
            }

            // NÃO escondemos o botão aqui - ele só deve ser escondido se não havia foto original
            // O botão permanece visível para permitir que o usuário continue interagindo
            if (!userHasOriginalPhoto) {
                removeButton.style.display = 'none';
            }
        }

        // Inicializa o estado do botão quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            const removeButton = document.getElementById('removePhotoButton');
            // Se não há foto original, esconde o botão inicialmente
            if (!userHasOriginalPhoto) {
                removeButton.style.display = 'none';
            }
        });
    </script>
</section>