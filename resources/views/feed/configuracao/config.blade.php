@php
use App\Models\Tendencia;
use App\Models\Postagem;

$postsPopulares = Postagem::withCount('curtidas')
->with(['imagens', 'usuario'])
->orderByDesc('curtidas_count')
->take(5)
->get();

$tendenciasPopulares = Tendencia::populares(7)->get();

@endphp
<!doctype html>
<html lang="pt-br">

<head>

    @include("monochrome")
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Perfil</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/config.css') }}">
    <!-- Postagem -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">
</head>

<body>
    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- conteúdo principal -->
            <div class="container-main">
                <div class="profile-container">
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            @if (!empty($user->foto))
                            <img src="{{ asset('storage/'.$user->foto) }}" class="card-img-top" alt="foto perfil">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
                            @endif
                        </div>
                        <div class="profile-info">
                            <h1>{{ $user->nome }}</h1>
                            <p class="username"> {{ $user->user }}</p>
                            <p class="bio">{{ $user->descricao ?? 'Sem descrição' }}</p>
                            <p class="tipo-usuario">
                                @switch($user->tipo_usuario)
                                @case(1) Administrador @break
                                @case(2) Autista @break
                                @case(3) Comunidade @break
                                @case(4) Profissional de Saúde @break
                                @case(5) Responsável @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                    <!-- Seção Acessibilidade -->
                    <div class="config-section">
                        <h3>Acessibilidade</h3>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Modo Monocromático</h4>
                                <p>Ativa a escala monocromatica para pessoas com sensibilidade</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="monochrome-sidebar-toggle"
                                    {{ Auth::user()->tema_preferencia == 'monocromatico' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <!-- Mostrando diretamente "Atualizar senha" e "Excluir conta" -->
                    @if(auth()->id() == $user->id)
                    <div class="profile-settings-direct">
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                    @endif
                </div>
            </div>
            <div class="content-popular">
                @include('profile.partials.buscar')
                @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])
            </div>
        </div>
    </div>

    <!-- modal de avisos -->
    @include("layouts.partials.avisos")

    <!-- Modal Criação de postagem -->
    @include('feed.post.create-modal')

</body>

</html>