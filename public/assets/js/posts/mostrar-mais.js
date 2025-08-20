function toggleTexto(id, btn) {
    const curto = document.getElementById(`texto-${id}`);
    const completo = document.getElementById(`texto-completo-${id}`);

    const mostrandoCurto = curto.style.display !== 'none';
    curto.style.display = mostrandoCurto ? 'none' : 'block';
    completo.style.display = mostrandoCurto ? 'block' : 'none';
}
