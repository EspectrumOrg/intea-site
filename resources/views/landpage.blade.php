<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comunidade Autista</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/auth/landpage.css') }}">
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container nav-container">
      <div class="logo-container">
        <img src="{{ asset('assets/images/logos/intea/logo-brain.png') }}" alt="Logo" class="logo-img">
      </div>
      <nav>
        <ul class="nav-links">
          <li><a href="#sobre">Sobre</a></li>
          <li><a href="{{ route('login') }}">Login</a></li>
        </ul>
      </nav>
      <div class="menu-toggle">&#9776;</div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-content">
      <h2>Um espaço seguro e inclusivo</h2>
      <p>
        O Autismo afeta cerca de <strong>2,4 milhões de brasileiros</strong>, sendo <strong>1,2% da população</strong>.
        Nosso objetivo é criar um espaço acolhedor para esses pais, familiares e pessoas autistas.
      </p>
    </div>
  </section>

  <!-- Sobre -->
  <section id="sobre" class="section">
    <h3>Nosso Objetivo</h3>
    <div class="text-img">
      <img src="{{ asset('assets/images/landpage/Autismo.jpg') }}" alt="Comunidade unida">
      <p>
        Criamos esta comunidade para oferecer informação, apoio e acolhimento.
        Aqui, famílias, profissionais de saúde e autistas podem se conectar,
        compartilhar experiências e encontrar suporte mútuo.
      </p>
    </div>
  </section>
  <!-- Footer -->
  <footer>
    <p>© 2025 Comunidade Autista. Todos os direitos reservados.</p>
  </footer>

  <script src="{{ asset('assets/js/auth/landpage.js') }}"></script>
</body>
</html>
