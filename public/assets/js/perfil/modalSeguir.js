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
document.addEventListener('DOMContentLoaded', function() {
            // Controle das abas
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const tabsWrapper = document.querySelector('.profile-tabs-wrapper');
            const prevBtn = document.querySelector('.tab-scroll-prev');
            const nextBtn = document.querySelector('.tab-scroll-next');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    // Remove classe active de todos os botões e conteúdos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    // Adiciona classe active ao botão clicado e conteúdo correspondente
                    this.classList.add('active');
                    const targetContent = document.getElementById(`${tabId}-tab`);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });

            // Controle do scroll horizontal
            function updateScrollButtons() {
                if (!tabsWrapper || !prevBtn || !nextBtn) return;
                const scrollLeft = tabsWrapper.scrollLeft;
                const scrollWidth = tabsWrapper.scrollWidth;
                const clientWidth = tabsWrapper.clientWidth;

                // Mostra/oculta botões baseado no scroll
                prevBtn.style.display = scrollLeft > 0 ? 'flex' : 'none';
                nextBtn.style.display = scrollLeft < (scrollWidth - clientWidth - 10) ? 'flex' : 'none';

                // Ativa/desativa botões
                prevBtn.disabled = scrollLeft <= 0;
                nextBtn.disabled = scrollLeft >= (scrollWidth - clientWidth - 10);
            }

            // Eventos dos botões de scroll
            if (prevBtn && nextBtn && tabsWrapper) {
                prevBtn.addEventListener('click', () => {
                    tabsWrapper.scrollBy({ left: -200, behavior: 'smooth' });
                });
                nextBtn.addEventListener('click', () => {
                    tabsWrapper.scrollBy({ left: 200, behavior: 'smooth' });
                });

                // Atualiza botões quando scrollar
                tabsWrapper.addEventListener('scroll', updateScrollButtons);

                // Atualiza botões no carregamento e redimensionamento
                window.addEventListener('resize', updateScrollButtons);
                updateScrollButtons();
            }

            // Scroll suave para a aba ativa se estiver fora da view
            function scrollToActiveTab() {
                const activeTab = document.querySelector('.tab-button.active');
                if (activeTab && tabsWrapper) {
                    const tabRect = activeTab.getBoundingClientRect();
                    const wrapperRect = tabsWrapper.getBoundingClientRect();

                    if (tabRect.left < wrapperRect.left || tabRect.right > wrapperRect.right) {
                        activeTab.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest', 
                            inline: 'center' 
                        });
                    }
                }
            }

            // Recalcula scroll quando mudar de aba
            tabButtons.forEach(button => {
                button.addEventListener('click', scrollToActiveTab);
            });
        });