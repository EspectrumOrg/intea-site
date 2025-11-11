document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalUsuarios');
    const lista = document.getElementById('listaUsuarios');
    const fechar = document.getElementById('fecharModal');

    const btnSeguindo = document.getElementById('btnSeguindo');
    const btnSeguidores = document.getElementById('btnSeguidores');

    // Função auxiliar para renderizar os usuários
    function renderizarUsuarios(data) {
        lista.innerHTML = '';

        data.forEach(u => {
            const fotoUrl = u.foto ? '/storage/' + u.foto : '/storage/default.jpg';

            // Cria link que leva para o perfil
            const a = document.createElement('a');
            a.href = `/conta/${u.id}`;
            a.style.display = 'flex';
            a.style.alignItems = 'center';
            a.style.gap = '10px';
            a.style.textDecoration = 'none';
            a.style.color = 'inherit';
            a.style.padding = '8px';
            a.style.borderRadius = '6px';
            a.style.transition = 'background 0.2s';

            a.addEventListener('mouseenter', () => a.style.background = '#f5f5f5');
            a.addEventListener('mouseleave', () => a.style.background = 'transparent');

            a.innerHTML = `
                <img src="${fotoUrl}" 
                     alt="${u.user}" 
                     style="width:35px; height:35px; border-radius:50%; object-fit:cover;">
                <span>${u.user}</span>
            `;

            lista.appendChild(a);
        });

        modal.style.display = 'block';
    }

    if (btnSeguindo) {
        btnSeguindo.addEventListener('click', () => {
            fetch(btnSeguindo.dataset.url)
                .then(res => res.json())
                .then(data => renderizarUsuarios(data))
                .catch(err => console.error('Erro ao carregar seguindo:', err));
        });
    }

    if (btnSeguidores) {
        btnSeguidores.addEventListener('click', () => {
            fetch(btnSeguidores.dataset.url)
                .then(res => res.json())
                .then(data => renderizarUsuarios(data))
                .catch(err => console.error('Erro ao carregar seguidores:', err));
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

const abrirPerfil = document.getElementById('btnAbrirModalPerfil'); // ✅ corrigido
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
