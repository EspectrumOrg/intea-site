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

        <!-- Loading overlay -->
        <div id="loadingOverlay" class="loading-overlay hidden">
            <div class="loading-spinner">
                <span class="material-symbols-outlined">autorenew</span>
                <p>Processando...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('onboardingForm');
            const checkboxes = document.querySelectorAll('.interesse-checkbox');
            const continuarBtn = document.getElementById('continuarBtn');
            const pularBtn = document.getElementById('pularBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Função para mostrar loading
            function showLoading() {
                loadingOverlay.classList.remove('hidden');
            }

            // Função para esconder loading
            function hideLoading() {
                loadingOverlay.classList.add('hidden');
            }

            // Função para mostrar erro
            function showError(message) {
                alert('Erro: ' + message);
            }

            // Habilitar botão quando selecionar pelo menos 1 interesse
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selected = document.querySelectorAll('.interesse-checkbox:checked').length;
                    continuarBtn.disabled = selected === 0;
                });
            });

            // Enviar formulário
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                showLoading();
                continuarBtn.disabled = true;
                pularBtn.disabled = true;

                try {
                    const formData = new FormData(form);
                    
                    // ✅ CORRIGIDO: Usar a rota correta sem /api/
                    const response = await fetch('{{ route("onboarding.salvar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(formData)
                    });

                    // Verificar se a resposta é JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        // Se não for JSON, tentar ler como texto para debug
                        const text = await response.text();
                        console.error('Resposta não é JSON:', text);
                        throw new Error('Resposta do servidor não é JSON. Status: ' + response.status);
                    }

                    const data = await response.json();

                    if (data.sucesso) {
                        window.location.href = data.redirecionar;
                    } else {
                        showError(data.mensagem || 'Erro desconhecido');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showError('Erro de conexão. Verifique sua internet e tente novamente.');
                } finally {
                    hideLoading();
                    continuarBtn.disabled = false;
                    pularBtn.disabled = false;
                }
            });

            // Pular onboarding
            pularBtn.addEventListener('click', async function() {
                showLoading();
                continuarBtn.disabled = true;
                pularBtn.disabled = true;

                try {
                    // ✅ CORRIGIDO: Usar a rota correta sem /api/
                    const response = await fetch('{{ route("onboarding.pular") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    });

                    // Verificar se a resposta é JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        // Se não for JSON, tentar ler como texto para debug
                        const text = await response.text();
                        console.error('Resposta não é JSON:', text);
                        throw new Error('Resposta do servidor não é JSON. Status: ' + response.status);
                    }

                    const data = await response.json();

                    if (data.sucesso) {
                        window.location.href = data.redirecionar;
                    } else {
                        showError(data.mensagem || 'Erro desconhecido');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showError('Erro de conexão. Verifique sua internet e tente novamente.');
                } finally {
                    hideLoading();
                    continuarBtn.disabled = false;
                    pularBtn.disabled = false;
                }
            });
        });
    </script>

    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-overlay.hidden {
            display: none;
        }

        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .loading-spinner .material-symbols-outlined {
            font-size: 3rem;
            color: #3B82F6;
            animation: spin 1s linear infinite;
        }

        .loading-spinner p {
            margin-top: 15px;
            color: #333;
            font-weight: 500;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>