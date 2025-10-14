function toggleDropdown(event, button) {
    event.stopPropagation(); // impede o clique de fechar imediatamente
    const dropdown = button.closest('.dropdown');

    // Fecha todos os outros dropdowns abertos
    document.querySelectorAll('.dropdown.ativo').forEach(d => {
        if (d !== dropdown) d.classList.remove('ativo');
    });

    // Alterna o dropdown atual
    dropdown.classList.toggle('ativo');
}

// Fecha dropdown ao clicar fora
document.addEventListener('click', function(e) {
    const isDropdown = e.target.closest('.dropdown');
    if (!isDropdown) {
        document.querySelectorAll('.dropdown.ativo').forEach(d => d.classList.remove('ativo'));
    }
});
