@extends('admin.template.layout')

@section('main')
<div class="usuario-gerenciamento">

    <h1>Denúncias</h1>

    <!-- Formulário de busca e filtros -->
    <form method="GET" action="{{ route('denuncia.index') }}" class="filtro-form">
        <input type="text" name="search_denuncia" placeholder="Alguma ideia aqui?" value="{{ request('search_denuncia') }}">

        <select name="motivo_denuncia">
            <option value="">Todos os motivos</option>
            <option value="spam" {{ request('motivo_denuncia') == "spam" ? 'selected' : '' }}>Spam</option>
            <option value="falsidade" {{ request('motivo_denuncia') == "falsidade" ? 'selected' : '' }}>Desinformação</option>
            <option value="conteudo_explicito" {{ request('motivo_denuncia') == "conteudo_explicito" ? 'selected' : '' }}>Conteúdo Explícito</option>
            <option value="discurso_de_odio" {{ request('motivo_denuncia') == "discurso_de_odio" ? 'selected' : '' }}>Discurso de Ódio</option>
        </select>

        <select name="ordem">
            <option value="desc" {{ request('ordem') == 'desc' ? 'selected' : '' }}>Mais recente</option>
            <option value="asc" {{ request('ordem') == 'asc' ? 'selected' : '' }}>Mais antigo</option>
        </select>

        <button type="submit" class="btn-filtrar">Filtrar</button>
    </form>

    <div class="card">
        <div class="card-header">
            <h2>Denuncias</h2>
            <a href="#" class="btn-novo">Novo Admin</a>
        </div>

        <div class="card-body">
            <table class="table-usuarios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Feito por</th>
                        <th>User</th>
                        <th>Usuario Denunciado</th>
                        <th>motivo_denuncia</th>
                        <th>Texto</th>
                        <th>Data de Denuncia</th>
                        <th>Status Conta</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($denuncias as $item)
                    <td>{{ $item->usuario->id }}</td>
                    <td>{{ $item->usuario->nome }}</td>
                    <td>{{ $item->usuario->user }}</td>
                    <td>{{ $item->postagem->usuario->user }}</td>
                    <td>{{ $item->motivo_denuncia }}</td>
                    <td>{{ $item->texto_denuncia }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $item->usuario->status_conta }}</td>
                    <td>
                        <!-- Usuário ativo → Mostrar botão de banir -->
                        <form action="{{ route('denuncia.destroy', $item->usuario->id) }}" method="post">
                            @csrf
                            @method("delete")
                            <button type="submit" onclick="return confirm('Você tem certeza que deseja banir esse usuário?');" class="btn-excluir">
                                Banir
                            </button>
                        </form>
                    </td>
                    @empty
                    <tr>
                        <td colspan="8">Nenhuma denúncia encontrada</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>

            <div class="paginacao">
                {{ $denuncias->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection