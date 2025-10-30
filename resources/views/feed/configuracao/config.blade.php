@php
use App\Models\Postagem;

if (!isset($postsPopulares)) {
    $postsPopulares = Postagem::withCount('curtidas')
        ->with(['imagens', 'usuario'])
        ->orderByDesc('curtidas_count')
        ->take(5)
        ->get();
}
@endphp
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Perfil</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
</head>

<body>
    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- conteúdo principal -->
            <div class="container-main">
                <div class="profile-container">
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            @if (!empty($user->foto))
                            <img src="{{ asset('storage/'.$user->foto) }}" class="card-img-top" alt="foto perfil">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
                            @endif
                        </div>
                        <div class="profile-info">
                            <h1>{{ $user->nome }}</h1>
                            <p class="username"> {{ $user->user }}</p>
                            <p class="bio">{{ $user->descricao ?? 'Sem descrição' }}</p>
                            <p class="tipo-usuario">
                                @switch($user->tipo_usuario)
                                    @case(1) Administrador @break
                                    @case(2) Autista @break
                                    @case(3) Comunidade @break
                                    @case(4) Profissional de Saúde @break
                                    @case(5) Responsável @break
                                @endswitch
                            </p>
                        </div>
                    </div>
<!-- Seção Acessibilidade -->
                    <div class="config-section">
                        <h3>Acessibilidade</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Modo Monocromático</h4>
                                <p>Ativa a escala monocromatica para pessoas com sensibilidade</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="monochrome-sidebar-toggle" 
                                    {{ Auth::user()->tema_preferencia == 'monocromatico' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <!-- Mostrando diretamente "Atualizar senha" e "Excluir conta" -->
                    @if(auth()->id() == $user->id)
                    <div class="profile-settings-direct">
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                    @endif
                </div>
            </div>
            @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])        
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('monochrome-sidebar-toggle');
    const sidebar = document.querySelector('.container-sidebar .content');
    const sidebarTendencias = document.querySelector('.sidebar-tendencias');
    
    // Função para aplicar/remover modo monocromático
    function toggleMonochrome(isMonochrome) {
        // Sidebar principal
        if (sidebar) {
            if (isMonochrome) {
                sidebar.classList.add('sidebar-monochrome');
            } else {
                sidebar.classList.remove('sidebar-monochrome');
            }
        }
        
        // Sidebar de tendências
        if (sidebarTendencias) {
            if (isMonochrome) {
                sidebarTendencias.classList.add('sidebar-tendencias-monochrome');
            } else {
                sidebarTendencias.classList.remove('sidebar-tendencias-monochrome');
            }
        }
    }
    
    if (toggle) {
        toggle.addEventListener('change', function() {
            const isMonochrome = this.checked;
            
            // Atualizar visualmente imediatamente (sem esperar pelo servidor)
            toggleMonochrome(isMonochrome);
            
            // Enviar para o servidor em segundo plano
            fetch('/update-theme-preference', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tema_preferencia: isMonochrome ? 'monocromatico' : 'colorido'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Preferência salva com sucesso!');
                    
                    // Opcional: mostrar feedback visual sucesso
                    showFeedback('Preferência salva!', 'success');
                } else {
                    throw new Error(data.message || 'Erro desconhecido');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                
                // Reverter visualmente em caso de erro
                toggleMonochrome(!isMonochrome);
                toggle.checked = !isMonochrome;
                
                // Mostrar feedback de erro
                showFeedback('Erro ao salvar preferência', 'error');
            });
        });
        
        // Aplicar estado inicial baseado no toggle (caso a página seja carregada com a preferência já salva)
        if (sidebarTendencias && sidebarTendencias.classList.contains('sidebar-tendencias-monochrome')) {
            toggle.checked = true;
        }
    }
});

// Função para mostrar feedback visual
function showFeedback(message, type) {
    // Remove feedback anterior se existir
    const existingFeedback = document.querySelector('.feedback-message');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Cria novo feedback
    const feedback = document.createElement('div');
    feedback.className = `feedback-message feedback-${type}`;
    feedback.textContent = message;
    feedback.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #10b981;' : 'background: #ef4444;'}
    `;
    
    document.body.appendChild(feedback);
    
    // Remove automaticamente após 3 segundos
    setTimeout(() => {
        feedback.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => feedback.remove(), 300);
    }, 3000);
}

// Adicionar os keyframes de animação
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Estilos para modo monocromático do sidebar de tendências */
    .sidebar-tendencias-monochrome {
        filter: grayscale(100%);
        transition: filter 0.3s ease;
    }
    
    .sidebar-tendencias-monochrome .sidebar-header {
        color: #666 !important;
    }
    
    .sidebar-tendencias-monochrome .sidebar-header span {
        filter: grayscale(100%);
    }
    
    .sidebar-tendencias-monochrome .tendencia-item {
        color: #666 !important;
        border-color: #ddd !important;
    }
    
    .sidebar-tendencias-monochrome .tendencia-nome {
        color: #666 !important;
    }
    
    .sidebar-tendencias-monochrome .tendencia-contador {
        color: #999 !important;
    }
    
    .sidebar-tendencias-monochrome .ver-mais a {
        color: #666 !important;
        border-color: #ddd !important;
        background-color: #f5f5f5 !important;
    }
    
    .sidebar-tendencias-monochrome .no-tendencias p {
        color: #999 !important;
    }
    
    .sidebar-tendencias-monochrome .material-symbols-outlined {
        filter: grayscale(100%);
    }
`;
document.head.appendChild(style);
</script>
</body>
</html>
