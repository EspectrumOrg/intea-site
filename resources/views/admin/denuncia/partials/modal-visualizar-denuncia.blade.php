<!-- Modal -->
<div id="modalVisualizarDenuncia{{ $item->id }}" class="modal hidden">
    <div class="modal-content">

        <!-- Cabeçalho -->
        <div class="modal-header">
            <h2 class="titulo" id="modalDenunciaLabel{{ $item->id }}">
                @if ($item->usuarioDenunciado)
                Denúncia de <p class="sublinhado">usuário</p>
                <p class="sublinhado red">{{ $item->usuarioDenunciado->user}}</p> por <p class="sublinhado blue">{{ $item->usuarioDenunciante->user}}</p>
                @elseif ($item->postagem)
                Denúncia de <p class="sublinhado">postagem</p> de <p class="sublinhado red">{{ $item->postagem->usuario->user}}</p> por <p class="sublinhado blue">{{ $item->usuarioDenunciante->user}}</p>
                @elseif ($item->comentario)
                Denúncia de <p class="sublinhado">comentário</p> de <p class="sublinhado red">{{ $item->comentario->usuario->user}}</p> por <p class="sublinhado blue">{{ $item->usuarioDenunciante->user}}</p>
                @endif
            </h2>
            <button class="modal-fechar" onclick="fecharModalVisualizarDenuncia('{{$item->id}}')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="corpo-visualizar">

            <!-- Opções -->
            <div class="tabs">
                <button class="tab active" onclick="changeTab('{{ $item->id }}', 'info')">Informações</button>
                <button class="tab" onclick="changeTab('{{ $item->id }}', 'ban')">Banimento</button>
            </div>

            <!-- Conteúdo -->
            <div class="tab-content">

                <!-- INFORMAÇÕES -->
                <div id="tab-info-{{ $item->id }}" class="tab-pane active">

                    <div class="modal-body">

                        @if ($item->usuarioDenunciado)
                        <!-- Usuário -->
                        <div class="text-center">
                            <img
                                src="{{ asset('storage/'.$item->usuarioDenunciado->foto ?? 'assets/images/logos/contas/user.png') }}"
                                class="foto-denunciado">
                            <h6 style="font-size: 18px; color: black; font-weight: 600;">{{ $item->usuarioDenunciado->user }}</h6>
                            <p class="text-muted" style="padding: 0 1rem;"> {{ $item->usuarioDenunciado->descricao ?? 'Sem descrição'}}</p>
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
                            @php
                            $motivos = [
                            'odio' => ['Ódio ou Discriminação'],
                            'abuso_e_assedio' => ['Abuso ou Assédio'],
                            'discurso_de_odio' => ['Ameaças ou Incitação à Violência'],
                            'seguranca_infantil' => ['Segurança Infantil'],
                            'privacidade' => ['Privacidade'],
                            'comportamentos_ilegais_e_regulamentados' => ['Atividades Ilegais'],
                            'spam' => ['Spam ou Engajamento Artificial'],
                            'suicidio_ou_automutilacao' => ['Risco à Integridade Pessoal'],
                            'personificacao' => ['Falsa Identidade'],
                            'entidades_violentas_e_odiosas' => ['Grupos Extremistas'],
                            ];
                            @endphp
                            <p><strong>Motivo:</strong>{{ $motivos[$item->motivo_denuncia][0] ?? 'Motivo desconhecido' }}</p>

                            <hr>

                            <!-- Outras Denúncias -->
                            @php
                            $motivos = [
                            'odio' => ['Ódio ou Discriminação', 'red-text'],
                            'abuso_e_assedio' => ['Abuso ou Assédio', 'orange-text'],
                            'discurso_de_odio' => ['Ameaças ou Incitação à Violência', 'black-text'],
                            'seguranca_infantil' => ['Segurança Infantil', 'purple-text'],
                            'privacidade' => ['Privacidade', 'blue-text'],
                            'comportamentos_ilegais_e_regulamentados' => ['Atividades Ilegais', 'brown-text'],
                            'spam' => ['Spam ou Engajamento Artificial', 'green-text'],
                            'suicidio_ou_automutilacao' => ['Risco à Integridade Pessoal', 'pink-text'],
                            'personificacao' => ['Falsa Identidade', 'teal-text'],
                            'entidades_violentas_e_odiosas' => ['Grupos Extremistas', 'dark-red-text'],
                            ];

                            $denunciasCount = \App\Models\Denuncia::select('motivo_denuncia')
                            ->where(function ($q) use ($item) {
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

                                @php
                                // Filtra somente motivos que têm contagem maior que 0
                                $motivosComValor = $denunciasCount->filter(fn($total) => $total > 0);
                                @endphp

                                @if ($motivosComValor->count() > 0)
                                <ul>
                                    @foreach ($motivosComValor as $motivoKey => $total)
                                    @if(isset($motivos[$motivoKey]))
                                    <li>
                                        {{ $motivos[$motivoKey][0] }}: {{ $total }}
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                @else
                                <p>Nenhuma outra denúncia relacionada.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <!-- BANIMENTO -->
                <div id="tab-ban-{{ $item->id }}" class="tab-pane">

                    @php
                    $usuarioAlvo = $item->usuarioDenunciado
                    ?? $item->postagem->usuario
                    ?? $item->comentario->usuario;
                    @endphp

                    <form action="{{ route('usuario.destroy', $usuarioAlvo->id) }}" method="POST" class="form-banimento">
                        @csrf
                        @method('DELETE')

                        <h2 class="ban-title">Banir <p class="sublinhado">{{ $usuarioAlvo->user }}</p>
                        </h2>

                        <div class="dados-usuario-banimento">

                        </div>

                        <label class="form-label">Infração</label>
                        <select name="infracao" class="form-select" required>
                            <option value="">Selecione…</option>
                            <option value="odio">Ódio ou Discriminação</option>
                            <option value="abuso_e_assedio">Abuso ou Assédio</option>
                            <option value="discurso_de_odio">Ameaças ou Violência</option>
                            <option value="seguranca_infantil">Segurança Infantil</option>
                            <option value="privacidade">Privacidade</option>
                            <option value="comportamentos_ilegais_e_regulamentados">Atividades Ilegais</option>
                            <option value="spam">Spam</option>
                            <option value="suicidio_ou_automutilacao">Risco à Integridade Pessoal</option>
                            <option value="personificacao">Falsa Identidade</option>
                            <option value="entidades_violentas_e_odiosas">Grupos Extremistas</option>
                        </select>


                        <!-- Campo motivo -->
                        <div class="mb-3">
                            <label for="motivo{{ $item->id }}" class="form-label">Motivo do banimento</label>
                            <textarea 
                                name="motivo" 
                                id="motivo{{ $item->id }}" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Descreva o motivo do banimento..." required></textarea>
                        </div>

                        @if($item->postagem)
                        <input type="hidden" name="id_postagem" value="{{ $item->postagem->id }}">
                        @endif

                        @if($item->comentario)
                        <input type="hidden" name="id_comentario" value="{{ $item->comentario->id }}">
                        @endif

                        <div style="display: flex; width: 100%; justify-content:flex-end">
                            <button type="submit" class="btn-banir">Confirmar banimento</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Rodapé -->
            <div class="modal-footer">
                <form action="{{ route('denuncia.resolve', $item->id) }}" method="post">
                    @csrf
                    @method("put")
                    <button type="submit" onclick="return confirm('Você tem certeza que deseja marcar a denúncia como resolvida?');" class="btn-resolver">
                        Marcar como resolvida
                    </button>
                </form>
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

        //Opção
        function changeTab(id, tab) {
            const infoTab = document.getElementById(`tab-info-${id}`);
            const banTab = document.getElementById(`tab-ban-${id}`);

            // Alternando visibilidade
            infoTab.classList.toggle('active', tab === 'info');
            banTab.classList.toggle('active', tab === 'ban');

            // Botões
            const buttons = document.querySelectorAll(`#modalVisualizarDenuncia${id} .tab`);
            buttons.forEach(btn => btn.classList.remove('active'));

            const clicked = document.querySelector(`#modalVisualizarDenuncia${id} .tab:nth-child(${tab === 'info' ? 1 : 2})`);
            clicked.classList.add('active');
        }
    </script>