<script>
    // Aguarda o carregamento completo do DOM antes de executar
    document.addEventListener('DOMContentLoaded', function() {

        const body = document.body;

        const img = document.images;

        // Captura o toggle (checkbox) que ativa/desativa o modo monocromático
        const toggle = document.getElementById('monochrome-sidebar-toggle');

        // Sidebar principal
        const sidebar = document.querySelector('.container-sidebar .content');

        // Sidebar de tendências
        const sidebarTendencias = document.querySelector('.sidebar-tendencias');

        /*
        ===========================================================
          FUNÇÃO RESPONSÁVEL POR APLICAR OU REMOVER MODO MONOCROMÁTICO
          Aqui você adiciona novas áreas/componentes no futuro
          Só precis repetir o padrão "if (elemento) { add/remove class }"
        ===========================================================
        */
        function toggleMonochrome(isMonochrome) {

            if (body) {
                if (isMonochrome) {
                    // Adiciona a classe que deixa ela monocromática
                    body.classList.add('monochrome');
                } else {
                    // Remove e volta ao modo normal
                    body.classList.remove('monochrome');
                }
            }

            if (img) {
                for (let i = 0; i < img.length; i++) {
                    if (isMonochrome) {
                        img[i].classList.add('monochrome-img');
                    } else {
                        img[i].classList.remove('monochrome-img');
                    }
                }
            }

            // Sidebar principal
            if (sidebar) {
                if (isMonochrome) {
                    // Adiciona a classe que deixa ela monocromática
                    sidebar.classList.add('sidebar-monochrome');
                } else {
                    // Remove e volta ao modo normal
                    sidebar.classList.remove('sidebar-monochrome');
                }
            }

            // Sidebar de tendências
            if (sidebarTendencias) {
                if (isMonochrome) {
                    sidebarTendencias.classList.add('sidebar-tendencias-monochrome');
                } else {
                    sidebarTendencias.classList.remove('sidebar-tendencias-monochrome');
                }
            }

            /*
            ===========================================================
              COMO EXPANDIR PARA OUTROS ELEMENTOS DO SITE
            ===========================================================
            
            Exemplo: você quer aplicar monocromático nas TABS:
            
            const tabs = document.querySelector('.tabs');
            if (tabs) {
                if (isMonochrome) tabs.classList.add('tabs-monochrome');
                else tabs.classList.remove('tabs-monochrome');
            }
            
            OU 
            
            aplicar em vários elementos de uma vez:
            
            document.querySelectorAll('.card, .titulo, .botao')
            .forEach(el => {
                if (isMonochrome) el.classList.add('mono');
                else el.classList.remove('mono');
            });

            Ai no CSS, você vai adicionar as coisas, entende? Por exemplo no caso do
            monocrómatico da SIDEBAR:
            .sidebar-monochrome .nav-link span,
            .sidebar-monochrome .nav-link h1,
            .sidebar-monochrome .info h5,
            .sidebar-monochrome .info h4 {
                color: #000 !important;
            }
            Isso já vai estar no código, recomendo inclusive que deixe no Style.css tudo isso (que fica no public/assets/css)
            por que todas as páginas usam isso. Só identifica com um comentário onde inicia. Mas é basicamente isso.
            */
        }

        // Executa a função ao carregar a página com a preferência do usuário
        const userThemePreference = "<?php echo auth()->user()->tema_preferencia; ?>";
        console.log('😂 Preferência de tema do usuário:', userThemePreference);
        const isMonochrome = userThemePreference === 'monocromatico';
        toggleMonochrome(isMonochrome);

        /*
            ===========================================================
              ESCUTA O TOGGLE DE MODO MONOCROMÁTICO
              (CLIQUE DO USUÁRIO)
            ===========================================================
            */
        if (toggle) {
            toggle.addEventListener('change', function() {

                const isMonochrome = this.checked;

                // Atualiza visualmente NA HORA, sem esperar o servidor
                toggleMonochrome(isMonochrome);

                /*
                ===========================================================
                  AQUI É ENVIADO PARA O SERVIDOR
                ===========================================================
                */
                fetch('/update-theme-preference', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            tema_preferencia: isMonochrome ? 'monocromatico' : 'colorido'
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            console.log('Preferência salva com sucesso!');

                            // Feedback visual opcional
                            showFeedback('Preferência salva!', 'success');
                        } else {
                            throw new Error(data.message || 'Erro desconhecido');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);

                        // Reverte o estado caso o salvamento falhe
                        toggleMonochrome(!isMonochrome);
                        toggle.checked = !isMonochrome;

                        // Feedback de falha
                        showFeedback('Erro ao salvar preferência', 'error');
                    });
            });

            /*
            ===========================================================
              QUANDO A PÁGINA CARREGA
              Se a sidebar já vier com a classe monocromática 
              então marcamos o toggle como ativo
            ===========================================================
            */
            if (sidebarTendencias && sidebarTendencias.classList.contains('sidebar-tendencias-monochrome')) {
                toggle.checked = true;
            }
        }
    });




    /*
    ==========================================================
      FUNÇÃO DE FEEDBACK VISUAL
    ==========================================================
    */
    function showFeedback(message, type) {

        // Remove qualquer feedback anterior
        const existingFeedback = document.querySelector('.feedback-message');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Elemento de feedback
        const feedback = document.createElement('div');
        feedback.className = `feedback-message feedback-${type}`;
        feedback.textContent = message;

        feedback.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #10b981;' : 'background: #ef4444;'}
    `;

        document.body.appendChild(feedback);

        // Remove automaticamente após 3 segundos
        setTimeout(() => {
            feedback.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => feedback.remove(), 300);
        }, 3000);
    }
</script>