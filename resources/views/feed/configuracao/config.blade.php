@php
use App\Models\Tendencia;
use App\Models\Postagem;

$postsPopulares = Postagem::withCount('curtidas')
->with(['imagens', 'usuario'])
->orderByDesc('curtidas_count')
->take(5)
->get();

$tendenciasPopulares = Tendencia::populares(7)->get();

@endphp
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Perfil</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/config.css') }}">
    <!-- Postagem -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">
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
            <div class="content-popular">
                @include('profile.partials.buscar')
                @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])
            </div>
        </div>
    </div>

    <!-- modal de avisos -->
    @include("layouts.partials.avisos")

    <!-- Modal Criação de postagem -->
    @include('feed.post.create-modal')

    <script>
        // Aguarda o carregamento completo do DOM antes de executar
        document.addEventListener('DOMContentLoaded', function() {

            // Captura o toggle (checkbox) que ativa/desativa o modo monocromático
            const toggle = document.getElementById('monochrome-sidebar-toggle');

            // Sidebar principal
            const sidebar = document.querySelector('.container-sidebar .content');

            // Sidebar de tendências
            const sidebarTendencias = document.querySelector('.sidebar-tendencias');

            /*
            ===========================================================
              FUNÇÃO RESPONSÁVEL POR APLICAR OU REMOVER MODO MONOCROMÁTICO
              Aqui você adiciona novas áreas/componentes no futuro
              Só precis repetir o padrão "if (elemento) { add/remove class }"
            ===========================================================
            */
            function toggleMonochrome(isMonochrome) {

                // Sidebar principal
                if (sidebar) {
                    if (isMonochrome) {
                        // Adiciona a classe que deixa ela monocromática
                        sidebar.classList.add('sidebar-monochrome');
                    } else {
                        // Remove e volta ao modo normal
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

                /*
                ===========================================================
                  COMO EXPANDIR PARA OUTROS ELEMENTOS DO SITE
                ===========================================================
                
                Exemplo: você quer aplicar monocromático nas TABS:
                
                const tabs = document.querySelector('.tabs');
                if (tabs) {
                    if (isMonochrome) tabs.classList.add('tabs-monochrome');
                    else tabs.classList.remove('tabs-monochrome');
                }
                
                OU 
                
                aplicar em vários elementos de uma vez:
                
                document.querySelectorAll('.card, .titulo, .botao')
                .forEach(el => {
                    if (isMonochrome) el.classList.add('mono');
                    else el.classList.remove('mono');
                });

                Ai no CSS, você vai adicionar as coisas, entende? Por exemplo no caso do
                monocrómatico da SIDEBAR:
                .sidebar-monochrome .nav-link span,
                .sidebar-monochrome .nav-link h1,
                .sidebar-monochrome .info h5,
                .sidebar-monochrome .info h4 {
                    color: #000 !important;
                }
                Isso já vai estar no código, recomendo inclusive que deixe no Style.css tudo isso (que fica no public/assets/css)
                por que todas as páginas usam isso. Só identifica com um comentário onde inicia. Mas é basicamente isso.
                */
            }


            /*
            ===========================================================
              ESCUTA O TOGGLE DE MODO MONOCROMÁTICO
              (CLIQUE DO USUÁRIO)
            ===========================================================
            */
            if (toggle) {
                toggle.addEventListener('change', function() {

                    const isMonochrome = this.checked;

                    // Atualiza visualmente NA HORA, sem esperar o servidor
                    toggleMonochrome(isMonochrome);

                    /*
                    ===========================================================
                      AQUI É ENVIADO PARA O SERVIDOR
                    ===========================================================
                    */
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

                                // Feedback visual opcional
                                showFeedback('Preferência salva!', 'success');
                            } else {
                                throw new Error(data.message || 'Erro desconhecido');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);

                            // Reverte o estado caso o salvamento falhe
                            toggleMonochrome(!isMonochrome);
                            toggle.checked = !isMonochrome;

                            // Feedback de falha
                            showFeedback('Erro ao salvar preferência', 'error');
                        });
                });

                /*
                ===========================================================
                  QUANDO A PÁGINA CARREGA
                  Se a sidebar já vier com a classe monocromática 
                  então marcamos o toggle como ativo
                ===========================================================
                */
                if (sidebarTendencias && sidebarTendencias.classList.contains('sidebar-tendencias-monochrome')) {
                    toggle.checked = true;
                }
            }
        });




        /*
        ==========================================================
          FUNÇÃO DE FEEDBACK VISUAL
        ==========================================================
        */
        function showFeedback(message, type) {

            // Remove qualquer feedback anterior
            const existingFeedback = document.querySelector('.feedback-message');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            // Elemento de feedback
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
    </script>
</body>

</html>