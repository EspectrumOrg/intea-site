<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comunidade Autista</title>
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/auth/landpage.css') }}"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="http://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">
</head>
<body>
  <!-- NAVBAR OTIMIZADA -->
  <nav class="navbar">
    <div class="navbar-container">
      <div class="logo-container">
<a href="{{ route('login') }}"><img src="{{ asset('assets/images/landpage/logoIntea2.png') }}" alt="inTEA - Comunidade Autista" class="logo-img"></a>
      </div>
      <div class="navbar-links">
        <a href="#hero" class="nav-link animated-underline">Home</a>
        <a href="#solutions" class="nav-link animated-underline">Qualidades</a>
        <a href="#features" class="nav-link animated-underline">Funcionalidades</a>
        <a href="#contact" class="nav-link animated-underline">Fale conosco</a>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION OTIMIZADA -->
  <section id="hero" class="hero">
  <div class="hero-content">
    <div class="hero-text">
<h1 class="hero-title">
  <span class="gradient-text">Sempre apoiando e conectando</span><br>
  <span class="text-azul-solido">a comunidade autista</span>
</h1>
      <p class="hero-description">
        Um espaço seguro, compreensivo e construído com a comunidade, onde você pode ser verdadeiramente você. Conecte-se, compartilhe experiências e encontre suporte que entende suas necessidades.
      </p>
      <div class="hero-buttons">
        <a href="{{ route('login') }}"><button class="btn-primary">Teste agora</button></a>
        <button class="btn-secondary">
          <i class="fas fa-play"></i>
          <span>Assista à demo</span>
        </button>
      </div>
    </div>
    <div class="hero-image">
     <img src="{{ asset('assets/images/landpage/feliz.jpg') }}" alt="Comunidade unida - inTEA" class="hero-image-floating">
</section>

  <!-- SOLUTIONS SECTION OTIMIZADA -->
  <section id="solutions" class="section solutions">
    <div class="container">
      <div class="solutions-content">
        <div class="solutions-header">
          <span class="solution-tag">Apoio e Comunidade</span>
  <h2 class="section-title">
  <span class="gradient-text">Criado para a</span> 
  <span class="text-azul-solido">comunidade autista</span>
</h2>
          <p class="section-description">
            A comunidade autista enfrenta muitos desafios, principalmente com a dificuldade na comunicação e conexão com outras pessoas, estamos aqui para resolver esse problema.
          </p>
        </div>
        <div class="solutions-grid">
          <div class="solution-item">
            <div class="solution-icon">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="solution-text">
              <h3>Validação e Identificação</h3>
              <p>Encontre pessoas que compartilham experiências similares, reduzindo a sensação de isolamento e proporcionando um senso de pertencimento genuíno.</p>
            </div>
          </div>
          <div class="solution-item">
            <div class="solution-icon">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="solution-text">
              <h3>Troca de Experiências</h3>
              <p>Compartilhe estratégias, descobertas e insights sobre como navegar em um mundo neurotípico, aprendendo com a sabedoria coletiva da comunidade.</p>
            </div>
          </div>
          <div class="solution-item">
            <div class="solution-icon">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="solution-text">
              <h3>Crescimento Pessoal</h3>
              <p>Um ambiente seguro para desenvolver habilidades sociais no seu próprio ritmo, com respeito às suas necessidades individuais e limites sensoriais.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES SECTION OTIMIZADA -->
  <section id="features" class="section features">
    <div class="container">
 <div class="features-header text-center">
  <span class="solution-tag">Nossas funcionalidades</span>
  <h2 class="section-title">
  <span class="gradient-text">Tudo que você precisa</span> 
  <span class="text-azul-solido">para se conectar</span>
</h2>
  <p class="section-description">
    Nossa plataforma oferece todas as ferramentas e recursos necessários para criar uma comunidade inclusiva e acolhedora para pessoas no espectro autista.
  </p>
