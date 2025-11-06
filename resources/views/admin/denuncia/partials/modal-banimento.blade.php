<!-- Modal de banimento -->
<div id="modal-banimento-denuncia-{{$item->id}}" class="modal hidden">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Banir Usuário</h2>
            <button class="modal-fechar" onclick="fecharModalBanimentoDenuncia('{{$item->id}}')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Corpo -->
        <div class="modal-body">
            @php
            $usuarioAlvo = $item->usuarioDenunciado
            ?? $item->postagem->usuario
            ?? $item->comentario->usuario;
            @endphp

            <p>denunciante {{ $item->usuarioDenunciante->user}}</p>

            <div class="conteudo-modal">
                <img src="{{ asset('storage/'.$usuarioAlvo->foto ?? 'assets/images/logos/contas/user.png') }}" class="rounded-circle" width="100" height="100" alt="Foto do usuário">
                <h5 class="mt-2">{{ $usuarioAlvo->user }}</h5>
                <p class="text-muted">{{ $usuarioAlvo->apelido ?? 'Sem apelido' }}</p>
            </div>

            @if ($item->postagem)
            <div class="conteudo-modal">
                <strong>Postagem relacionada:</strong><br>
                {{ Str::limit($item->postagem->texto_postagem, 150) }}<br>
                @if ($item->postagem->imagens && $item->postagem->imagens->isNotEmpty())
                <img 
                    src="{{ asset('storage/'.$item->postagem->imagens->first()->caminho_imagem) }}" 
                    class="foto-conteudo-visualizar">
                @endif
            </div>

            @elseif ($item->comentario)
            <div class="conteudo-modal">
                <strong>Comentário relacionado:</strong><br>
                {{ Str::limit($item->comentario->comentario, 150) }}
                @if ($item->comentario->image && $item->comentario->image->isNotEmpty())
                <img 
                    src="{{ asset('storage/'.$item->comentario->image->caminho_imagem) }}" 
                    class="foto-conteudo-visualizar">
                @endif
            </div>
            @endif

            <form action="{{ route('usuario.destroy', $usuarioAlvo->id) }}" method="POST" class="form-banimento">
                @csrf
                @method('DELETE')

                <h1 class="banir">Banir {{ $usuarioAlvo->user }}</h1>

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
                    <button type="button" class="btn btn-secondary" onclick="fecharModalBanimentoDenuncia('{{$item->id}}')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">Confirmar Banimento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    function abrirModalBanimentoDenuncia(id) {
        const modalBanimentoDenuncia = document.getElementById(`modal-banimento-denuncia-${id}`);
        if (modalBanimentoDenuncia) {
            modalBanimentoDenuncia.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function fecharModalBanimentoDenuncia(id) {
        const modalBanimentoDenuncia = document.getElementById(`modal-banimento-denuncia-${id}`);
        if (modalBanimentoDenuncia) {
            modalBanimentoDenuncia.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Fechar clicando fora
    window.addEventListener('click', function(event) {
        const modaisBanimentoDenuncia = document.querySelectorAll('.modal');
        modaisBanimentoDenuncia.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    });
</script>