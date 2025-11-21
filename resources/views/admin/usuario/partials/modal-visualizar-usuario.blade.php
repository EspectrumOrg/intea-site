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

                            <h6 style="font-size: 18px; color:black; font-weight:600;">
                                {{ $item->user }}
                            </h6>

                            <p class="text-muted" style="padding: 0 1rem;">
                                {{ $item->descricao ?? 'Sem descrição' }}
                            </p>
                        </div>

                        <hr>

                        <!-- Detalhes simples -->
                        <div class="detalhes-container">
                            <h1>Dados do usuário</h1>

                            <p><strong>Nome:</strong> {{ $item->apelido }}</p>
                            <p><strong>Email:</strong> {{ $item->email }}</p>
                            <p><strong>Idade:</strong> {{ \Carbon\Carbon::parse($item->data_nascimento)->age }} anos</p>
                            <p><strong>Tipo:</strong>
                                @if($item->tipo_usuario === 1) Admin
                                @elseif($item->tipo_usuario === 2) Autista
                                @elseif($item->tipo_usuario === 3) Comunidade
                                @elseif($item->tipo_usuario === 4) Profissional de Saúde
                                @else Responsável
                                @endif
                            </p>

                            <p><strong>Status:</strong>
                                @switch($item->status_conta)
                                @case(0) Conta excluída @break
                                @case(1) Ativo @break
                                @case(2) Banido @break
                                @default Desconhecido
                                @endswitch
                            </p>
                        </div>

                        <hr>

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
                            <p>Nenhum contato de suporte pendente.</p>
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

                        <h2 class="ban-title">Banir <p class="sublinhado">{{ $item->user }}</p>
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
                            <button type="submit" class="btn-banir">Confirmar banimento</button>
                        </div>
                    </form>

                    {{-- Se o usuário está banido → mostrar DESBANIR --}}
                    @elseif($item->status_conta == 2)

                    <form action="{{ route('usuario.desbanir', $item->id) }}" method="POST" class="form-desbanir">
                        @csrf
                        @method('PATCH')

                        <h2 class="ban-title">Desbanir <p class="verde">{{ $item->user }}</p>
                        </h2>

                        <div class="bottom-desbanir">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja desbanir?')" class="btn-desbanir-usuario">
                                Desbanir usuário
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