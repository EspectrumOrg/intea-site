@extends('admin.template.layout')

@section('main')
<!-- css geral -->
<link rel="stylesheet" href="{{ asset('assets/css/admin/denuncia-gerenciamento.css') }}">
<!-- CSS do Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-bdr1sENtJTR1yM0Ff9kxC4jo0B0p8jlzJQYc8jowTgk9kzxRzYfT5mFjZbFzFSJv" crossorigin="anonymous">
<!-- JS do Bootstrap (necessário para modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-2KJxw7Rf9L7/n8+ITbPmxv4R05gVnpRb2VHZgZ8fFqzS9mQ9OH+TGLZxUq5K7vN4" crossorigin="anonymous"></script>


<div class="denuncia-gerenciamento">

    <div class="denuncia-inner">

        <div class="denuncia-title">
            <span class="material-symbols-outlined">flag_2</span>
            <h1>Denúncias</h1>
        </div>

        <!-- Formulário de busca e filtros -->
        <form method="GET" action="{{ route('denuncia.index') }}" class="filtro-form">
            <!-- <input type="text" name="search_denuncia" placeholder="Alguma ideia aqui?" value="{{ request('search_denuncia') }}"> -->

            <select name="motivo_denuncia">
                <option value="">Todos os motivos</option>
                <option value="spam" {{ request('motivo_denuncia') == "spam" ? 'selected' : '' }}>Spam</option>
                <option value="falsidade" {{ request('motivo_denuncia') == "falsidade" ? 'selected' : '' }}>Desinformação</option>
                <option value="conteudo_explicito" {{ request('motivo_denuncia') == "conteudo_explicito" ? 'selected' : '' }}>Conteúdo Explícito</option>
                <option value="discurso_de_odio" {{ request('motivo_denuncia') == "discurso_de_odio" ? 'selected' : '' }}>Discurso de Ódio</option>
            </select>

            <select name="status_denuncia">
                <option value="1" style="color: green;" {{ request('status_denuncia') == '1' ? 'selected' : '' }}>Pendente</option>
                <option value="0" style="color: red;" {{ request('status_denuncia') == '0' ? 'selected' : '' }}>Resolvida</option>
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
            </div>

            <div class="card-body">
                <table class="table-denuncias">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Feito por</th>
                            <th>User</th>
                            <th>Usuario denunciado</th>
                            <th>Motivo</th>
                            <th>Data de denuncia</th>
                            <th>Status conta</th>
                            <th>Status denúncia</th>
                            <th>Resolvido</th>
                            <th>Banir</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($denuncias as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->usuarioDenunciante->apelido }}</td>
                            <td>{{ $item->usuarioDenunciante->user }}</td>
                            <td>{{ $item->postagem->usuarioDenunciado->user ?? $item->postagem->usuario->user ?? $item->comentario->usuario->user}}</td>
                            <td>
                                @if( $item->motivo_denuncia == 'spam')
                                <p class="motivo_denuncia red-text">Spam</p>
                                @elseif( $item->motivo_denuncia == 'desinformacao')
                                <p class="motivo_denuncia orange-text">Desinformação</p>
                                @elseif( $item->motivo_denuncia == 'conteudo_explicito')
                                <p class="motivo_denuncia purple-text">Conteúdo explícito</p>
                                @elseif( $item->motivo_denuncia == 'discurso_de_odio')
                                <p class="motivo_denuncia black-text">Discurso de ódio</p>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                            <td>
                                @switch($item->usuarioDenunciado->status_conta ?? $item->postagem->usuario->status_conta ?? $item->comentario->usuario->status_conta)
                                @case(0)
                                Banido
                                @break

                                @case(1)
                                Ativo
                                @break

                                @case(2)
                                Conta excluída
                                @break

                                @default
                                Desconhecido
                                @endswitch
                            </td>
                            <td>
                                @switch($item->status_denuncia)
                                @case('resolvida')
                                Resolvida
                                @break

                                @case('pendente')
                                Pendente
                                @break

                                @default
                                Desconhecido
                                @endswitch
                            </td>
                            <td class="button-open-data acoes-denuncia">
                                <div class="td-acoes">
                                    <!-- Usuário ativo → Desabilitar denúncia -->
                                    <form action="{{ route('denuncia.resolve', $item->id) }}" method="post">
                                        @csrf
                                        @method("put")
                                        <button type="submit" onclick="return confirm('Você tem certeza que deseja marcar a denúncia como resolvida?');" class="btn-desabilitar">
                                            <span class="material-symbols-outlined">check</span>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="button-open-data acoes-denuncia">
                                <!-- Banir usuário, postagem → usuário, comentário → usuário -->
                                <form action="{{ route('usuario.destroy', $item->usuarioDenunciado->id ?? $item->postagem->usuario->id ?? $item->comentario->usuario->id) }}" method="post">
                                    @csrf
                                    @method("delete")
                                    <button type="button" class="btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoDenuncia('{{$item->id}}')">
                                        <span class="material-symbols-outlined">person_off</span>
                                    </button>
                                </form>
                                <!-- Inclui o modal banimento -->
                                @include('admin.denuncia.partials.modal-banimento', ['item' => $item])

                            <td class="button-open-data acoes-denuncia">

                                <!-- Visulalizar -->
                                <button type="button" class="btn-visualizar" onclick="abrirModalVisualizarDenuncia('{{$item->id}}')">
                                    <span class="material-symbols-outlined">open_in_full</span>
                                </button>
                                <!-- Inclui o modal visualizar denúncia -->
                                @include('admin.denuncia.partials.modal-visualizar-denuncia', ['item' => $item])
                            </td>
                        </tr>

                        @empty
                        <tr class="nada-aqui">
                            <td colspan="10">Nenhuma denúncia encontrada</td>
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
</div>
@endsection