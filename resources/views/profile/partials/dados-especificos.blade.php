{{-- resources/views/profile/partials/dados-especificos.blade.php --}}
@if($tipoUsuario == 2) {{-- Autista --}}
    <div class="info-item">
        <strong>CIPTEA Autista:</strong>
        <span>{{ $dados->cipteia_autista ?? 'Não informado' }}</span>
    </div>
    <div class="info-item">
        <strong>Status CIPTEA:</strong>
        <span>{{ $dados->status_cipteia_autista ?? 'Não informado' }}</span>
    </div>
    <div class="info-item">
        <strong>RG Autista:</strong>
        <span>{{ $dados->rg_autista ?? 'Não informado' }}</span>
    </div>
@endif

@if($tipoUsuario == 4) {{-- Profissional de Saúde --}}
    <div class="info-item">
        <strong>Tipo de Registro:</strong>
        <span>{{ $dados->tipo_registro ?? 'Não informado' }}</span>
    </div>
    <div class="info-item">
        <strong>Registro Profissional:</strong>
        <span>{{ $dados->registro_profissional ?? 'Não informado' }}</span>
    </div>
@endif