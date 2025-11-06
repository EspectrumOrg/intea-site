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
                $outras = collect();
                if ($item->usuarioDenunciado) $outras = $item->usuarioDenunciado->denuncias->where('id', '!=', $item->id);
                if ($item->postagem) $outras = $item->postagem->denuncias->where('id', '!=', $item->id);
                if ($item->comentario) $outras = $item->comentario->denuncias->where('id', '!=', $item->id);
                @endphp

                <div class="outras-denuncias">
                    <h1>Denúncias relacionadas:</h1>
                    @if ($outras->count() > 0)
                    <ul>
                        @foreach ($outras as $outra)
                        <li>
                            <strong>Denunciante:</strong> {{ $outra->usuarioDenunciante->user}} -
                            <strong>Data:</strong> {{ \Carbon\Carbon::parse($outra->created_at)->format('d/m/Y H:i') }}<br>
                            <strong>Motivo:</strong> {{ $outra->motivo_denuncia }}<br>
                            @if ($outra->texto_denuncia)
                            <strong>Texto:</strong> {{ $outra->texto_denuncia }}<br>
                            @endif
                            <strong>Status:</strong> {{ $outra->status_denuncia == 1 ? 'Pendente' : 'Resolvida' }}
                        </li>
                        <hr>
                        @endforeach
                    </ul>
                    @else
                    <p>Nenhuma outra denúncia relacionada.</p>
                    @endif
                </div>
            </div>

            <!-- Rodapé -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalVisualizarDenuncia('{{$item->id}}')">Fechar</button>
            </div>
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