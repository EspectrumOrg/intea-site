@extends('feed.post.template.layout')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Criar Novo Interesse</h1>
        <p>Crie uma comunidade em torno do que você ama</p>
    </div>

    <div class="criar-interesse-container">
        <form action="{{ route('interesses.store') }}" method="POST" enctype="multipart/form-data" class="criar-interesse-form" id="interesseForm">
            @csrf

            <!-- Nome do Interesse -->
            <div class="form-group">
                <label for="nome">Nome do Interesse *</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome') }}" 
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
                          class="form-textarea" placeholder="Descreva brevemente seu interesse...">{{ old('descricao') }}</textarea>
                <small class="form-help">Máximo 200 caracteres. Aparece nos cards de interesse.</small>
                @error('descricao')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Sobre (Opcional) -->
            <div class="form-group">
                <label for="sobre">Sobre o Interesse</label>
                <textarea id="sobre" name="sobre" maxlength="1000" 
                          class="form-textarea" placeholder="Conte mais sobre este interesse (opcional)...">{{ old('sobre') }}</textarea>
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
                        <input type="radio" name="icone_type" value="default" checked id="icone_type_default">
                        Usar ícone padrão
                    </label>
                    <label>
                        <input type="radio" name="icone_type" value="custom" id="icone_type_custom">
                        Upload de ícone customizado
                    </label>
                </div>

                <div class="icone-container-unificado">
                    <!-- Grid de Ícones Padrão -->
                    <div id="iconesPadraoContainer" class="icone-content">
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
                                           {{ old('icone') == $icone ? 'checked' : ($loop->first ? 'checked' : '') }}>
                                    <span class="material-symbols-outlined">{{ $icone }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upload de Ícone Customizado -->
                    <div id="iconeCustomContainer" class="icone-content" style="display: none;">
                        <div class="icone-upload-area">
                            <input type="file" id="icone_custom" name="icone_custom" 
                                   accept="image/jpeg,image/png,image/svg+xml" class="form-file">
                            <label for="icone_custom" class="upload-label">
                                <span class="material-symbols-outlined">upload</span>
                                <span>Clique para fazer upload do ícone</span>
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

            <!-- Cor com opção personalizada -->
            <div class="form-group">
                <label for="cor">Cor do Interesse *</label>
                
                <!-- TIPO DE COR - FIXADO: agora usa a mesma lógica do edit -->
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
                                           {{ old('cor_predefinida') == $cor ? 'checked' : ($loop->first ? 'checked' : '') }}>
                                    <span class="checkmark">✓</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cor Personalizada -->
                    <div id="corPersonalizadaContainer" class="cor-content" style="display: none;">
                        <div class="cor-personalizada-area">
                            <input type="color" id="cor_personalizada" name="cor_personalizada" 
                                   value="{{ old('cor_personalizada', '#3B82F6') }}" class="color-picker">
                            <div class="cor-preview">
                                <div class="cor-display" id="corDisplay" style="background-color: {{ old('cor_personalizada', '#3B82F6') }};"></div>
                                <span id="corValueText">{{ old('cor_personalizada', '#3B82F6') }}</span>
                            </div>
                            <small id="corSelecionadaText">Clique no seletor acima para escolher uma cor personalizada</small>
                        </div>
                    </div>
                </div>
                
                <!-- Campo oculto para valor final da cor -->
                <input type="hidden" id="cor_final" name="cor" value="{{ old('cor_predefinida', '#3B82F6') }}">
                
                @error('cor')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Preview -->
            <div class="form-group">
                <label>Preview do Interesse:</label>
                <div class="interesse-preview" id="interessePreview">
                    <div class="preview-card">
                        <div class="preview-header" id="previewHeader">
                            <div class="preview-icon" id="previewIcon">
                                <span class="material-symbols-outlined">smartphone</span>
                            </div>
                            <h3 id="previewNome">Nome do Interesse</h3>
                        </div>
                        <p id="previewDescricao">Descrição do interesse aparecerá aqui</p>
                        <div class="preview-stats">
                            <span>1 seguidor</span>
                            <span>•</span>
                            <span>0 postagens</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('interesses.index') }}" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-criar">Criar Interesse</button>
            </div>
        </form>
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
    let currentIconType = 'default';
    let currentCorType = 'predefinida';
    let customIconUrl = null;

    // Inicializar cor final
    let corSelecionada = document.querySelector('input[name="cor_predefinida"]:checked').value;
    corFinalInput.value = corSelecionada;

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
            // Validações
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
                
                // Preview na área de upload
                iconePreview.innerHTML = `
                    <div class="preview-custom-icon">
                        <img src="${customIconUrl}" alt="Preview do ícone">
                        <button type="button" class="btn-remover-imagem" onclick="removerImagemCustomizada()">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                `;
                
                // Atualiza o preview principal
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
        // Nome
        const previewNome = document.getElementById('previewNome');
        if (previewNome) {
            previewNome.textContent = nomeInput.value || 'Nome do Interesse';
        }
        
        // Descrição
        const previewDescricao = document.getElementById('previewDescricao');
        if (previewDescricao) {
            previewDescricao.textContent = descricaoInput.value || 'Descrição do interesse aparecerá aqui';
        }
        
        // Ícone
        const previewIcon = document.getElementById('previewIcon');
        if (previewIcon) {
            previewIcon.innerHTML = '';
            
            if (currentIconType === 'default') {
                // Ícone padrão
                const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
                const iconValue = iconeSelecionado ? iconeSelecionado.value : 'smartphone';
                
                const iconElement = document.createElement('span');
                iconElement.className = 'material-symbols-outlined';
                iconElement.textContent = iconValue;
                iconElement.style.fontSize = '2rem';
                iconElement.style.color = corSelecionada;
                previewIcon.appendChild(iconElement);
                
            } else {
                // Ícone customizado
                if (customIconUrl) {
                    const imgElement = document.createElement('img');
                    imgElement.src = customIconUrl;
                    imgElement.alt = 'Ícone customizado';
                    imgElement.style.width = '100%';
                    imgElement.style.height = '100%';
                    imgElement.style.objectFit = 'contain';
                    imgElement.style.borderRadius = '8px';
                    previewIcon.appendChild(imgElement);
                } else {
                    // Placeholder quando não há imagem
                    const iconElement = document.createElement('span');
                    iconElement.className = 'material-symbols-outlined';
                    iconElement.textContent = 'image';
                    iconElement.style.fontSize = '2rem';
                    iconElement.style.color = corSelecionada;
                    previewIcon.appendChild(iconElement);
                }
            }
        }
        
        // Cor
        const previewHeader = document.getElementById('previewHeader');
        if (previewHeader && previewNome) {
            // Aplica cor ao fundo do header
            previewHeader.style.backgroundColor = corSelecionada + '20';
            previewHeader.style.borderLeft = `4px solid ${corSelecionada}`;
            
            // Aplica cor ao título
            previewNome.style.color = corSelecionada;
            
            // Aplica cor apenas aos ícones de material (se existirem)
            const materialIcon = previewIcon.querySelector('.material-symbols-outlined');
            if (materialIcon) {
                materialIcon.style.color = corSelecionada;
            }
        }
    }

    // Event listeners
    nomeInput.addEventListener('input', atualizarPreview);
    descricaoInput.addEventListener('input', atualizarPreview);
    
    iconeInputs.forEach(input => {
        input.addEventListener('change', atualizarPreview);
    });

    // Validação do formulário antes de enviar
    document.getElementById('interesseForm').addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        // Verifica se um ícone foi selecionado
        if (currentIconType === 'default') {
            const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
            if (!iconeSelecionado) {
                isValid = false;
                errorMessage = 'Por favor, selecione um ícone padrão.';
            }
        } else {
            const iconeCustom = document.getElementById('icone_custom').files[0];
            if (!iconeCustom) {
                isValid = false;
                errorMessage = 'Por favor, faça upload de um ícone customizado.';
            }
        }

        // Verifica se uma cor foi selecionada
        if (!corFinalInput.value) {
            isValid = false;
            errorMessage = 'Por favor, selecione uma cor.';
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });

    // Inicializar
    atualizarPreview();
});
</script>

<style>
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

/* Container unificado */
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

.icone-option input[type="radio"]:checked {
    border-color: #3b82f6;
    background: #eff6ff;
}

.icone-option .material-symbols-outlined {
    font-size: 2rem;
    color: #6b7280;
}

.icone-option input[type="radio"]:checked + .material-symbols-outlined {
    color: #3b82f6;
}

/* Área de upload */
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

.preview-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
}

.preview-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
}

.preview-icon .material-symbols-outlined {
    font-size: 2rem;
}

/* Cores */
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

.cor-option input[type="radio"]:checked {
    border-color: #1f2937;
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

/* Cor personalizada */
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

/* Preview do interesse */
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
    background: #3b82f620;
    border-left: 4px solid #3b82f6;
    transition: all 0.3s ease;
}

.preview-header h3 {
    margin: 0;
    color: #1f2937;
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