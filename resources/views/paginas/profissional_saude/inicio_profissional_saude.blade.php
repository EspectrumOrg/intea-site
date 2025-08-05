<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Página Inicial - Profissional de Saúde</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f5f7fa;
            color: #333;
        }
        header {
            background-color: #0d6efd;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav a {
            color: white;
            margin-left: 1rem;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            display: flex;
            padding: 2rem;
            gap: 2rem;
            max-width: 1200px;
            margin: auto;
        }
        .sidebar {
            width: 250px;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .profile-summary {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .profile-summary img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }
        .main-content {
            flex: 1;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .post {
            border-bottom: 1px solid #ddd;
            padding: 1rem 0;
        }
        .post:last-child {
            border-bottom: none;
        }
        .post-header {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .post-content {
            margin-bottom: 0.5rem;
        }
        .sidebar-footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #666;
            text-align: center;
        }
    </style>
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
