<!DOCTYPE html>
<html>
<head>
    <title>Cadastro Cuidador</title>
</head>
<body>
    <h1>Cadastro de Cuidador</h1>

    <form method="POST" action="{{ route('cuidador.store') }}">
        @csrf

        <label>Nome:</label><br>
        <input type="text" name="nomeUsuario" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="emailUsuario" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senhaUsuario" required><br><br>

        <label>Usuário (userUsuario):</label><br>
        <input type="text" name="userUsuario"><br><br>

        <label>Apelido (apelidoUsuario):</label><br>
        <input type="text" name="apelidoUsuario"><br><br>

        <label>CPF:</label><br>
        <input type="number" name="cpfUsuario" required><br><br>

        <label>Gênero:</label><br>
        <input type="text" name="generoUsuario" required><br><br>

        <label>Data de Nascimento:</label><br>
        <input type="date" name="dataNascUsuario" required><br><br>

        <label>CEP:</label><br>
        <input type="text" name="cepUsuario"><br><br>

        <label>Logradouro:</label><br>
        <input type="text" name="logradouroUsuario"><br><br>

        <label>Endereço:</label><br>
        <input type="text" name="enderecoUsuario"><br><br>

        <label>Rua:</label><br>
        <input type="text" name="ruaUsuario"><br><br>

        <label>Bairro:</label><br>
        <input type="text" name="bairroUsuario"><br><br>

        <label>Número:</label><br>
        <input type="number" name="numeroUsuario"><br><br>

        <label>Cidade:</label><br>
        <input type="text" name="cidadeUsuario"><br><br>

        <label>Estado:</label><br>
        <input type="text" name="estadoUsuario"><br><br>

        <label>Complemento:</label><br>
        <input type="text" name="complementoUsuario"><br><br>

        <label>Cipteia Autista (cuidador):</label><br>
        <input type="text" name="cipteiaAutista" required><br><br>

        <label>Telefone (foneUsuario):</label><br>
        <input type="text" name="foneUsuario" required><br><br>

        <button type="submit">Cadastrar Cuidador</button>
    </form>
</body>
</html>
