<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel INTEA - Postagens</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Conteúdo principal com scroll -->
            <div class="container-post">
                <h1>Postagens</h1>
                <div class="content-post">
                    @foreach($postagens as $postagem)
                    <div class="corpo-post">
                        <h5>{{ $postagem->titulo_postagem }}</h5>
                        <h5>{{ $postagem->created_at->format('d/m/y') }}</h5>
                        <p>{{ $postagem->usuario->nome ?? 'Desconhecido' }}</p>
                        <div>
                            @if ($postagem->imagens->isNotEmpty())
                            <img src="{{ asset($postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                            @endif

                            <div class="coment-perfil">
                                <p>{{ Str::limit($postagem->texto_postagem, 150, '...') }}</p>
                            </div>

                            <div class="opcoes-perfil">
                                <a> Comentarios </a>
                                <a> Reagir </a>
                                <a> Compartilhar </a>
                            </div>


                            <div class="foto-perfil">
                                <h1>
                                    <a> (foto--perfil)Comentar </a>
                                </h1>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @include('dashboard.post.partials.sidebar-popular')
            </div>
        </div>
    </div>

</body>

</html>