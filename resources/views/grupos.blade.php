<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


<form action="{{ route('grupos.inserir') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nomeGrupo">Nome do Grupo</label>
                <input type="text" name="nomeGrupo" id="nomeGrupo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nomeManga">descrição do grupo</label>
                <input type="text" name="descGrupo" id="descGrupo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="imagem">Imagem do grupo</label>
                <input type="file" name="foto" id="foto" class="form-control" required>
            </div>

<button class="botao"type="submit" class="btn btn-primary">Cadastrar Manga</button>

@foreach($grupo as $g)
    <h2>{{ $g->nomeGrupo }}</h2>
    <p>{{ $g->descGrupo }}</p>

    @if($g->imagemGrupo)
        <img src="{{ asset('storage/'.$g->imagemGrupo) }}" width="150">
    @endif

    <!-- Botão para entrar no grupo -->
    <form action="{{ route('grupo.entrar', $g->id) }}" method="POST" style="margin-top:10px;">
        @csrf
        <button type="submit" class="btn btn-primary">Entrar no grupo</button>
    </form>
@endforeach



</body>
</html>