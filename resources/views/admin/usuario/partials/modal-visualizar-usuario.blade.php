<!-- Modal -->
<div id="modalVisualizarUsuario{{$item->id}}" class="modal hidden">
    <div class="modal-content">

        <!-- Cabeçalho -->
        <div class="modal-header">
            <h2 class="titulo">
                Informações de <p class="sublinhado red">{{ $item->user }}</p>
            </h2>

            <button class="modal-fechar" onclick="fecharModalVisualizarUsuario('{{$item->id}}')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="corpo-visualizar">

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="changeTab('{{$item->id}}','info')">Informações</button>
                <button class="tab" onclick="changeTab('{{$item->id}}','ban')">Ações</button>
            </div>

            <!-- Conteúdo -->
            <div class="tab-content">

                <!-- TAB — INFORMAÇÕES -->
                <div id="tab-info-{{$item->id}}" class="tab-pane active">

                    <div class="modal-body">

                        <!-- Foto e nome -->
                        <div class="text-center">
                            <img
                                src="{{ asset('storage/'.$item->foto ?? 'assets/images/logos/contas/user.png') }}"
                                class="foto-denunciado">

                            <div class="ao-lado-foto">
                                <h6>
                                    {{ $item->user }}
                                </h6>
                                <p>
                                    {{ $item->descricao ?? 'Sem descrição' }}
                                </p>

                                <div class="status-user">
                                    @switch($item->status_conta)
                                    @case(0)
                                    <p class="vermelho">
                                        <span class="material-symbols-outlined">
                                            no_accounts
                                        </span>Conta excluída
                                    </p>
                                    @break

                                    @case(1)
                                    <p class="verde">
                                        <span class="material-symbols-outlined">
                                            account_circle
                                        </span>Ativo
                                    </p>
                                    @break

                                    @case(2)
                                    <p class="vermelho">
                                        <span class="material-symbols-outlined">
                                            no_accounts
                                        </span>Banido
                                        @break

                                        @default Desconhecido
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes simples -->
                        <div class="detalhes-container">
                            <h1>Dados do usuário</h1>

                            <hr>

                            <div class="dados-usuario">
                                <div>
                                    <p class="titulo-info">Nome Completo:</p>
                                    <p>{{ $item->apelido }}</p>
                                </div>


                                <div>
                                    <p class="titulo-info">Email:</p>
                                    <p>{{ $item->email }}</p>
                                </div>

                                <div>

                                    <p class="titulo-info">Idade:</p>
                                    <p>{{ \Carbon\Carbon::parse($item->data_nascimento)->age }} anos</p>

                                </div>

                                <div>
                                    <p class="titulo-info">Tipo de Usuário:</p>
                                    <p>
                                        @if($item->tipo_usuario === 1) Admin
                                        @elseif($item->tipo_usuario === 2) Autista
                                        @elseif($item->tipo_usuario === 3) Comunidade
                                        @elseif($item->tipo_usuario === 4) Profissional de Saúde
                                        @else Responsável
                                        @endif
                                    </p>
                                </div>

                            </div>
                        </div>

                        <!-- CONTATOS DE SUPORTE (NOVO) -->
                        @php
                        $contatos = \App\Models\ContatoSuporte::where('email', $item->email)
                        ->where('status_contato', 'pendente')
                        ->orderBy('created_at','desc')
                        ->limit(5)
                        ->get();
                        @endphp

                        <div class="detalhes-container">
                            <h1>Contatos de Suporte</h1>

                            <hr>

                            @if($contatos->count() > 0)
                            @foreach($contatos as $c)
                            <div class="contato-item">
                                <p><strong>Assunto:</strong> {{ $c->assunto }}</p>
                                <p><strong>Mensagem:</strong> {{ $c->mensagem }}</p>
                                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($c->status_contato) }}</p>
                                <hr>
                            </div>
                            @endforeach
                            @else
                            <div class="sem-contatos">
                                <p>Não há contatos de suporte para exibir.</p>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- TAB — AÇÕES (BANIR OU DESBANIR) -->
                <div id="tab-ban-{{$item->id}}" class="tab-pane">

                    {{-- Se o usuário está ativo → mostrar BANIR --}}
                    @if($item->status_conta == 1)
                    <form action="{{ route('usuario.destroy', $item->id) }}" method="POST" class="form-banimento">
                        @csrf
                        @method('DELETE')

                        <h2 class="ban-title">Confirmar Banimento de Usuário</p>
                        </h2>

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

                        <div class="mb-3">
                            <label class="form-label">Motivo do banimento</label>
                            <textarea name="motivo" class="form-control" rows="3" required></textarea>
                        </div>

                        <div style="display:flex; justify-content:flex-end;">
                            <button type="submit" class="btn-banir">Confirmar Banimento</button>
                        </div>
                    </form>

                    {{-- Se o usuário está banido → mostrar DESBANIR --}}
                    @elseif($item->status_conta == 2)

                    <div class="content-desbanir">
                        <span class="material-symbols-outlined">
                            lock_open
                        </span>
                        <h3>Desbanir Usuário?</h3>
                        <p>
                            Você tem certeza que deseja desbanir o usuário
                            <span class="email-user">{{ $item->email }}</span>?
                            Isso vai restaurar o acesso para a aplicação imediatamente.
                        </p>

                    </div>

                    <form action="{{ route('usuario.desbanir', $item->id) }}" method="POST" class="form-desbanir">
                        @csrf
                        @method('PATCH')
                        </h2>

                        <div class="bottom-desbanir">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja desbanir?')" class="btn-desbanir-usuario">
                                Confirmar Desbanimento
                            </button>
                        </div>
                    </form>

                    @else
                    <p>Sem ações disponíveis.</p>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function abrirModalVisualizarUsuario(id) {
        const modal = document.getElementById(`modalVisualizarUsuario${id}`);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function fecharModalVisualizarUsuario(id) {
        const modal = document.getElementById(`modalVisualizarUsuario${id}`);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function changeTab(id, tab) {
        const t1 = document.getElementById(`tab-info-${id}`);
        const t2 = document.getElementById(`tab-ban-${id}`);

        t1.classList.toggle('active', tab === 'info');
        t2.classList.toggle('active', tab === 'ban');

        const modal = document.getElementById(`modalVisualizarUsuario${id}`);
        const btns = modal.querySelectorAll('.tab');

        btns.forEach(b => b.classList.remove('active'));

        if (tab === 'info') btns[0].classList.add('active');
        else btns[1].classList.add('active');
    }
</script>