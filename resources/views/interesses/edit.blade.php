@extends('feed.post.template.layout')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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

            <!-- Cor com opção personalizada (igual ao create) -->
            <div class="form-group">
                <label for="cor">Cor do Interesse *</label>
                
                <!-- TIPO DE COR - ADICIONADO -->
                <div class="cor-option-type">
                    <label>
                        <input type="radio" name="cor_type" value="predefinida" checked id="cor_type_predefinida">
                        Cores predefinidas
                    </label>
                    <label>
                        <input type="radio" name="cor_type" value="personalizada" id="cor_type_personalizada">
                        Cor personalizada
                    </label>
                </div>

                <div class="cor-container-unificado">
                    <!-- Cores Predefinidas -->
                    <div id="coresPredefinidasContainer" class="cor-content">
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
                                <label class="cor-option" style="background-color: {{ $cor }};">
                                    <input type="radio" name="cor_predefinida" value="{{ $cor }}" 
                                           {{ old('cor_predefinida', $interesse->cor) == $cor ? 'checked' : '' }}>
                                    <span class="checkmark">✓</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cor Personalizada -->
                    <div id="corPersonalizadaContainer" class="cor-content" style="display: none;">
                        <div class="cor-personalizada-area">
                            <input type="color" id="cor_personalizada" name="cor_personalizada" 
                                   value="{{ old('cor_personalizada', $interesse->cor) }}" class="color-picker">
                            <div class="cor-preview">
                                <div class="cor-display" id="corDisplay" style="background-color: {{ old('cor_personalizada', $interesse->cor) }};"></div>
                                <span id="corValueText">{{ old('cor_personalizada', $interesse->cor) }}</span>
                            </div>
                            <small id="corSelecionadaText">Clique no seletor acima para escolher uma cor personalizada</small>
                        </div>
                    </div>
                </div>
                
                <!-- Campo oculto para valor final da cor -->
                <input type="hidden" id="cor_final" name="cor" value="{{ old('cor_predefinida', $interesse->cor) }}">
                
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
                        <div class="preview-header" id="previewHeader">
                            <div class="preview-icon" id="previewIcon">
                                @if($interesse->icone_custom)
                                    <img src="{{ $interesse->icone }}" alt="Ícone atual" style="width: 40px; height: 40px; border-radius: 8px;">
                                @else
                                    <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                                @endif
                            </div>
                            <h3 id="previewNome">{{ $interesse->nome }}</h3>
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
    // Elementos do DOM
    const nomeInput = document.getElementById('nome');
    const descricaoInput = document.getElementById('descricao');
    const iconeInputs = document.querySelectorAll('input[name="icone"]');
    const iconeTypeInputs = document.querySelectorAll('input[name="icone_type"]');
    const corTypeInputs = document.querySelectorAll('input[name="cor_type"]');
    const corPredefinidaInputs = document.querySelectorAll('input[name="cor_predefinida"]');
    const corPersonalizadaInput = document.getElementById('cor_personalizada');
    const corFinalInput = document.getElementById('cor_final');
    const corDisplay = document.getElementById('corDisplay');
    const corValueText = document.getElementById('corValueText');
    
    const iconesPadraoContainer = document.getElementById('iconesPadraoContainer');
    const iconeCustomContainer = document.getElementById('iconeCustomContainer');
    const iconeCustomInput = document.getElementById('icone_custom');
    const iconePreview = document.getElementById('iconePreview');
    
    const coresPredefinidasContainer = document.getElementById('coresPredefinidasContainer');
    const corPersonalizadaContainer = document.getElementById('corPersonalizadaContainer');
    
    // Estado
    let currentIconType = '{{ $interesse->icone_custom ? "custom" : "default" }}';
    let currentCorType = 'predefinida';
    let customIconUrl = null;

    // Inicializar cor final com o valor atual
    let corSelecionada = '{{ $interesse->cor }}';
    corFinalInput.value = corSelecionada;

    // Verificar se a cor atual está nas predefinidas
    const coresPredefinidas = [
        '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899',
        '#06B6D4', '#84CC16', '#F97316', '#6366F1', '#64748B', '#000000'
    ];
    
    if (!coresPredefinidas.includes(corSelecionada.toUpperCase())) {
        // Se a cor não está nas predefinidas, selecionar "personalizada"
        document.getElementById('cor_type_personalizada').checked = true;
        currentCorType = 'personalizada';
        coresPredefinidasContainer.style.display = 'none';
        corPersonalizadaContainer.style.display = 'block';
    } else {
        // Se está nas predefinidas, selecionar a cor correspondente
        const corRadio = document.querySelector(`input[name="cor_predefinida"][value="${corSelecionada}"]`);
        if (corRadio) {
            corRadio.checked = true;
        }
    }

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

    // Alternar entre cores predefinidas e personalizada
    corTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            currentCorType = this.value;
            
            if (currentCorType === 'predefinida') {
                coresPredefinidasContainer.style.display = 'block';
                corPersonalizadaContainer.style.display = 'none';
                if (!document.querySelector('input[name="cor_predefinida"]:checked')) {
                    document.querySelector('input[name="cor_predefinida"]').checked = true;
                }
                // Atualiza cor final com a predefinida selecionada
                const corPredefinida = document.querySelector('input[name="cor_predefinida"]:checked');
                if (corPredefinida) {
                    corSelecionada = corPredefinida.value;
                    corFinalInput.value = corSelecionada;
                }
            } else {
                coresPredefinidasContainer.style.display = 'none';
                corPersonalizadaContainer.style.display = 'block';
                // Atualiza cor final com a personalizada
                corSelecionada = corPersonalizadaInput.value;
                corFinalInput.value = corSelecionada;
                
                // Atualiza display da cor
                if (corDisplay) corDisplay.style.backgroundColor = corSelecionada;
                if (corValueText) corValueText.textContent = corSelecionada;
            }
            
            atualizarPreview();
        });
    });

    // Atualizar cor predefinida
    corPredefinidaInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (currentCorType === 'predefinida') {
                corSelecionada = this.value;
                corFinalInput.value = corSelecionada;
                atualizarPreview();
            }
        });
    });

    // Atualizar cor personalizada
    corPersonalizadaInput.addEventListener('input', function() {
        if (currentCorType === 'personalizada') {
            corSelecionada = this.value;
            corFinalInput.value = corSelecionada;
            
            // Atualiza display da cor
            if (corDisplay) corDisplay.style.backgroundColor = corSelecionada;
            if (corValueText) corValueText.textContent = corSelecionada;
            
            atualizarPreview();
        }
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

    // Função para remover imagem customizada
    window.removerImagemCustomizada = function() {
        iconeCustomInput.value = '';
        customIconUrl = null;
        iconePreview.innerHTML = '';
        atualizarPreview();
    };

    function atualizarPreview() {
        const previewNome = document.getElementById('previewNome');
        const previewDescricao = document.getElementById('previewDescricao');
        const previewIcon = document.getElementById('previewIcon');
        const previewHeader = document.getElementById('previewHeader');
        
        if (previewNome) previewNome.textContent = nomeInput.value || '{{ $interesse->nome }}';
        if (previewDescricao) previewDescricao.textContent = descricaoInput.value || '{{ $interesse->descricao }}';
        
        if (previewIcon) {
            previewIcon.innerHTML = '';
            
            if (currentIconType === 'default') {
                const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
                const iconValue = iconeSelecionado ? iconeSelecionado.value : '{{ $interesse->icone }}';
                
                const iconElement = document.createElement('span');
                iconElement.className = 'material-symbols-outlined';
                iconElement.textContent = iconValue;
                iconElement.style.fontSize = '2rem';
                iconElement.style.color = corSelecionada;
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
                    iconElement.style.color = corSelecionada;
                    previewIcon.appendChild(iconElement);
                }
            }
        }
        
        if (previewHeader && previewNome) {
            previewHeader.style.backgroundColor = corSelecionada + '20';
            previewHeader.style.borderLeft = `4px solid ${corSelecionada}`;
            previewNome.style.color = corSelecionada;
            
            const materialIcon = previewIcon.querySelector('.material-symbols-outlined');
            if (materialIcon) {
                materialIcon.style.color = corSelecionada;
            }
        }
    }

    nomeInput.addEventListener('input', atualizarPreview);
    descricaoInput.addEventListener('input', atualizarPreview);
    iconeInputs.forEach(input => input.addEventListener('change', atualizarPreview));

    // Inicializar
    atualizarPreview();
});

