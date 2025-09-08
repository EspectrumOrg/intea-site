// Menu toggle
const menuToggle = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");

menuToggle.addEventListener("click", () => {
  navLinks.classList.toggle("show");
});

// Animação dos cards ao rolar
const cards = document.querySelectorAll(".card");

function mostrarCards() {
  const gatilho = window.innerHeight * 0.85; // 85% da tela
  cards.forEach(card => {
    const topo = card.getBoundingClientRect().top;
    if (topo < gatilho) {
      card.classList.add("show");
    }
  });
}

window.addEventListener("scroll", mostrarCards);
window.addEventListener("load", mostrarCards);