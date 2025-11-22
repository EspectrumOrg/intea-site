@extends('admin.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/admin/suporte-gerenciamento.css') }}">
<!-- CSS do Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-bdr1sENtJTR1yM0Ff9kxC4jo0B0p8jlzJQYc8jowTgk9kzxRzYfT5mFjZbFzFSJv" crossorigin="anonymous">
<!-- JS do Bootstrap (necessário para modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-2KJxw7Rf9L7/n8+ITbPmxv4R05gVnpRb2VHZgZ8fFqzS9mQ9OH+TGLZxUq5K7vN4" crossorigin="anonymous"></script>


<div class="suporte-gerenciamento">

    <div class="suporte-inner">

        <div class="suporte-title">
            <span class="material-symbols-outlined">support_agent</span>
            <h1>Mensagens de Suporte</h1>
        </div>

        <!-- Filtros -->
        <form method="GET" action="{{ route('contato.index') }}" class="filtro-form">
            <input type="text" name="search" placeholder="Buscar por email" value="{{ request('search') }}">

            <select name="assunto">
                <option value="">Todos os assuntos</option>
                <option value="Recomende mudanças no sistema" {{ request('assunto') == 'Recomende mudanças no sistema' ? 'selected' : '' }}>Recomende mudanças no sistema</option>
                <option value="Consulta por vagas de emprego" {{ request('assunto') == 'Consulta por vagas de emprego' ? 'selected' : '' }}>Consulta por vagas de emprego</option>
                <option value="Tire sua dúvida" {{ request('assunto') == 'Tire sua dúvida' ? 'selected' : '' }}>Tire sua dúvida</option>
                <option value="Reconsideração de banimento" {{ request('assunto') == 'Reconsideração de banimento' ? 'selected' : '' }}>Reconsideração de banimento</option>
                <option value="Informação de bug/erro encontrado" {{ request('assunto') == 'Informação de bug/erro encontrado' ? 'selected' : '' }}>Informação de bug/erro encontrado</option>
                <option value="Comunicações comerciais/governamentais" {{ request('assunto') == 'Comunicações comerciais/governamentais' ? 'selected' : '' }}>Comunicações comerciais/governamentais</option>
            </select>

            <button type="submit" class="btn-filtrar">Filtrar</button>
        </form>

        <!-- Tabela -->
        <div class="card">
            <div class="card-header">
                <h2>Contatos Feitos</h2>
            </div>

            <div class="card-body">
                <table class="table-suporte">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Assunto</th>
                            <th>Mensagem</th>
                            <th>Feito</th>
                            <th>Responder</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contatos as $contato)
                        <tr>
                            <td>{{ $contato->id }}</td>
                            <td>{{ $contato->name }}</td>
                            <td>{{ $contato->email }}</td>
                            <td>{{ $contato->assunto }}</td>
                            <td>{{ Str::limit($contato->mensagem, 100) }}</td>
                            <td>{{ $contato->created_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn-visualizar" onclick="abrirModalRespostaSuporte('{{$contato->id}}')">
                                    <span class="material-symbols-outlined">reply</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Responder -->
                        <div id="modal-resposta-{{ $contato->id }}" class="modal-overlay-resposta">
                            <div class="modal-resposta">
                                <div class="modal-resposta-header">
                                    <h5>Responder - {{ $contato->name }}</h5>
                                    <button type="button" class="close-modal-resposta" onclick="fecharModalRespostaSuporte('{{$contato->id}}')">&times;</button>
                                </div>
                                <div class="modal-resposta-body">
                                    <form action="{{ route('contato.resposta') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="data_contato" value="{{$contato->created_at}}">
                                        <input type="hidden" name="id_contato" value="{{$contato->id}}">

                                        <div class="form-group">
                                            <label>Para:</label>
                                            <input type="text" name="destinatario" value="{{$contato->email}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Assunto:</label>
                                            <input type="text" name="assunto" value="{{$contato->assunto}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Mensagem:</label>
                                            <textarea name="mensagem" rows="3" placeholder="Resposta a mensagem" readonly>{{$contato->mensagem}}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Resposta:</label>
                                            <textarea name="resposta" rows="5" placeholder="Resposta a mensagem" required></textarea>
                                        </div>
                                        <div class="botoes-modal">
                                            <button type="button" class="btn-cancelar" onclick="fecharModalRespostaSuporte('{{$contato->id}}')">Cancelar</button>
                                            <button type="submit" class="btn-enviar">Enviar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr class="nada-aqui">
                            <td colspan="10">Nenhuma denúncia encontrada</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="paginacao">
                    {{ $contatos->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalRespostaSuporte(id) {
        const modal = document.getElementById(`modal-resposta-${id}`);
        if (modal) modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function fecharModalRespostaSuporte(id) {
        const modal = document.getElementById(`modal-resposta-${id}`);
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('click', (e) => {
        document.querySelectorAll('.modal-overlay-resposta').forEach(modal => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });
</script>
@endsection