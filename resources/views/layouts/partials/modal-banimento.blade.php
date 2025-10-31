<!-- COMPONENTE DE MODAL DE BANIMENTO -->
<div class="modal fade" id="modalBanirUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalBanirUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Cabeçalho -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalBanirUsuarioLabel{{ $usuario->id }}">
                    Banir Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo -->
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/'.$usuario->foto ?? 'assets/images/logos/contas/user.png') }}"
                         class="rounded-circle" width="100" height="100" alt="Foto do usuário">
                    <h5 class="mt-2">{{ $usuario->user }}</h5>
                    <p class="text-muted">{{ $usuario->apelido ?? 'Sem apelido' }}</p>
                </div>

                <form action="{{ route('usuario.destroy', $usuario->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Campo infração -->
                    <label class="form-label">Infração</label>
                    <select class="form-select" id="infracao-{{ $usuario->id }}" name="infracao" required>
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
                        <label for="motivo{{ $usuario->id }}" class="form-label">Motivo do banimento</label>
                        <textarea name="motivo" id="motivo{{ $usuario->id }}" class="form-control" rows="3"
                                  placeholder="Descreva o motivo do banimento..." required></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Banimento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
