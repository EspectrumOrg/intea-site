@extends('admin.template.layout')

@section('main')
<div class="usuario-gerenciamento">

    <h1>Gerenciamento de Usuários</h1>

    <!-- Formulário de busca e filtros -->
    <form method="GET" action="{{ route('usuario.index') }}" class="filtro-form">
        <input type="text" name="search" placeholder="Buscar por nome, user ou email" value="{{ request('search') }}">

        <select name="tipo_usuario">
            <option value="">Todos os tipos</option>
            <option value="1" {{ request('tipo_usuario') == 1 ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ request('tipo_usuario') == 2 ? 'selected' : '' }}>Autistas</option>
            <option value="3" {{ request('tipo_usuario') == 3 ? 'selected' : '' }}>Comunidade</option>
            <option value="4" {{ request('tipo_usuario') == 4 ? 'selected' : '' }}>Profissionais de Saúde</option>
            <option value="5" {{ request('tipo_usuario') == 5 ? 'selected' : '' }}>Responsáveis</option>
        </select>

        <select name="status_conta">
            <option value="">Todos status</option>
            <option value="1" {{ request('status_conta') == 1 ? 'selected' : '' }}>Ativa</option>
            <option value="0" {{ request('status_conta') == 0 ? 'selected' : '' }}>Banida</option>
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
            <a href="#" class="btn-novo">Novo Admin</a>
        </div>

        <div class="card-body">
            <table class="table-usuarios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Data Nascimento</th>
                        <th>Tipo Usuário</th>
                        <th>Data de Login</th>
                        <th>Status Conta</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuario as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->nome }}</td>
                        <td>{{ $item->user }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->data_nascimento)->format('d/m/Y') }}</td>
                        <td>{{ $item->tipo_usuario }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $item->status_conta }}</td>
                        <td>
                            @if($item->status_conta == 1)
                            <!-- Usuário ativo → Mostrar botão de banir -->
                            <form action="{{ route('usuario.destroy', $item->id) }}" method="post" class="form-excluir">
                                @csrf
                                @method("delete")
                                <button type="submit" onclick="return confirm('Você tem certeza que deseja banir esse usuário?');" class="btn-excluir">
                                    Banir
                                </button>
                            </form>
                            @else
                            <!-- Usuário banido → Mostrar botão de desbanir -->
                            <form action="{{ route('usuario.desbanir', $item->id) }}" method="post" class="form-desbanir">
                                @csrf
                                @method("patch")
                                <button type="submit" onclick="return confirm('Você tem certeza que deseja desbanir esse usuário?');" class="btn-desbanir">
                                    Desbanir
                                </button>
                            </form>
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
@endsection