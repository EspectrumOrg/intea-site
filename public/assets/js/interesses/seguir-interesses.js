document.addEventListener('DOMContentLoaded', function() {
    // Botões Seguir/Deixar de seguir
    document.querySelectorAll('.btn-seguir-interesse, .btn-deixar-seguir, .btn-seguir').forEach(btn => {
        btn.addEventListener('click', function() {
            const interesseId = this.dataset.interesseId;
            const estaSeguindo = this.classList.contains('seguindo');
            const isDeixarSeguir = this.classList.contains('btn-deixar-seguir');
            
            const url = (estaSeguindo || isDeixarSeguir) 
                ? `/api/interesses/${interesseId}/deixar-seguir`
                : `/api/interesses/${interesseId}/seguir`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    if (estaSeguindo || isDeixarSeguir) {
                        this.classList.remove('seguindo');
                        if (this.classList.contains('btn-seguir')) {
                            this.textContent = 'Seguir';
                        } else {
                            this.innerHTML = '<span class="material-symbols-outlined">add</span> Seguir';
                        }
                    } else {
                        this.classList.add('seguindo');
                        if (this.classList.contains('btn-seguir')) {
                            this.textContent = 'Seguindo';
                        } else {
                            this.innerHTML = '<span class="material-symbols-outlined">check</span> Seguindo';
                        }
                    }
                    
                    // Atualizar contador se disponível
                    if (data.dados && data.dados.contador_membros) {
                        const contadorElement = document.querySelector(`[data-interesse-contador="${interesseId}"]`);
                        if (contadorElement) {
                            contadorElement.textContent = data.dados.contador_membros;
                        }
                    }

                    // Recarregar a página para atualizar a navegação
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar ação');
            });
        });
    });

    // Filtros na página de interesses
    const filterBtns = document.querySelectorAll('.filter-btn');
    const interesseCards = document.querySelectorAll('.interesse-card-full');
    
    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Ativar botão
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filtrar cards
                interesseCards.forEach(card => {
                    if (filter === 'all') {
                        card.style.display = 'flex';
                    } else if (filter === 'destaque') {
                        card.style.display = card.dataset.category === 'destaque' ? 'flex' : 'none';
                    } else if (filter === 'popular') {
                        card.style.display = card.dataset.popular === 'popular' ? 'flex' : 'none';
                    }
                });
            });
        });
    }
});