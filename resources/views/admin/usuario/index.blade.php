@extends('admin.template.layout')

@section('main')
<!-- css geral -->
<link rel="stylesheet" href="{{ asset('assets/css/admin/usuario-gerenciamento.css') }}">

<div class="usuario-gerenciamento">

    <div class="usuario-inner">
        <div class="usuario-title">
            <span class="material-symbols-outlined">groups</span>
            <h1>Usuários</h1>
        </div>

        <!-- Formulário de busca e filtros -->
        <form method="GET" action="{{ route('usuario.index') }}" class="filtro-form">
            <input type="text" name="search" placeholder="Buscar por nome, user ou email" value="{{ request('search') }}">

            <select name="tipo_usuario">
                <option value="">Todos os tipos</option>
                <option value="1" {{ request('tipo_usuario') == 1 ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ request('tipo_usuario') == 2 ? 'selected' : '' }}>Autistas</option>
                <option value="3" {{ request('tipo_usuario') == 3 ? 'selected' : '' }}>Comunidade</option>
                <option value="5" {{ request('tipo_usuario') == 5 ? 'selected' : '' }}>Responsáveis</option>
            </select>

            <select name="status_conta">
                <option value="" {{ request('status_conta') == null ? 'selected' : '' }}>Todos status</option>
                <option value="1" {{ request('status_conta') == '1' ? 'selected' : '' }}>Ativa</option>
                <option value="2" {{ request('status_conta') == '0' ? 'selected' : '' }}>Banida</option>
            </select>


            <select name="ordem">
                <option value="desc" {{ request('ordem') == 'desc' ? 'selected' : '' }}>Mais recente</option>
                <option value="asc" {{ request('ordem') == 'asc' ? 'selected' : '' }}>Mais antigo</option>
            </select>

            <button type="submit" class="btn-filtrar">Filtrar</button>
        </form>

        <div class="card">
            <div class="card-header">
                <h2>Usuários Cadastrados</h2>
                <a href="{{ route('admin.create') }}" class="btn-novo">Novo Admin</a>
            </div>

            <div class="card-body">
                <table class="table-usuarios">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Idade</th>
                            <th>Tipo Usuário</th>
                            <th>Data de Login</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuario as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->apelido }}</td>
                            <td>{{ $item->user }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->data_nascimento)->age }} anos</td>
                            <td>
                                @if($item->tipo_usuario === 1)
                                Admin
                                @elseif($item->tipo_usuario === 2)
                                Autista
                                @elseif($item->tipo_usuario === 3)
                                Comunidade
                                @elseif($item->tipo_usuario === 4)
                                Profissional de Saúde
                                @else
                                Responsável
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                            <td>
                                @switch($item->status_conta)
                                @case(0)
                                Conta excluída
                                @break

                                @case(1)
                                Ativo
                                @break

                                @case(2)
                                Banido
                                @break

                                @default
                                Desconhecido
                                @endswitch
                            </td>
                            <td>
                                @if($item->status_conta == 1)
                                <!-- Usuário ativo → Mostrar botão de banir -->
                                <button type="button" class="btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoUsuarioEspecifico('{{ $item->id }}')">
                                    <span class="material-symbols-outlined">person_off</span>
                                    Banir
                                </button>

                                <!-- Inclui o modal -->
                                @include('layouts.partials.modal-banimento', ['usuario' => $item])

                                @elseif($item->status_conta == 2)
                                <!-- Usuário banido → Mostrar botão de desbanir -->
                                <form action="{{ route('usuario.desbanir', $item->id) }}" method="post" class="form-desbanir">
                                    @csrf
                                    @method("patch")
                                    <button type="submit" onclick="return confirm('Você tem certeza que deseja desbanir esse usuário?');" class="btn-desbanir">
                                        <span class="material-symbols-outlined">person_add</span>
                                        Desbanir
                                    </button>
                                </form>
                                @else
                                <h1>sem ações possíveis</h1>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;">Nenhum usuário encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="paginacao">
                    {{ $usuario->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection