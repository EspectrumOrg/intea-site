<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Página Inicial - Profissional de Saúde</title>
    <link rel="stylesheet" href="{{ asset('assets/css/profissional_saude/pagina_inicial') }}">

</head>

<body>

    <header>
        <h1>Rede Social Autistas - Profissional</h1>
        <nav>
            <a href="#">Início</a>
            <a href="#">Mensagens</a>
            <a href="#">Eventos</a>
            <a href="#">Notificações <span class="notification-count">1</span></a>
            <a href="#">Configurações</a>
            <a href="#">Sair</a>
        </nav>
    </header>

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
                    <li><a href="#">Grupos</a></li>
                    <li><a href="#">Novas Mensagens</a></li>
                </ul>
            </div>

            <div class="sidebar-footer">
                © 2025 Rede Social Autistas
            </div>
        </aside>

        <main class="feed">
            <div class="create-post">
                <input type="text" placeholder="Comece uma publicação" />
                <div class="post-options">
                    <button>Vídeo</button>
                    <button>Foto</button>
                </div>
            </div>

            <article class="post">
                <div class="post-header">
                    <img class="image-post" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Yuri Garcia" />
                    <div>
                        <strong>Yuri Garcia</strong> <span>Controller Financeiro</span><br />
                        <small>1 semana atrás</small>
                    </div>
                    <button class="follow-btn">+ Seguir</button>
                </div>
                <p>Empresas! Parem de perder tempo procurando candidatos perfeitos. Eles não existem!</p>
                <div class="post-actions">
                    <button>Gostei (3.951)</button>
                    <button>Comentar (416)</button>
                    <button>Compartilhar (437)</button>
                    <button>Enviar</button>
                </div>
            </article>

            <article class="post promoted">
                <div class="post-header">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/Bradesco_Logo.svg/1200px-Bradesco_Logo.svg.png" alt="Bradesco" />
                    <div>
                        <strong>Bradesco</strong> <span>3.289.467 seguidores - Promovido</span>
                    </div>
                    <button class="follow-btn">+ Seguir</button>
                </div>
                <p>Já conhece a nossa página no LinkedIn?</p>
                <img src="https://via.placeholder.com/600x200?text=Anúncio" alt="Anúncio" />
            </article>
        </main>

        <aside class="sidebar-right">
            <div class="news">
                <h3>Notícias</h3>
                <ul>
                    <li><strong>Vagas de trainee e estágio</strong><br><small>há 56 min • 20.586 leitores</small></li>
                    <li><strong>Balanços do 2º trimestre</strong><br><small>há 56 min • 8.095 leitores</small></li>
                    <li><strong>Contagem regressiva para a COP30</strong><br><small>há 56 min • 1.725 leitores</small></li>
                    <li><strong>Inadimplência no agro pressiona bancos</strong><br><small>há 56 min • 115 leitores</small></li>
                    <li><strong>O leilão que pode redefinir a Faria Lima</strong><br><small>há 55 min</small></li>
                </ul>
                <button class="show-more">Exibir mais ▼</button>
            </div>

    </div>

</body>

</html>