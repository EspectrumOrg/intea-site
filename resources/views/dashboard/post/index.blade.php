<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel INTEA (Beta) - Postagens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous"> <!-- Boostrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> <!-- icones boostrap-->
</head>

<body>

    <div class="container-fluid">
        <div class="row" style="height: 100vh; overflow: hidden;">
            <!-- Conteúdo principal com scroll -->
            <div class="col-md-9 p-4" style="height: 100vh; overflow-y: auto;">
                <h1 class="mb-4">Postagens</h1>
                <div class="container mt-4">
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        @foreach($postagens as $postagem)
                        <div class="col">
                            <h class="card-title">{{ $postagem->titulo_postagem }}</h5>
                                <h5 class="card-text">{{ $postagem->created_at->format('d/m/y') }}</h5>
                                <small class="text-muted">
                                    {{ $postagem->usuario->nome ?? 'Desconhecido' }}
                                </small>
                                <div class="card h-100 shadow-sm">
                                    @if ($postagem->imagens->isNotEmpty())
                                    <img src="{{ asset($postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                                    @endif

                                    <div class="card-body">
                                        <p class="card-text">{{ Str::limit($postagem->texto_postagem, 150, '...') }}</p>
                                    </div>

                                    <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                                        <a class="btn btn-sm btn-primary"> Comentarios </a>
                                        <a class="btn btn-sm btn-primary"> Reagir </a>
                                        <a class="btn btn-sm btn-primary"> Compartilhar </a>
                                    </div>


                                    <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                                        <small class="text-muted">
                                            <a class="btn btn-sm btn-primary"> (foto--perfil)Comentar </a>
                                        </small>
                                    </div>
                                </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar com scroll próprio -->
            <div class="col-md-3 bg-light min-vh-100 p-3" style="overflow-y: auto;">
                @include('dashboard.post.partials.sidebar-popular')
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>

</body>

</html>