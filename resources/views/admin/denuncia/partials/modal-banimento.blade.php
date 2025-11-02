<!-- Modal de banimento -->
<div id="modal-banimento-denuncia-{{ $item->id }}" class="modal hidden">
    <div class="modal-denuncia-content">

        <div class="modal-dnuncia-header">
            <h2>Banir Usuário</h2>
            <button class="modal-banimento-fechar" onclick="fecharModalBanirUsuario('{{$item->id}}')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Cabeçalho -->
        <div class="modal-header">
            <h5 class="modal-title" id="modalBanirUsuarioLabel{{ $item->id }}">
                Banir Usuário
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <!-- Corpo -->
        <div class="modal-body">
            @php
            $usuarioAlvo = $item->usuarioDenunciado
            ?? $item->postagem->usuario
            ?? $item->comentario->usuario;
            @endphp

            <div class="text-center mb-3">
                <img src="{{ asset('storage/'.$usuarioAlvo->foto ?? 'assets/images/logos/contas/user.png') }}" class="rounded-circle" width="100" height="100" alt="Foto do usuário">
                <h5 class="mt-2">{{ $usuarioAlvo->user }}</h5>
                <p class="text-muted">{{ $usuarioAlvo->apelido ?? 'Sem apelido' }}</p>
            </div>

            @if ($item->postagem)
            <div class="alert alert-secondary">
                <strong>Postagem relacionada:</strong><br>
                {{ Str::limit($item->postagem->texto_postagem, 150) }}
            </div>
            @elseif ($item->comentario)
            <div class="alert alert-secondary">
                <strong>Comentário relacionado:</strong><br>
                {{ Str::limit($item->comentario->comentario, 150) }}
            </div>
            @endif

            <form action="{{ route('usuario.destroy', $usuarioAlvo->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <!-- Campo infração -->
                <label class="form-label">Infração</label>
                <select class="form-select" id="infracao-{{ $item->id }}" name="infracao" required>
                    <option value="">Tipo</option>
                    <option value="conteudo_explicito">Conteúdo Explícito</option>
                    <option value="desinformacao">Desinformação</option>
                    <option value="discurso_de_odio">Discurso de Ódio</option>
                    <option value="farsa">Farsa</option>
                    <option value="golpe">Golpe</option>
                    <option value="spam">Spam</option>
                </select>

                <!-- Campo motivo -->
                <div class="mb-3">
                    <label for="motivo{{ $item->id }}" class="form-label">Motivo do banimento</label>
                    <textarea name="motivo" id="motivo{{ $item->id }}" class="form-control" rows="3" placeholder="Descreva o motivo do banimento..." required></textarea>
                </div>

                <!-- Campos ocultos: caso a denúncia venha de postagem ou comentário -->
                @if ($item->postagem)
                <input type="hidden" name="id_postagem" value="{{ $item->postagem->id }}">
                @endif
                @if ($item->comentario)
                <input type="hidden" name="id_comentario" value="{{ $item->comentario->id }}">
                @endif

                <div class="text-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Banimento</button>
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