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
                <option value="odio" {{ request('motivo_denuncia') == "odio" ? 'selected' : '' }}>Ódio ou Discriminação</option>
                <option value="abuso_e_assedio" {{ request('motivo_denuncia') == "abuso_e_assedio" ? 'selected' : '' }}>Abuso ou Assédio</option>
                <option value="discurso_de_odio" {{ request('motivo_denuncia') == "discurso_de_odio" ? 'selected' : '' }}>Ameaças ou Incitação à Violência</option>
                <option value="seguranca_infantil" {{ request('motivo_denuncia') == "seguranca_infantil" ? 'selected' : '' }}>Segurança Infantil</option>
                <option value="privacidade" {{ request('motivo_denuncia') == "privacidade" ? 'selected' : '' }}>Privacidade</option>
                <option value="comportamentos_ilegais_e_regulamentados" {{ request('motivo_denuncia') == "comportamentos_ilegais_e_regulamentados" ? 'selected' : '' }}>Atividades Ilegais</option>
                <option value="spam" {{ request('motivo_denuncia') == "spam" ? 'selected' : '' }}>Spam ou Engajamento Artificial</option>
                <option value="suicidio_ou_automutilacao" {{ request('motivo_denuncia') == "suicidio_ou_automutilacao" ? 'selected' : '' }}>Risco à Integridade Pessoal</option>
                <option value="personificacao" {{ request('motivo_denuncia') == "personificacao" ? 'selected' : '' }}>Falsa Identidade</option>
                <option value="entidades_violentas_e_odiosas" {{ request('motivo_denuncia') == "entidades_violentas_e_odiosas" ? 'selected' : '' }}>Grupos Extremistas</option>
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
                            <th>Usuario denunciado</th>
                            <th>O que foi Denunciado</th>
                            <th>Motivo</th>
                            <th>Feita</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($denuncias as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->usuarioDenunciante->user }}</td>
                            <td>{{ $item->postagem->usuarioDenunciado->user ?? $item->postagem->usuario->user ?? $item->comentario->usuario->user}}</td>
                            <td>
                                @if ($item->usuarioDenunciado)
                                Usuário
                                @elseif ($item->postagem)
                                Postagem
                                @elseif ($item->comentario)
                                Comentário
                                @endif
                            </td>
                            <td>
                                @php
                                $motivos = [
                                'odio' => ['Ódio ou Discriminação', 'red-text'],
                                'abuso_e_assedio' => ['Abuso ou Assédio', 'orange-text'],
                                'discurso_de_odio' => ['Ameaças ou Incitação à Violência', 'black-text'],
                                'seguranca_infantil' => ['Segurança Infantil', 'purple-text'],
                                'privacidade' => ['Privacidade', 'blue-text'],
                                'comportamentos_ilegais_e_regulamentados' => ['Atividades Ilegais', 'brown-text'],
                                'spam' => ['Spam ou Engajamento Artificial', 'green-text'],
                                'suicidio_ou_automutilacao' => ['Risco à Integridade Pessoal', 'pink-text'],
                                'personificacao' => ['Falsa Identidade', 'teal-text'],
                                'entidades_violentas_e_odiosas' => ['Grupos Extremistas', 'dark-red-text'],
                                ];
                                @endphp

                                @if(isset($motivos[$item->motivo_denuncia]))
                                <p class="motivo_denuncia {{ $motivos[$item->motivo_denuncia][1] }}">
                                    {{ $motivos[$item->motivo_denuncia][0] }}
                                </p>
                                @else
                                <p class="motivo_denuncia gray-text">Motivo não reconhecido</p>
                                @endif
                            </td>

                            <td>{{ $item->created_at->diffForHumans() }}</td>

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