@extends('feed.grupos.template.layout')

@section('main')

<div class="grupos-container">
    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('landpage') }}">
            <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        </a>
    </div>

    <h1>Seja bem-vindo aos Grupos Intea! Forme verdadeiras comunidades para postar e visualizar conteúdos feitos exclusivamente por outras pessoas autistas!</h1>
</div>

<form action="{{ route('grupos.inserir') }}" method="POST" enctype="multipart/form-data" class="grupos-style">
    @csrf
    <div class="form-group">
        <label for="nomeGrupo">Nome do grupo</label>
        <input type="text" name="nomeGrupo" id="nomeGrupo" class="form-control" placeholder="Insira um nome" required>
    </div>
    <div class="form-group">
        <label for="nomeManga">Descrição do grupo</label>
        <input type="text" name="descGrupo" id="descGrupo" class="form-control" placeholder="Como é seu grupo?" required>
    </div>
    <div class="form-group">
        <label for="imagem">Imagem do grupo</label>
        <input type="file" name="foto" id="foto" class="form-control" required>
    </div>
    <button class="botao" type="submit" class="btn btn-primary">Cadastrar Grupo</button>
</form>

<div class="grupos-style-dois">
    @foreach($grupo as $g)
    <h2>{{ $g->nomeGrupo }}</h2>
    <p>{{ $g->descGrupo }}</p>

    @if($g->imagemGrupo)
    <img src="{{ asset('storage/'.$g->imagemGrupo) }}" class="groupBanner">
    @endif

    <!-- Botão para entrar no grupo -->
    <form action="{{ route('grupo.entrar', $g->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn-group">Entrar no grupo</button>
    </form>
    @endforeach
</div>

@endsection