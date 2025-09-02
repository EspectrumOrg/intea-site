<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Página Inicial - Profissional de Saúde</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/profissional_saude/pagina_inicial.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}">
</head>

<body>
    <div class="layout">
        <!-- conteúdo navbar  -->
        <div class="container-content-nav">
            @include('layouts.partials.menu')
        </div>
        <div class="container">

            <aside class="sidebar">
                <div class="profile-summary">
                    <img src="https://via.placeholder.com/100" alt="Foto do profissional" />
                    @php
                    $usuario = Auth::user();
                    $profissional = $usuario->profissionalSaude ?? null;
                    @endphp

                    <h2>Dr. {{ $usuario->nome }}</h2>

                    @if ($profissional)
                    <p>
                        {{ $profissional->tipo_profissional ?? 'Profissão não informada' }}
                        |
                        {{ $profissional->registro_profissional ?? 'Registro profissional não informado' }}
                    </p>
                    @else
                    <p>Informações profissionais não disponíveis.</p>
                    @endif

                    <p>Especialista em Autismo</p>
                </div>

                <div>
                    <h3>Contatos Rápidos</h3>
                    <ul>
                        <li><a href="#">Pacientes</a></li>
                        <li><a href="#">Agenda</a></li>
                        <li><a href="#">Relatórios</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Estatísticas</h3>
                    <ul>
                        <li>Pacientes ativos: 12</li>
                        <li>Mensagens hoje: 5</li>
                        <li>Consultas agendadas: 3</li>
                    </ul>
                </div>

                <div class="sidebar-footer">
                    © 2025 Rede Social Autistas
                </div>
            </aside>

            <div class="chats-container">
                <div class="chats-header">
                    <h2>Conversas com Pacientes</h2>
                    <div class="search-chats">
                        <input type="text" placeholder="Buscar paciente...">
                        <button>Buscar</button>
                    </div>
                </div>

                <div class="chat-list">
                    <!-- Chat 1 -->
                    <div class="chat-item active">
                        <img src="https://via.placeholder.com/50" alt="Paciente" class="chat-avatar">
                        <div class="chat-info">
                            <h3>Ana Oliveira</h3>
                            <p class="last-message">Olá doutor, gostaria de marcar uma consulta...</p>
                        </div>
                        <div class="chat-meta">
                            <div class="timestamp">10:30</div>
                            <span class="message-status status-unread"></span>
                        </div>
                    </div>

                    <!-- Chat 2 -->
                    <div class="chat-item">
                        <img src="https://via.placeholder.com/50" alt="Paciente" class="chat-avatar">
                        <div class="chat-info">
                            <h3>Pedro Santos</h3>
                            <p class="last-message">Obrigado pela consulta de ontem!</p>
                        </div>
                        <div class="chat-meta">
                            <div class="timestamp">Ontem</div>
                            <span class="message-status status-responded"></span>
                        </div>
                    </div>

                    <!-- Chat 3 -->
                    <div class="chat-item">
                        <img src="https://via.placeholder.com/50" alt="Paciente" class="chat-avatar">
                        <div class="chat-info">
                            <h3>Mariana Costa</h3>
                            <p class="last-message">Preciso renovar a receita médica.</p>
                        </div>
                        <div class="chat-meta">
                            <div class="timestamp">12/05</div>
                            <span class="message-status status-pending"></span>
                        </div>
                    </div>

                    <!-- Chat 4 -->
                    <div class="chat-item">
                        <img src="https://via.placeholder.com/50" alt="Paciente" class="chat-avatar">
                        <div class="chat-info">
                            <h3>João Mendonça</h3>
                            <p class="last-message">Poderia explicar novamente os exercícios?</p>
                        </div>
                        <div class="chat-meta">
                            <div class="timestamp">11/05</div>
                            <span class="message-status status-read"></span>
                        </div>
                    </div>

                    <!-- Chat 5 -->
                    <div class="chat-item">
                        <img src="https://via.placeholder.com/50" alt="Paciente" class="chat-avatar">
                        <div class="chat-info">
                            <h3>Laura Fernandes</h3>
                            <p class="last-message">Estou com dúvidas sobre a medicação.</p>
                        </div>
                        <div class="chat-meta">
                            <div class="timestamp">10/05</div>
                            <span class="message-status status-responded"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>