</div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/posts.png') }}" alt="Comunidade Segura">
          </div>
          <h3>Comunidade Segura</h3>
          <p>Um espaço moderado onde você pode ser você mesmo, sem medo de julgamentos ou incompreensões.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/fotos.png') }}" alt="Recursos de postagem">
          </div>
          <h3>Recursos de Postagem</h3>
          <p>Recursos para uma comunicação rica, como postagem de imagens, curtidas, comentários e muito mais.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/privacidade.png') }}" alt="Privacidade Garantida">
          </div>
          <h3>Privacidade Garantida</h3>
          <p>Controle total sobre suas informações e quem pode vê-las, com configurações intuitivas de privacidade.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/interesse.png') }}" alt="Grupos de Interesse">
          </div>
          <h3>Grupos de Interesse</h3>
          <p>Participe de comunidades temáticas baseadas em seus interesses específicos e hiperfocos.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/família.png') }}" alt="Controle Responsável">
          </div>
          <h3>Controle Responsável</h3>
          <p>Para os autistas de menos de 18 anos, temos a opção de um controle de suas contas por seus responsáveis.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <img src="{{ asset('assets/images/landpage/psicologia.png') }}" alt="Profissionais Credenciados">
          </div>
          <h3>Profissionais Credenciados</h3>
          <p>Encontre profissionais em autismo credenciados, que poderão fornecer seus serviços.</p>
        </div>
      </div>
    </div>
  </section>

<!-- CONTACT SECTION ORIGINAL COM IMAGEM -->
<div class="contact1" id="contact">
  <div class="container-contact1">
    <div class="contact1-pic js-tilt" data-tilt>
     <img src="{{ asset('assets/images/landpage/img-01.png') }}" alt="IMG">
    </div>

    <form class="contact1-form validate-form">
      <span class="contact1-form-title">
        Fale conosco  
      </span>

      <div class="wrap-input1 validate-input" data-validate="Nome é necessário">
        <input class="input1" type="text" name="name" placeholder="Nome">
        <span class="shadow-input1"></span>
      </div>

      <div class="wrap-input1 validate-input" data-validate="Coloque um email válido: ex@abc.xyz">
        <input class="input1" type="text" name="email" placeholder="Email">
        <span class="shadow-input1"></span>
      </div>

      <div class="wrap-input1 validate-input" data-validate="Assunto é necessário">
        <input class="input1" type="text" name="subject" placeholder="Assunto">
        <span class="shadow-input1"></span>
      </div>

      <div class="wrap-input1 validate-input" data-validate="menssagem é necessária">
        <textarea class="input1" name="message" placeholder="Menssagem"></textarea>
        <span class="shadow-input1"></span>
      </div>

      <div class="container-contact1-form-btn">
        <button class="contact1-form-btn">
          <span>
            Enviar email
            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
          </span>
        </button>
      </div>
    </form>
  </div>
</div>
  <!-- FOOTER OTIMIZADO -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-logo">
       <img src="{{ asset('assets/images/landpage/logoIntea2.png') }}" alt="inTEA">
        <div class="footer-social">
          <a href="#" class="social-link" id="instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="social-link" id="facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="social-link" id="whatsapp">
            <i class="fab fa-whatsapp"></i>
          </a>
        </div>
      </div>
      <div class="footer-info">
        <div>
          <i class="fas fa-map-marker-alt"></i>
          <p>R. Feliciano de Mendonça, 290 - Guaianases, São Paulo</p>
        </div>
        <div>
          <i class="fas fa-phone"></i>
          <p>(11) 2957-3859</p>
        </div>
        <div>
          <i class="fas fa-envelope"></i>
          <p>Espectrum@gmail.com</p>
        </div>
      </div>
      <div class="footer-about">
        <span>Sobre a empresa</span>
        <p>Valorizamos soluções empáticas, contando com membros que vivenciam diretamente as realidades que abordamos. Convidamos parceiros a se juntarem a nós para transformar realidades e tornar visível o invisível.</p>
      </div>
    </div>
  </footer>

  <script>
    // Script otimizado para funcionalidades essenciais
    document.addEventListener('DOMContentLoaded', function() {
      // Smooth scroll para links de navegação
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      });

      // Menu mobile simples
      const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
      const navbarLinks = document.querySelector('.navbar-links');
      
      if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
          navbarLinks.style.display = navbarLinks.style.display === 'flex' ? 'none' : 'flex';
        });
      }

      // Animação de entrada para cards
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observar elementos para animação
      document.querySelectorAll('.feature-card, .solution-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
      });
    });
  </script>
</body>
</html>