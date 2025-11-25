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
        <form action="{{ route('interesses.store') }}" method="POST" enctype="multipart/form-data" class="criar-interesse-form">
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
                
                <div class="icone-option-type">
                    <label>
                        <input type="radio" name="icone_type" value="default" checked>
                        Usar ícone padrão
                    </label>
                    <label>
                        <input type="radio" name="icone_type" value="custom">
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
                                           {{ old('icone') == $icone ? 'checked' : '' }}>
                                    <span class="material-symbols-outlined">{{ $icone }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upload de Ícone Customizado -->
                    <div id="iconeCustomContainer" class="icone-content" style="display: none;">
                        <div class="icone-upload-area">
                            <input type="file" id="icone_custom" name="icone_custom" 
                                   accept="image/*" class="form-file">
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

            <!-- Cor com mais opções + cor personalizada -->
            <div class="form-group">
                <label for="cor">Cor do Interesse *</label>
                
                <div class="cor-option-type">
                    <label>
                        <input type="radio" name="cor_type" value="predefinida" checked>
                        Cores predefinidas
                    </label>
                    <label>
                        <input type="radio" name="cor_type" value="personalizada">
                        Cor personalizada
                    </label>
                </div>

                <div class="cor-container-unificado">
                    <!-- Cores Predefinidas -->
                    <div id="coresPredefinidasContainer" class="cor-content">
                        <div class="cores-grid">
                            @php
                                $cores = [
                                    '#3B82F6' => 'Azul', '#2563EB' => 'Azul Escuro', '#60A5FA' => 'Azul Claro',
                                    '#EF4444' => 'Vermelho', '#DC2626' => 'Vermelho Escuro', '#F87171' => 'Vermelho Claro',
                                    '#10B981' => 'Verde', '#059669' => 'Verde Escuro', '#34D399' => 'Verde Claro',
                                    '#F59E0B' => 'Amarelo', '#D97706' => 'Amarelo Escuro', '#FBBF24' => 'Amarelo Claro',
                                    '#8B5CF6' => 'Roxo', '#7C3AED' => 'Roxo Escuro', '#A78BFA' => 'Roxo Claro',
                                    '#EC4899' => 'Rosa', '#DB2777' => 'Rosa Escuro', '#F472B6' => 'Rosa Claro',
                                    '#06B6D4' => 'Ciano', '#0891B2' => 'Ciano Escuro', '#22D3EE' => 'Ciano Claro',
                                    '#84CC16' => 'Lima', '#65A30D' => 'Lima Escuro', '#A3E635' => 'Lima Claro',
                                    '#F97316' => 'Laranja', '#EA580C' => 'Laranja Escuro', '#FB923C' => 'Laranja Claro',
                                    '#6366F1' => 'Índigo', '#4F46E5' => 'Índigo Escuro', '#818CF8' => 'Índigo Claro',
                                    '#64748B' => 'Cinza', '#475569' => 'Cinza Escuro', '#94A3B8' => 'Cinza Claro',
                                    '#000000' => 'Preto', '#FFFFFF' => 'Branco', '#A855F7' => 'Púrpura',
                                    '#EAB308' => 'Amarelo Ouro', '#14B8A6' => 'Verde Água', '#F43F5E' => 'Rosa Vibrante'
                                ];
                            @endphp
                            @foreach($cores as $cor => $nome)
                                <label class="cor-option" style="background-color: {{ $cor }}; border: {{ $cor == '#FFFFFF' ? '1px solid #d1d5db' : 'none' }};">
                                    <input type="radio" name="cor" value="{{ $cor }}" 
                                           {{ old('cor') == $cor ? 'checked' : '' }}>
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
                            <label for="cor_personalizada" class="color-label">
                                <span class="material-symbols-outlined">palette</span>
                                <span>Clique para escolher uma cor personalizada</span>
                            </label>
                        </div>
                    </div>
                </div>
                
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
    const corInputs = document.querySelectorAll('input[name="cor"]');
    const corPersonalizadaInput = document.getElementById('cor_personalizada');
    
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
            } else {
                iconesPadraoContainer.style.display = 'none';
                iconeCustomContainer.style.display = 'block';
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
                if (!document.querySelector('input[name="cor"]:checked')) {
                    document.querySelector('input[name="cor"]').checked = true;
                }
            } else {
                coresPredefinidasContainer.style.display = 'none';
                corPersonalizadaContainer.style.display = 'block';
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
                iconePreview.innerHTML = `<img src="${customIconUrl}" alt="Ícone customizado">`;
                atualizarPreview();
            };
            reader.readAsDataURL(file);
        }
    });

    // Atualizar cor personalizada no preview
    corPersonalizadaInput.addEventListener('input', atualizarPreview);

    function atualizarPreview() {
        // Nome
        document.getElementById('previewNome').textContent = 
            nomeInput.value || 'Nome do Interesse';
        
        // Descrição
        document.getElementById('previewDescricao').textContent = 
            descricaoInput.value || 'Descrição do interesse aparecerá aqui';
        
        // Ícone
        const previewIcon = document.getElementById('previewIcon');
        
        if (currentIconType === 'default') {
            const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
            if (iconeSelecionado) {
                previewIcon.innerHTML = 
                    `<span class="material-symbols-outlined">${iconeSelecionado.value}</span>`;
            }
        } else {
            if (customIconUrl) {
                previewIcon.innerHTML = `<img src="${customIconUrl}" alt="Ícone customizado">`;
            } else {
                previewIcon.innerHTML = '<span class="material-symbols-outlined">image</span>';
            }
        }
        
        // Cor
        let corSelecionada;
        if (currentCorType === 'predefinida') {
            corSelecionada = document.querySelector('input[name="cor"]:checked');
        } else {
            corSelecionada = { value: corPersonalizadaInput.value };
        }
        
        if (corSelecionada) {
            previewIcon.style.color = corSelecionada.value;
            document.getElementById('previewHeader').style.backgroundColor = corSelecionada.value + '20';
        }
    }

    // Event listeners
    nomeInput.addEventListener('input', atualizarPreview);
    descricaoInput.addEventListener('input', atualizarPreview);
    
    iconeInputs.forEach(input => {
        input.addEventListener('change', atualizarPreview);
    });
    
    corInputs.forEach(input => {
        input.addEventListener('change', atualizarPreview);
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

/* Container unificado para ícones */
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

.icone-option input[type="radio"]:checked + .material-symbols-outlined {
    color: #3b82f6;
}

.icone-option .material-symbols-outlined {
    font-size: 2rem;
    color: #6b7280;
}

/* Área de upload unificada */
.icone-upload-area,
.cor-personalizada-area {
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
}

.upload-label,
.color-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: #6b7280;
    width: 100%;
}

.icone-preview img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 12px;
}

.preview-icon img {
    width: 40px;
    height: 40px;
    object-fit: contain;
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
.color-picker {
    width: 80px;
    height: 80px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.color-picker::-webkit-color-swatch {
    border: none;
    border-radius: 6px;
}

.color-picker::-moz-color-swatch {
    border: none;
    border-radius: 6px;
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