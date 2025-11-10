    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/modal-banimento.css') }}">

    <div id="modal-banimento-usuario-{{ $usuario->id }}" class="modal-banimento-overlay" style="display: none;">
        <div class="modal-banimento-content">
            <div class="modal-banimento-header">
                <h2>Banir Usuário</h2>
                <button class="modal-banimento-fechar" onclick="fecharModalBanimentoUsuarioEspecifico('{{ $usuario->id }}')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="modal-banimento-body">
                <div class="modal-banimento-usuario">
                    <div class="nome-usuario-banimento">
                        <img src="{{ asset('storage/'.$usuario->foto ?? 'assets/images/logos/contas/user.png') }}" alt="Foto do usuário">
                        <h3>{{ $usuario->user }}</h3>
                        <p>{{ $usuario->apelido ?? 'Sem apelido' }}</p>
                    </div>
                    <div class="descricao-usuario-banimento">
                        <p>{{ $usuario->descricao ?? 'Sem descrição' }}</p>
                    </div>
                </div>

                <form action="{{ route('usuario.destroy', $usuario->id) }}" method="POST" class="modal-banimento-form">
                    @csrf
                    @method('DELETE')

                    <label for="infracao-{{ $usuario->id }}">Infração</label>
                    <select id="infracao-{{ $usuario->id }}" name="infracao" required>
                        <option value="">Selecione</option>
                        <option value="conteudo_explicito">Conteúdo Explícito</option>
                        <option value="desinformacao">Desinformação</option>
                        <option value="discurso_de_odio">Discurso de Ódio</option>
                        <option value="farsa">Farsa</option>
                        <option value="golpe">Golpe</option>
                        <option value="spam">Spam</option>
                    </select>

                    <label for="motivo-{{ $usuario->id }}">Motivo do banimento</label>
                    <textarea 
                        id="motivo-{{ $usuario->id }}" 
                        rows="5"
                        name="motivo" placeholder="Motivo do banimento" required></textarea>

                    <div class="modal-banimento-botoes">
                        <button type="submit" class="modal-banimento-confirmar">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        function abrirModalBanimentoUsuarioEspecifico(usuarioId) {
            const modalBanimentoUsuario = document.getElementById(`modal-banimento-usuario-${usuarioId}`);
            if (modalBanimentoUsuario) modalBanimentoUsuario.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function fecharModalBanimentoUsuarioEspecifico(usuarioId) {
            const modalBanimentoUsuario = document.getElementById(`modal-banimento-usuario-${usuarioId}`);
            if (modalBanimentoUsuario) modalBanimentoUsuario.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('click', function(event) {
            const todosOsModaisBanimentoUsuarios = document.querySelectorAll('.modal-banimento-overlay');
            todosOsModaisBanimentoUsuarios.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>