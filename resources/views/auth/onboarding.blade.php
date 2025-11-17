<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Escolha seus interesses</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body class="onboarding-body">
    <div class="onboarding-container">
        <div class="onboarding-header">
            <div class="logo-section">
                <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}" alt="Intea" class="logo">
                <h1>Bem-vindo ao Intea!</h1>
            </div>
            <p class="subtitle">Escolha seus interesses para personalizar seu feed</p>
        </div>

        <form id="onboardingForm" class="interesses-grid">
            @csrf
            <div class="interesses-container">
                @foreach($interesses as $interesse)
                <div class="interesse-card" data-interesse-id="{{ $interesse->id }}">
                    <input type="checkbox" name="interesses[]" value="{{ $interesse->id }}" id="interesse-{{ $interesse->id }}" class="interesse-checkbox">
                    
                    <label for="interesse-{{ $interesse->id }}" class="interesse-label">
                        <div class="interesse-icon" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                            <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                        </div>
                        <h3 class="interesse-nome">{{ $interesse->nome }}</h3>
                        <p class="interesse-descricao">{{ $interesse->descricao }}</p>
                        <div class="interesse-stats">
                            <span class="stat">
                                <span class="material-symbols-outlined">people</span>
                                {{ $interesse->contador_membros }} seguidores
                            </span>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="onboarding-actions">
                <button type="button" id="pularBtn" class="btn-secondary">Pular por agora</button>
                <button type="submit" id="continuarBtn" class="btn-primary" disabled>Continuar</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('onboardingForm');
            const checkboxes = document.querySelectorAll('.interesse-checkbox');
            const continuarBtn = document.getElementById('continuarBtn');
            const pularBtn = document.getElementById('pularBtn');

            // Habilitar botão quando selecionar pelo menos 1 interesse
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selected = document.querySelectorAll('.interesse-checkbox:checked').length;
                    continuarBtn.disabled = selected === 0;
                });
            });

            // Enviar formulário
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                fetch('{{ route("onboarding.salvar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        window.location.href = data.redirecionar;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao salvar interesses');
                });
            });

            // Pular onboarding
            pularBtn.addEventListener('click', function() {
                fetch('{{ route("onboarding.pular") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        window.location.href = data.redirecionar;
                    }
                });
            });
        });
    </script>
</body>
</html>