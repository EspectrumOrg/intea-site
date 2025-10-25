<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Perfil</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
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

                    <!-- Mostrando diretamente "Atualizar senha" e "Excluir conta" -->
                    @if(auth()->id() == $user->id)
                    <div class="profile-settings-direct">
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>
