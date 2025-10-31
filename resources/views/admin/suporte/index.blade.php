@extends('admin.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/admin/suporte-gerenciamento.css') }}">

<div class="suporte-gerenciamento">
    <div class="suporte-inner">
        <div class="suporte-title">
            <span class="material-symbols-outlined">support_agent</span>
            <h1>Mensagens de Suporte</h1>
        </div>

        <!-- Filtros -->
        <form method="GET" action="{{ route('suporte.index') }}" class="filtro-form">
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
            <div class="card-body">
                <table class="table-suporte">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Assunto</th>
                            <th>Mensagem</th>
                            <th>Ações</th>
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
                            <td>
                                <button class="btn-visualizar" data-bs-toggle="modal" data-bs-target="#modalResponder{{ $contato->id }}">
                                    <span class="material-symbols-outlined">reply</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Responder -->
                        <div class="modal fade" id="modalResponder{{ $contato->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Responder - {{ $contato->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('suporte.resposta') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="destinatario" value="{{ $contato->email }}">
                                            <div class="mb-3">
                                                <label>Assunto</label>
                                                <input type="text" name="assunto" class="form-control" value="Re: {{ $contato->assunto }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Mensagem</label>
                                                <textarea name="mensagem" rows="5" class="form-control" required></textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="6">Nenhuma mensagem encontrada.</td></tr>
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
@endsection
