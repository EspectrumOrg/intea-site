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

            <!-- Ícone -->
            <div class="form-group">
                <label for="icone">Ícone *</label>
                <div class="icones-grid">
                    @php
                        $icones = [
                            'smartphone', 'code', 'science', 'sports_esports', 'sports_soccer',
                            'music_note', 'movie', 'palette', 'travel_explore', 'restaurant',
                            'fitness_center', 'school', 'business_center', 'psychology', 'nature',
                            'pets', 'directions_car', 'flight', 'book', 'star'
                        ];
                    @endphp
                    @foreach($icones as $icone)
                        <label class="icone-option">
                            <input type="radio" name="icone" value="{{ $icone }}" 
                                   {{ old('icone') == $icone ? 'checked' : '' }} required>
                            <span class="material-symbols-outlined">{{ $icone }}</span>
                        </label>
                    @endforeach
                </div>
                @error('icone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Cor -->
            <div class="form-group">
                <label for="cor">Cor do Interesse *</label>
                <div class="cores-grid">
                    @php
                        $cores = [
                            '#3B82F6' => 'Azul',
                            '#EF4444' => 'Vermelho', 
                            '#10B981' => 'Verde',
                            '#F59E0B' => 'Amarelo',
                            '#8B5CF6' => 'Roxo',
                            '#EC4899' => 'Rosa',
                            '#06B6D4' => 'Ciano',
                            '#84CC16' => 'Lima',
                            '#F97316' => 'Laranja',
                            '#6366F1' => 'Índigo'
                        ];
                    @endphp
                    @foreach($cores as $cor => $nome)
                        <label class="cor-option" style="background-color: {{ $cor }};">
                            <input type="radio" name="cor" value="{{ $cor }}" 
                                   {{ old('cor') == $cor ? 'checked' : '' }} required>
                            <span class="checkmark">✓</span>
                        </label>
                    @endforeach
                </div>
                @error('cor')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Banner (Opcional) -->
            <div class="form-group">
                <label for="banner">Banner do Interesse</label>
                <input type="file" id="banner" name="banner" accept="image/*" class="form-file">
                <small class="form-help">Imagem opcional para o cabeçalho do interesse. Máximo 2MB.</small>
                @error('banner')
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
    // Atualizar preview em tempo real
    const nomeInput = document.getElementById('nome');
    const descricaoInput = document.getElementById('descricao');
    const iconeInputs = document.querySelectorAll('input[name="icone"]');
    const corInputs = document.querySelectorAll('input[name="cor"]');

    function atualizarPreview() {
        // Nome
        document.getElementById('previewNome').textContent = 
            nomeInput.value || 'Nome do Interesse';
        
        // Descrição
        document.getElementById('previewDescricao').textContent = 
            descricaoInput.value || 'Descrição do interesse aparecerá aqui';
        
        // Ícone
        const iconeSelecionado = document.querySelector('input[name="icone"]:checked');
        if (iconeSelecionado) {
            document.getElementById('previewIcon').innerHTML = 
                `<span class="material-symbols-outlined">${iconeSelecionado.value}</span>`;
        }
        
        // Cor
        const corSelecionada = document.querySelector('input[name="cor"]:checked');
        if (corSelecionada) {
            document.getElementById('previewIcon').style.color = corSelecionada.value;
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

    // Inicializar preview
    atualizarPreview();
});
</script>
@endsection