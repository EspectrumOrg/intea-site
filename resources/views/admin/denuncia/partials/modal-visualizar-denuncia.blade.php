<!-- Modal -->
<div id="modalVisualizarDenuncia{{ $item->id }}" class="modal hidden">
    <div class="modal-content">

        <!-- Cabeçalho -->
        <div class="modal-header">
            <h2 class="titulo" id="modalDenunciaLabel{{ $item->id }}">
                @if ($item->usuarioDenunciado)
                Visulização de denúncia de usuário - {{ $item->usuarioDenunciado->user}}
                @elseif ($item->postagem)
                Visulização de denúncia de postagem - {{ $item->postagem->usuario->user}}
                @elseif ($item->comentario)
                Visulização de denúncia de comentário - {{ $item->comentario->usuario->user}}
                @endif
            </h2>
            <button class="modal-fechar" onclick="fecharModalVisualizarDenuncia('{{$item->id}}')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="corpo-visualizar">
            <!-- Corpo -->
            <div class="modal-body">

                @if ($item->usuarioDenunciado)
                <!-- Usuário -->
                <div class="text-center">
                    <img
                        src="{{ asset('storage/'.$item->usuarioDenunciado->foto ?? 'assets/images/logos/contas/user.png') }}"
                        class="foto-denunciado">
                    <h6>{{ $item->usuarioDenunciado->user }}</h6>
                    <p class="text-muted"> {{ $item->usuarioDenunciado->descricao ?? 'Sem descrição'}}</p>
                </div>

                @elseif ($item->postagem)
                <!-- Postagem -->
                <div class="conteudo-modal">
                    <h1>Conteúdo postagem denunciada:</h1>
                    <p>{{ $item->postagem->texto_postagem}}</p>

                    @if ($item->postagem->imagens->count() > 0)
                    <div class="text-center">
                        @foreach ($item->postagem->imagens as $image)
                        <img
                            src="{{ asset('storage/'.$image->caminho_imagem) }}"
                            class="foto-conteudo-visualizar"
                            alt="Imagem da postagem">
                        @endforeach
                    </div>
                    @endif
                </div>

                @elseif ($item->comentario)
                <!-- Comentário -->
                <div class="conteudo-modal">
                    <h1>Conteúdo comentário denunciada:</h1>
                    <p>{{ $item->comentario->comentario}}</p>

                    @if ($item->comentario->image)
                    <div class="text-center">
                        <img
                            src="{{ asset('storage/'.$item->comentario->image->caminho_imagem) }}"
                            class="foto-conteudo-visualizar"
                            alt="Imagem do comentário">
                    </div>
                    @endif
                </div>
                @endif

                <hr>

                <!-- Detalhes Denúncias -->
                <div class="detalhes-container">
                    <h1>Detalhes da denúncia</h1>
                    <p><strong>Denunciante:</strong> {{ $item->usuarioDenunciante->user}} / {{ $item->usuarioDenunciante->apelido }}</p>
                    <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Motivo:</strong> {{ $item->motivo_denuncia }}</p>
                    @if ($item->texto_denuncia)
                    <p><strong>Descrição:</strong> {{ $item->texto_denuncia}}</p>
                    @endif


                    <hr>

                    <!-- Outras Denúncias -->
                    @php
                    $denunciasCount = \App\Models\Denuncia::select('motivo_denuncia')
                    ->where(function($q) use ($item) {
                    if ($item->id_postagem) {
                    $q->where('id_postagem', $item->id_postagem);
                    } elseif ($item->id_comentario) {
                    $q->where('id_comentario', $item->id_comentario);
                    } elseif ($item->id_usuario_denunciado) {
                    $q->where('id_usuario_denunciado', $item->id_usuario_denunciado);
                    }
                    })
                    ->groupBy('motivo_denuncia')
                    ->selectRaw('motivo_denuncia, COUNT(*) as total')
                    ->pluck('total', 'motivo_denuncia');
                    @endphp

                    <div class="outras-denuncias">
                        <h1>Denúncias relacionadas:</h1>
                        @if ($denunciasCount->count() > 0)
                        <ul>
                            <li>Ódio ou Discriminação: {{ $denunciasCount['odio'] ?? 0 }}</li>
                            <li>Abuso ou Assédio: {{ $denunciasCount['abuso_e_assedio'] ?? 0 }}</li>
                            <li>Ameaças ou Incitação à Violência: {{ $denunciasCount['discurso_de_odio'] ?? 0 }}</li>
                            <li>Segurança Infantil: {{ $denunciasCount['seguranca_infantil'] ?? 0 }}</li>
                            <li>Privacidade: {{ $denunciasCount['privacidade'] ?? 0 }}</li>
                            <li>Atividades Ilegais: {{ $denunciasCount['comportamentos_ilegais_e_regulamentados'] ?? 0 }}</li>
                            <li>Spam ou Engajamento Artificial: {{ $denunciasCount['spam'] ?? 0 }}</li>
                            <li>Risco à Integridade Pessoal: {{ $denunciasCount['suicidio_ou_automutilacao'] ?? 0 }}</li>
                            <li>Falsa Identidade: {{ $denunciasCount['personificacao'] ?? 0 }}</li>
                            <li>Grupos Extremistas: {{ $denunciasCount['entidades_violentas_e_odiosas'] ?? 0 }}</li>
                        </ul>
                        @else
                        <p>Nenhuma outra denúncia relacionada.</p>
                        @endif
                    </div>
                </div>
            </div>

            @php
            $usuarioAlvo = $item->usuarioDenunciado
            ?? $item->postagem->usuario
            ?? $item->comentario->usuario;
            @endphp

            <form action="{{ route('usuario.destroy', $usuarioAlvo->id) }}" method="POST" class="form-banimento">
                @csrf
                @method('DELETE')

                <h1 class="banir">Banir {{ $usuarioAlvo->user }}</h1>

                <!-- Campo infração -->
                <label class="form-label">Infração</label>
                <select class="form-select" id="infracao-{{ $item->id }}" name="infracao" required>
                    <option value="">Tipo</option>
                    <option value="odio">Ódio ou Discriminação</option>
                    <option value="abuso_e_assedio">Abuso ou Assédio</option>
                    <option value="discurso_de_odio">Ameaças ou Incitação à Violência</option>
                    <option value="seguranca_infantil">Segurança Infantil</option>
                    <option value="privacidade">Privacidade</option>
                    <option value="comportamentos_ilegais_e_regulamentados">Atividades Ilegais</option>
                    <option value="spam">Spam ou Engajamento Artificial</option>
                    <option value="suicidio_ou_automutilacao">Risco à Integridade Pessoal</option>
                    <option value="personificacao">Falsa Identidade</option>
                    <option value="entidades_violentas_e_odiosas">Grupos Extremistas</option>
                </select>


                <!-- Campos ocultos: caso a denúncia venha de postagem ou comentário -->
                @if ($item->postagem)
                <input type="hidden" name="id_postagem" value="{{ $item->postagem->id }}">
                @endif
                @if ($item->comentario)
                <input type="hidden" name="id_comentario" value="{{ $item->comentario->id }}">
                @endif

                <div class="text-end">
                    <button type="submit" class="btn btn-danger">Confirmar Banimento</button>
                </div>
            </form>
        </div>

        <!-- Rodapé -->
        <div class="modal-footer">

        </div>

    </div>
</div>

<script>
    function abrirModalVisualizarDenuncia(id) {
        const modalVisualizarDenuncia = document.getElementById(`modalVisualizarDenuncia${id}`);
        if (modalVisualizarDenuncia) {
            modalVisualizarDenuncia.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function fecharModalVisualizarDenuncia(id) {
        const modalVisualizarDenuncia = document.getElementById(`modalVisualizarDenuncia${id}`);
        if (modalVisualizarDenuncia) {
            modalVisualizarDenuncia.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Fechar clicando fora
    window.addEventListener('click', function(event) {
        const modaisVisualizarDenuncia = document.querySelectorAll('.modal');
        modaisVisualizarDenuncia.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    });
</script>