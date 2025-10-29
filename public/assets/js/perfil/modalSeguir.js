document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalUsuarios');
    const lista = document.getElementById('listaUsuarios');
    const fechar = document.getElementById('fecharModal');

    const btnSeguindo = document.getElementById('btnSeguindo');
    const btnSeguidores = document.getElementById('btnSeguidores');

    if (btnSeguindo) {
        btnSeguindo.addEventListener('click', () => {
            fetch(btnSeguindo.dataset.url)
                .then(res => res.json())
                .then(data => {
                    lista.innerHTML = '';
                    data.forEach(u => {
                        lista.innerHTML += `
    <li style="display:flex; align-items:center; gap:10px;">
        <img src="${u.foto ? '/storage/' + u.foto : '/storage/default.jpg'}" 
             alt="${u.user}" 
             style="width:30px; height:30px; border-radius:50%;">
        <span>${u.user}</span>
    </li>`;
                    });
                    modal.style.display = 'block';
                });
        });
    }

    if (btnSeguidores) {
        btnSeguidores.addEventListener('click', () => {
            fetch(btnSeguidores.dataset.url)
                .then(res => res.json())
                .then(data => {
                    lista.innerHTML = '';
                    data.forEach(u => {
                        lista.innerHTML +=`
    <li style="display:flex; align-items:center; gap:10px;">
        <img src="${u.foto ? '/storage/' + u.foto : '/storage/default.jpg'}" 
             alt="${u.user}" 
             style="width:30px; height:30px; border-radius:50%;">
        <span>${u.user}</span>
    </li>`;
                    });
                    modal.style.display = 'block';
                });
        });
    }

    if (fechar) {
        fechar.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const modalPerfil = document.getElementById('modalPerfil');
    const modalRemover = document.getElementById('modalRemoverDependente');

    const abrirPerfil = document.getElementById('abrirModalPerfil');
    const fecharPerfil = document.getElementById('fecharModalPerfil');
    const abrirRemover = document.getElementById('abrirModalRemover');
    const fecharRemover = document.getElementById('fecharModalRemover');

    // Modal de adicionar dependente
    if (abrirPerfil && modalPerfil && fecharPerfil) {
        abrirPerfil.addEventListener('click', () => modalPerfil.style.display = 'flex');
        fecharPerfil.addEventListener('click', () => modalPerfil.style.display = 'none');
        modalPerfil.addEventListener('click', e => {
            if (e.target === modalPerfil) modalPerfil.style.display = 'none';
        });
    }

    // Modal de remover dependente
    if (abrirRemover && modalRemover && fecharRemover) {
        abrirRemover.addEventListener('click', () => modalRemover.style.display = 'flex');
        fecharRemover.addEventListener('click', () => modalRemover.style.display = 'none');
        modalRemover.addEventListener('click', e => {
            if (e.target === modalRemover) modalRemover.style.display = 'none';
        });
    }
});
