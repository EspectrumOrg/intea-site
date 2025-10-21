<link rel="stylesheet" href="{{ asset('assets/css/layout/avisos.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>

@if (session('success') || session('error') || session('warning'))
<div class="modal-aviso-padrao
        @if(session('success')) sucesso
        @elseif(session('error')) erro
        @elseif(session('warning')) aviso
        @endif">
    <div class="modal-aviso-padrao-content">
        <div class="icone">
            @if (session('success'))
            <span class="material-symbols-outlined">check_circle</span>
            @elseif (session('error'))
            <span class="material-symbols-outlined">error</span> 
            @elseif (session('warning'))
            <span class="material-symbols-outlined">warning</span>     
            @endif
        </div>

        <div class="texto">
            @if (session('success'))
            <h1>Sucesso!</h1>
            <p>{{ session('success') }}</p>
            @elseif (session('error'))
            <h1>Erro!</h1>
            <p>{{ session('error') }}</p>
            @elseif (session('warning'))
            <h1>Aviso!</h1>
            <p>{{ session('warning') }}</p>
            @endif
        </div>

        <div class="botao-modal-padrao">
            <button class="closeModalPadrao">Fechar</button>
        </div>
    </div>
</div>
@endif

<script src="{{ asset('assets/js/avisos/success.js') }}"></script>