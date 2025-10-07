@extends('admin.template.layout')

@section('main')
<div class="usuario-gerenciamento">

    <h1>Denúncias</h1>

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
            <a href="#" class="btn-novo">Novo Admin</a>
        </div>

        <div class="card-body">
            <table class="table-usuarios">
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
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($denuncias as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->usuario->nome }}</td>
                        <td>{{ $item->usuario->user }}</td>
                        <td>{{ $item->postagem->usuario->user }}</td>
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
                            @switch($item->usuario->status_conta)
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
                            @case('0')
                            Resolvida
                            @break

                            @case('1')
                            Pendente
                            @break

                            @default
                            Desconhecido
                            @endswitch
                        </td>
                        <td class="button-open-data">
                            <!-- Botão para abrir o modal -->
                            <button type="button" class="btn-visualizar" data-bs-toggle="modal" data-bs-target="#modalPostagem{{ $item->id }}">
                                <img class="img-icons" src="{{ asset('assets/images/logos/symbols/open-folder.png') }}" alt="expandir">
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="modalPostagem{{ $item->id }}" tabindex="-1" aria-labelledby="modalPostagemLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <!-- Cabeçalho -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalPostagemLabel{{ $item->id }}">
                                                Postagem de {{ $item->postagem->usuario->user }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>

                                        <!-- Corpo -->
                                        <div class="modal-body">
                                            <!-- Conteúdo da Postagem -->
                                            <div class="mb-3">
                                                <p><strong>Conteúdo da postagem:</strong></p>
                                                <p>{{ $item->postagem->texto_postagem ?? 'Sem texto' }}</p>

                                                @if($item->postagem->imagens->count() > 0)
                                                <div class="text-center mt-3">
                                                    @foreach($item->postagem->imagens as $img)
                                                    <img src="{{ asset('storage/'.$img->caminho_imagem) }}"
                                                        class="img-fluid rounded mb-2"
                                                        alt="Imagem da postagem">
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>

                                            <hr>

                                            <!-- Detalhes da denúncia atual -->
                                            <div class="mb-3">
                                                <h6><strong>Denúncia atual:</strong></h6>
                                                <p><strong>Usuário denunciante:</strong>
                                                    <a href="#" target="_blank">
                                                        {{ $item->usuario->user }}
                                                    </a>
                                                </p>
                                                <p><strong>Data da denúncia:</strong> {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</p>
                                                <p><strong>Motivo:</strong> {{ $item->motivo_denuncia }}</p>
                                                @if($item->texto_denuncia)
                                                <p><strong>Texto adicional:</strong> {{ $item->texto_denuncia }}</p>
                                                @endif
                                            </div>

                                            <hr>

                                            <!-- Outras denúncias -->
                                            <div>
                                                <h6><strong>Outras denúncias desta postagem:</strong></h6>

                                                @php
                                                $outrasDenuncias = $item->postagem->denuncias->where('id','!=',$item->id);
                                                @endphp

                                                @if($outrasDenuncias->count() > 0)
                                                <ul>
                                                    @foreach($outrasDenuncias as $outra)
                                                    <li>
                                                        <strong>Usuário:</strong>
                                                        <a href="#" target="_blank">
                                                            {{ $outra->usuario->user }}
                                                        </a>
                                                        —
                                                        <strong>Data:</strong> {{ \Carbon\Carbon::parse($outra->created_at)->format('d/m/Y H:i') }}<br>
                                                        <strong>Motivo:</strong> {{ $outra->motivo_denuncia }}<br>
                                                        @if($outra->texto_denuncia)
                                                        <strong>Texto:</strong> {{ $outra->texto_denuncia }}<br>
                                                        @endif
                                                        <strong>Status:</strong>
                                                        {{ $outra->status_denuncia == 1 ? 'Pendente' : 'Resolvida' }}
                                                    </li>
                                                    <hr>
                                                    @endforeach
                                                </ul>
                                                @else
                                                <p class="text-muted">Nenhuma outra denúncia para esta postagem.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Rodapé -->
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="td-acoes">
                                <!-- Usuário ativo → Desabilitar denúncia -->
                                <form action="{{ route('denuncia.resolve', $item->id) }}" method="post">
                                    @csrf
                                    @method("put")
                                    <button type="submit" onclick="return confirm('Você tem certeza que deseja marcar a denúncia como resolvida?');" class="btn-desabilitar">
                                        <img class="img-icons" src="{{ asset('assets/images/logos/symbols/check-mark.png') }}" alt="marcar resolvida">
                                    </button>
                                </form>
                                <!-- Usuário ativo → Mostrar botão de banir -->
                                <form action="{{ route('denuncia.destroy', $item->postagem->usuario->id) }}" method="post">
                                    @csrf
                                    @method("delete")
                                    <button type="submit" onclick="return confirm('Você tem certeza que deseja banir esse usuário?');" class="btn-excluir-usuario">
                                        <img class="img-icons" src="{{ asset('assets/images/logos/symbols/block.png') }}" alt="banir usuário">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
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
@endsection