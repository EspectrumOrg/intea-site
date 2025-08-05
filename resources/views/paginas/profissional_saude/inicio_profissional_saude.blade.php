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
        <a href="#">Configurações</a>
        <a href="#">Sair</a>
    </nav>
</header>

<div class="container">

    <aside class="sidebar">
        <div class="profile-summary">
            <img src="https://via.placeholder.com/100" alt="Foto do profissional" />
            <h2>Dr. Ana Silva</h2>
            <p>Psicóloga | CRP-06-12345</p>
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

    <main class="main-content">
        <h2>Feed de Atualizações</h2>

        <article class="post">
            <div class="post-header">Dr. Ana Silva</div>
            <div class="post-content">
                Hoje compartilhei um artigo sobre técnicas de comunicação com crianças autistas. Confira!
            </div>
            <div class="post-date">05/08/2025</div>
        </article>

        <article class="post">
            <div class="post-header">Rede Social Autistas</div>
            <div class="post-content">
                Evento online: Palestra sobre inclusão escolar para autistas. Inscreva-se!
            </div>
            <div class="post-date">03/08/2025</div>
        </article>

    </main>

</div>

</body>
</html>
