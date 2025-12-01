@extends('feed.post.template.layout')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Editar Interesse</h1>
        <p>Atualize as informações da sua comunidade</p>
        <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn-voltar">
            <span class="material-symbols-outlined">arrow_back</span>
            Voltar para o interesse
        </a>
    </div>

    <div class="criar-interesse-container">
        <form action="{{ route('interesses.update', $interesse->slug) }}" method="POST" enctype="multipart/form-data" class="criar-interesse-form" id="interesseForm">
            @csrf
            @method('PUT')

            <!-- Nome do Interesse -->
            <div class="form-group">
                <label for="nome">Nome do Interesse *</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $interesse->nome) }}" 
                       maxlength="50" required class="form-input">
                <small class="form-help">Máximo 50 caracteres. Nome único para sua comunidade.</small>
                @error('nome')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Descrição -->
            <div class="form-group">
                <label for="descricao">Descrição Curta *</label>
                <textarea id="descricao" name="descricao" maxlength="200" required 
                          class="form-textarea" placeholder="Descreva brevemente seu interesse...">{{ old('descricao', $interesse->descricao) }}</textarea>
                <small class="form-help">Máximo 200 caracteres. Aparece nos cards de interesse.</small>
                @error('descricao')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Sobre (Opcional) -->
            <div class="form-group">
                <label for="sobre">Sobre o Interesse</label>
                <textarea id="sobre" name="sobre" maxlength="1000" 
                          class="form-textarea" placeholder="Conte mais sobre este interesse (opcional)...">{{ old('sobre', $interesse->sobre) }}</textarea>
                <small class="form-help">Máximo 1000 caracteres. Aparece na página do interesse.</small>
                @error('sobre')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ícone Padrão OU Ícone Customizado -->
            <div class="form-group">
                <label for="icone">Escolha o Ícone *</label>
                
                <!-- TIPO DE ÍCONE -->
                <div class="icone-option-type">
                    <label>
                        <input type="radio" name="icone_type" value="default" {{ $interesse->icone_custom ? '' : 'checked' }} id="icone_type_default">
                        Usar ícone padrão
                    </label>
                    <label>
                        <input type="radio" name="icone_type" value="custom" {{ $interesse->icone_custom ? 'checked' : '' }} id="icone_type_custom">
                        {{ $interesse->icone_custom ? 'Alterar ícone customizado' : 'Upload de ícone customizado' }}
                    </label>
                </div>

                <div class="icone-container-unificado">
                    <!-- Grid de Ícones Padrão -->
                    <div id="iconesPadraoContainer" class="icone-content" style="{{ $interesse->icone_custom ? 'display: none;' : 'display: block;' }}">
                        <div class="icones-grid">
                            @php
                                $icones = [
                                    'smartphone', 'code', 'science', 'sports_esports', 'sports_soccer',
                                    'music_note', 'movie', 'palette', 'travel_explore', 'restaurant',
                                    'fitness_center', 'school', 'business_center', 'psychology', 'nature',
                                    'pets', 'directions_car', 'flight', 'book', 'star', 'favorite',
                                    'home', 'work', 'shopping_cart', 'local_cafe', 'fitness_center',
                                    'music_note', 'photo_camera', 'videogame_asset', 'computer', 'wifi'
                                ];
                            @endphp
                            @foreach($icones as $icone)
                                <label class="icone-option">
                                    <input type="radio" name="icone" value="{{ $icone }}" 
                                           {{ old('icone', $interesse->icone) == $icone ? 'checked' : '' }}>
                                    <span class="material-symbols-outlined">{{ $icone }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upload de Ícone Customizado -->
                    <div id="iconeCustomContainer" class="icone-content" style="{{ $interesse->icone_custom ? 'display: block;' : 'display: none;' }}">
                        <div class="icone-upload-area">
                            @if($interesse->icone_custom)
                                <div class="current-icon-preview">
                                    <p>Ícone atual:</p>
                                    <img src="{{ $interesse->icone }}" alt="Ícone atual" style="width: 60px; height: 60px; border-radius: 12px;">
                                </div>
                            @endif
                            <input type="file" id="icone_custom" name="icone_custom" 
                                   accept="image/jpeg,image/png,image/svg+xml" class="form-file">
                            <label for="icone_custom" class="upload-label">
                                <span class="material-symbols-outlined">upload</span>
                                <span>Clique para {{ $interesse->icone_custom ? 'alterar' : 'fazer upload' }} do ícone</span>
                                <small>Formatos: PNG, JPG, SVG. Máximo 1MB.</small>
                            </label>
                            <div id="iconePreview" class="icone-preview"></div>
                        </div>
                    </div>
                </div>
                
                @error('icone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                @error('icone_custom')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Cor -->
            <div class="form-group">
                <label for="cor">Cor do Interesse *</label>
                
                <div class="cor-simplificada">
                    <div class="cores-grid">
                        @php
                            $cores = [
                                '#3B82F6' => 'Azul', '#EF4444' => 'Vermelho', '#10B981' => 'Verde',
                                '#F59E0B' => 'Amarelo', '#8B5CF6' => 'Roxo', '#EC4899' => 'Rosa',
                                '#06B6D4' => 'Ciano', '#84CC16' => 'Lima', '#F97316' => 'Laranja',
                                '#6366F1' => 'Índigo', '#64748B' => 'Cinza', '#000000' => 'Preto'
                            ];
                        @endphp
                        @foreach($cores as $cor => $nome)
                            <label class="cor-option" style="background-color: {{ $cor }}; border: {{ $cor == '#FFFFFF' ? '1px solid #d1d5db' : 'none' }};">
                                <input type="radio" name="cor" value="{{ $cor }}" 
                                       {{ old('cor', $interesse->cor) == $cor ? 'checked' : '' }}>
                                <span class="checkmark">✓</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                @error('cor')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Configurações de Moderação -->
            <div class="form-group">
                <label>Configurações de Moderação</label>
                <div class="moderacao-settings">
                    <label class="checkbox-label">
                        <input type="checkbox" name="moderacao_ativa" value="1" 
                               {{ old('moderacao_ativa', $interesse->moderacao_ativa) ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Ativar moderação automática
                    </label>
                    <small class="form-help">Quando ativada, o sistema irá automaticamente moderar conteúdo ofensivo.</small>
                </div>
            </div>

            <!-- Preview -->
            <div class="form-group">
                <label>Preview do Interesse:</label>
                <div class="interesse-preview" id="interessePreview">
                    <div class="preview-card">
                        <div class="preview-header" id="previewHeader" style="border-left-color: {{ $interesse->cor }}; background: {{ $interesse->cor }}20;">
                            <div class="preview-icon" id="previewIcon">
                                @if($interesse->icone_custom)
                                    <img src="{{ $interesse->icone }}" alt="Ícone atual" style="width: 40px; height: 40px; border-radius: 8px;">
                                @else
                                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">{{ $interesse->icone }}</span>
                                @endif
                            </div>
                            <h3 id="previewNome" style="color: {{ $interesse->cor }};">{{ $interesse->nome }}</h3>
                        </div>
                        <p id="previewDescricao">{{ $interesse->descricao }}</p>
                        <div class="preview-stats">
                            <span>{{ $interesse->contador_membros }} seguidores</span>
                            <span>•</span>
                            <span>{{ $interesse->contador_postagens }} postagens</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-criar">Atualizar Interesse</button>
            </div>
        </form>

        <!-- Seção Perigosa -->
        <div class="danger-zone">
            <h3>Zona de Perigo</h3>
            <p>Ações nesta seção são irreversíveis. Proceda com cuidado.</p>
            
            <div class="danger-actions">
                <button type="button" class="btn-danger" onclick="confirmarDelecaoInteresse('{{ $interesse->slug }}')">
                    <span class="material-symbols-outlined">delete</span>
                    Deletar Interesse Permanentemente
                </button>
                <small>Todas as postagens, membros e dados serão permanentemente removidos.</small>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mesmo JavaScript do create.blade.php, mas adaptado para edição
    const nomeInput = document.getElementById('nome');
    const descricaoInput = document.getElementById('descricao');
    const iconeInputs = document.querySelectorAll('input[name="icone"]');
    const iconeTypeInputs = document.querySelectorAll('input[name="icone_type"]');
    const corInputs = document.querySelectorAll('input[name="cor"]');
    
    const iconesPadraoContainer = document.getElementById('iconesPadraoContainer');
    const iconeCustomContainer = document.getElementById('iconeCustomContainer');
    const iconeCustomInput = document.getElementById('icone_custom');
    const iconePreview = document.getElementById('iconePreview');
    
    let currentIconType = '{{ $interesse->icone_custom ? "custom" : "default" }}';
    let customIconUrl = null;

    // Alternar entre ícone padrão e customizado
    iconeTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            currentIconType = this.value;
            
            if (currentIconType === 'default') {
                iconesPadraoContainer.style.display = 'block';
                iconeCustomContainer.style.display = 'none';
                if (!document.querySelector('input[name="icone"]:checked')) {
                    document.querySelector('input[name="icone"]').checked = true;
                }
                iconeCustomInput.value = '';
                customIconUrl = null;
                iconePreview.innerHTML = '';
            } else {
                iconesPadraoContainer.style.display = 'none';
                iconeCustomContainer.style.display = 'block';
                document.querySelectorAll('input[name="icone"]').forEach(input => {
                    input.checked = false;
                });
            }
            
            atualizarPreview();
        });
    });

    // Preview do ícone customizado
    iconeCustomInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('O arquivo é muito grande. Máximo 1MB permitido.');
                this.value = '';
                return;
            }
            
            const validTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
            if (!validTypes.includes(file.type)) {
                alert('Formato de arquivo não suportado. Use PNG, JPG ou SVG.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                customIconUrl = e.target.result;
                iconePreview.innerHTML = `
                    <div class="preview-custom-icon">
                        <img src="${customIconUrl}" alt="Preview do ícone">
                        <button type="button" class="btn-remover-imagem" onclick="removerImagemCustomizada()">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                `;
                atualizarPreview();
            };
            reader.readAsDataURL(file);
        } else {
            customIconUrl = null;
            iconePreview.innerHTML = '';
            atualizarPreview();
        }
    });

    function atualizarPreview() {
        const previewNome = document.getElementById('previewNome');
        const previewDescricao = document.getElementById('previewDescricao');
        const previewIcon = document.getElementById('previewIcon');
        const previewHeader = document.getElementById('previewHeader');
        
        if (previewNome) previewNome.textContent = nomeInput.value || 'Nome do Interesse';
        if (previewDescricao) previewDescricao.textContent = descricaoInput.value || 'Descrição do interesse';
        
        if (previewIcon) {
            previewIcon.innerHTML = '';
            
            if (currentIconType === 'default') {
                const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
                const iconValue = iconeSelecionado ? iconeSelecionado.value : '{{ $interesse->icone }}';
                
                const iconElement = document.createElement('span');
                iconElement.className = 'material-symbols-outlined';
                iconElement.textContent = iconValue;
                iconElement.style.fontSize = '2rem';
                previewIcon.appendChild(iconElement);
            } else {
                if (customIconUrl) {
                    const imgElement = document.createElement('img');
                    imgElement.src = customIconUrl;
                    imgElement.alt = 'Ícone customizado';
                    imgElement.style.width = '100%';
                    imgElement.style.height = '100%';
                    imgElement.style.objectFit = 'contain';
                    imgElement.style.borderRadius = '8px';
                    previewIcon.appendChild(imgElement);
                } else if ('{{ $interesse->icone_custom }}') {
                    const imgElement = document.createElement('img');
                    imgElement.src = '{{ $interesse->icone }}';
                    imgElement.alt = 'Ícone atual';
                    imgElement.style.width = '100%';
                    imgElement.style.height = '100%';
                    imgElement.style.objectFit = 'contain';
                    imgElement.style.borderRadius = '8px';
                    previewIcon.appendChild(imgElement);
                } else {
                    const iconElement = document.createElement('span');
                    iconElement.className = 'material-symbols-outlined';
                    iconElement.textContent = 'image';
                    iconElement.style.fontSize = '2rem';
                    previewIcon.appendChild(iconElement);
                }
            }
        }
        
        if (previewHeader) {
            const corSelecionada = document.querySelector('input[name="cor"]:checked');
            if (corSelecionada && corSelecionada.value) {
                previewHeader.style.backgroundColor = corSelecionada.value + '20';
                previewHeader.style.borderLeftColor = corSelecionada.value;
                previewNome.style.color = corSelecionada.value;
                
                const materialIcon = previewIcon.querySelector('.material-symbols-outlined');
                if (materialIcon) {
                    materialIcon.style.color = corSelecionada.value;
                }
            }
        }
    }

    nomeInput.addEventListener('input', atualizarPreview);
    descricaoInput.addEventListener('input', atualizarPreview);
    iconeInputs.forEach(input => input.addEventListener('change', atualizarPreview));
    corInputs.forEach(input => input.addEventListener('change', atualizarPreview));

    // Inicializar
    atualizarPreview();
});

function confirmarDelecaoInteresse(slug) {
    if (confirm('⚠️ ATENÇÃO: Esta ação é PERMANENTE e IRREVERSÍVEL!\n\nTodos os dados deste interesse serão deletados:\n• Todas as postagens\n• Todos os seguidores\n• Histórico de moderação\n• Configurações\n\nTem certeza que deseja continuar?')) {
        fetch(`/interesses/${slug}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (data && data.success) {
                window.location.href = '/interesses';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao deletar interesse.');
        });
    }
}
</script>

<style>
.btn-voltar {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-voltar:hover {
    background: #e5e7eb;
}

.current-icon-preview {
    text-align: center;
    margin-bottom: 1rem;
}

.current-icon-preview p {
    margin-bottom: 0.5rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.danger-zone {
    margin-top: 3rem;
    padding: 2rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
}

.danger-zone h3 {
    color: #dc2626;
    margin-bottom: 0.5rem;
}

.danger-zone p {
    color: #7f1d1d;
    margin-bottom: 1.5rem;
}

.btn-danger {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

.moderacao-settings {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
}
</style>
@endsection