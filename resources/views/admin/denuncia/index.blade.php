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
                            <th>Ações</th>
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
                                            <span class="material-symbols-outlined">
                                                check
                                            </span>
                                            Resolvida
                                        </button>
                                    </form>


                                    <!-- Banir usuário, postagem → usuário, comentário → usuário -->
                                    <form action="{{ route('usuario.destroy', $item->usuarioDenunciado->id ?? $item->postagem->usuario->id ?? $item->comentario->usuario->id) }}" method="post">
                                        @csrf
                                        @method("delete")
                                        <button type="button" class="btn-excluir-usuario" data-bs-toggle="modal" onclick="modalBanirUsuario('{{ $item->id }}')">
                                            <span class="material-symbols-outlined">person_off</span>
                                            Banir
                                        </button>
                                    </form>
                                    <!-- Inclui o modal banimento -->
                                    @include('admin.denuncia.partials.modal-banimento', ['item' => $item])

                                    
                                    <!-- Visulalizar -->
                                    <button type="button" class="btn-visualizar" onclick="abrirModalDenuncia('{{$item->id}}')">
                                        <span class="material-symbols-outlined">open_in_full</span>
                                        Visualizar
                                    </button>
                                </div>

                                <!-- Modal ============================================================================================================================================================================================================-->
                                <div class="modal fade" id="modalVisualizarDenuncia{{ $item->id }}" tabindex="-1" aria-labelledby="modalDenunciaLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <!-- Cabeçalho -->
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalDenunciaLabel{{ $item->id }}">
                                                    @if ($item->usuarioDenunciado)
                                                    Denúncia de usuário - {{ $item->usuarioDenunciado->user}}
                                                    @elseif ($item->postagem)
                                                    Denúncia de postagem - {{ $item->postagem->usuario->user}}
                                                    @elseif ($item->comentario)
                                                    Denúncia de comentário - {{ $item->comentario->usuario->user}}
                                                    @endif
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>

                                            <!-- Corpo -->
                                            <div class="modal-body">

                                                @if ($item->usuarioDenunciado)
                                                <!-- Usuário -->
                                                <div class="text-center">
                                                    <img
                                                        src="{{ asset('storage/'.$item->usuarioDenunciado->foto ?? 'assets/images/logos/contas/user.png') }}"
                                                        class="foto-denunciado">
                                                    <h6>{{ $item->usuarioDenunciado->user }}</h6>
                                                    <p class="text-muted"> {{ $item->usuarioDenunciado->descricao ?? 'Sem descrição'}}</p>
                                                </div>

                                                @elseif ($item->postagem)
                                                <!-- Postagem -->
                                                <p>Conteúdo Postagem</p>
                                                <p>{{ $item->postagem->texto_postagem}}</p>

                                                @if ($item->postagem->imagens->count() > 0)
                                                <div class="text-center">
                                                    @foreach ($item->postagem->imagens as $image)
                                                    <img src="{{ asset('storage/'.$image->caminho_imagem) }}" class="imagem-postagem" alt="Imagem da postagem">
                                                    @endforeach
                                                </div>
                                                @endif



                                                @elseif ($item->comentario)
                                                <!-- Comentário -->
                                                <p>Comentário:</p>
                                                <p>{{ $item->comentario->comentario}}</p>

                                                @if ($item->comentario->image)
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/'.$item->comentario->image->caminho_imagem) }}" class="img-fluid rounded" alt="Imagem do comentário">
                                                </div>
                                                @endif
                                                @endif

                                                <hr>

                                                <!-- Detalhes Denúncias -->
                                                <div class="detalhes-container">
                                                    <h1>Detalhes da denúncia</h1>
                                                    <p><strong>Denunciante:</strong> {{ $item->usuarioDenunciante->user}} / {{ $item->usuarioDenunciante->apelido }}</p>
                                                    <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</p>
                                                    <p><strong>Motivo:</strong> {{ $item->motivo_denuncia }}</p>
                                                    @if ($item->texto_denuncia)
                                                    <p><strong>Descrição:</strong> {{ $item->texto_denuncia}}</p>
                                                    @endif


                                                    <hr>

                                                    <!-- Outras Denúncias -->
                                                    @php
                                                    $outras = collect();
                                                    if ($item->usuarioDenunciado) $outras = $item->usuarioDenunciado->denuncias->where('id', '!=', $item->id);
                                                    if ($item->postagem) $outras = $item->postagem->denuncias->where('id', '!=', $item->id);
                                                    if ($item->comentario) $outras = $item->comentario->denuncias->where('id', '!=', $item->id);
                                                    @endphp

                                                    <div class="outras-denuncias">
                                                        <h1>Denúncias relacionadas:</h1>
                                                        @if ($outras->count() > 0)
                                                        <ul>
                                                            @foreach ($outras as $outra)
                                                            <li>
                                                                <strong>Denunciante:</strong> {{ $outra->usuarioDenunciante->user}} -
                                                                <strong>Data:</strong> {{ \Carbon\Carbon::parse($outra->created_at)->format('d/m/Y H:i') }}<br>
                                                                <strong>Motivo:</strong> {{ $outra->motivo_denuncia }}<br>
                                                                @if ($outra->texto_denuncia)
                                                                <strong>Texto:</strong> {{ $outra->texto_denuncia }}<br>
                                                                @endif
                                                                <strong>Status:</strong> {{ $outra->status_denuncia == 1 ? 'Pendente' : 'Resolvida' }}
                                                            </li>
                                                            <hr>
                                                            @endforeach
                                                        </ul>
                                                        @else
                                                        <p>Nenhuma outra denúncia relacionada.</p>
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
                                    <!-- fim modal -->
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