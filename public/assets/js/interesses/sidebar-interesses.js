document.addEventListener('DOMContentLoaded', function() {
    // Ativar item ativo na sidebar
    const currentPath = window.location.pathname;
    
    // Para links de interesses
    document.querySelectorAll('.interesse-nav-item').forEach(item => {
        if (item.href === window.location.href) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Smooth scroll para seção de interesses se estiver muito longa
    const navSection = document.querySelector('.nav-section');
    if (navSection && navSection.querySelectorAll('.interesse-nav-item').length > 5) {
        navSection.style.maxHeight = '200px';
        navSection.style.overflowY = 'auto';
        navSection.style.overflowX = 'hidden';
        
        // Estilo para scrollbar
        navSection.style.scrollbarWidth = 'thin';
        navSection.style.scrollbarColor = '#c1c1c1 transparent';
    }

    // Adicionar tooltip para interesses com nomes longos
    document.querySelectorAll('.interesse-nav-item h1').forEach(title => {
        if (title.scrollWidth > title.clientWidth) {
            title.setAttribute('title', title.textContent);
        }
    });
});