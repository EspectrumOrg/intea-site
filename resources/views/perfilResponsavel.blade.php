<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>Dados do Usuário</h1>

<p>ID: {{ $usuario->id }}</p>
<p>Nome: {{ $usuario->nome }}</p>
<p>apelido:{{$usuario->apelido}}</p>
<p>Gênero: {{ $usuario->genero->titulo ?? 'Não informado' }}</p>
<p>Email: {{ $usuario->email }}</p>

<h2>Dados do Responsável</h2>
<p>Cipteia Autista: {{ $usuario->responsavel->cipteia_autista ?? 'Não informado' }}</p>
<p>teste {{$usuario->responsavel->created_at}} </p>
<h2>Telefones</h2>
<ul> 
    eqwkhjqkej
    @foreach ($usuario->telefones as $telefone)
        <li>{{ $telefone->numero_telefone }}</li>
    @endforeach
</ul>
</body>
</html>