function confirmarDelecaoInteresse(slug) {
    if (confirm('⚠️ ATENÇÃO: Esta ação é PERMANENTE e IRREVERSÍVEL!\n\nTodos os dados deste interesse serão deletados permanentemente.\n\nTem certeza que deseja continuar?')) {
        const tokenInput = document.querySelector('input[name="_token"]');
        if (!tokenInput) {
            alert('Erro: Token de segurança não encontrado.');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/interesses/${slug}`;
        form.style.display = 'none';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = tokenInput.value;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        
        form.submit();
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

/* Estilos adicionais para compatibilidade com create */
.icone-option-type,
.cor-option-type {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.icone-option-type label,
.cor-option-type label {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}

.icone-container-unificado,
.cor-container-unificado {
    width: 100%;
}

.icone-content,
.cor-content {
    width: 100%;
}

.icones-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.5rem;
    width: 100%;
}

.icone-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
}

.icone-option:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.icone-option input[type="radio"] {
    display: none;
}

.icone-option .material-symbols-outlined {
    font-size: 2rem;
    color: #6b7280;
}

.icone-option input[type="radio"]:checked + .material-symbols-outlined {
    color: #3b82f6;
}

.icone-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background: white;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: #6b7280;
    width: 100%;
}

.icone-preview {
    margin-top: 15px;
    display: flex;
    justify-content: center;
}

.preview-custom-icon {
    position: relative;
    display: inline-block;
}

.preview-custom-icon img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
}

.btn-remover-imagem {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
}

.btn-remover-imagem:hover {
    background: #dc2626;
}

.cores-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 0.5rem;
    width: 100%;
}

.cor-option {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
}

.cor-option:hover {
    transform: scale(1.1);
}

.cor-option input[type="radio"] {
    display: none;
}

.cor-option input[type="radio"]:checked + .checkmark {
    opacity: 1;
}

.checkmark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 14px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.cor-personalizada-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 30px 20px;
    text-align: center;
    background: white;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.color-picker {
    width: 80px;
    height: 80px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    padding: 0;
}

.color-picker::-webkit-color-swatch {
    border: none;
    border-radius: 10px;
}

.color-picker::-moz-color-swatch {
    border: none;
    border-radius: 10px;
}

.cor-preview {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.cor-display {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.cor-preview span {
    font-family: monospace;
    font-weight: 600;
    color: #374151;
}

.preview-card {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 1.5rem;
    background: #f9fafb;
    max-width: 300px;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.preview-header h3 {
    margin: 0;
    font-size: 1.2rem;
    transition: color 0.3s ease;
}

.preview-stats {
    display: flex;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

@media (max-width: 1024px) {
    .icones-grid {
        grid-template-columns: repeat(5, 1fr);
    }
    .cores-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

@media (max-width: 768px) {
    .icones-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    .cores-grid {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width: 480px) {
    .icones-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .cores-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .icone-option-type,
    .cor-option-type {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
@endsection