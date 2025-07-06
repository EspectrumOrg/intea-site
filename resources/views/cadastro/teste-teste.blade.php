<form method="POST" action="/usuarios/store">
    <!-- Token CSRF obrigatório no Laravel -->
    @csrf

    <label>Nome:</label>
    <input type="text" name="nomeUsuario" required><br>

    <label>Email:</label>
    <input type="email" name="emailUsuario" required><br>

    <label>Senha:</label>
    <input type="password" name="senhaUsuario" required><br>

    <label>Usuário (login):</label>
    <input type="text" name="userUsuario" required><br>

    <label>Apelido:</label>
    <input type="text" name="apelidoUsuario"><br>

    <label>CPF:</label>
    <input type="text" name="cpfUsuario" required><br>

    <label>Gênero:</label>
    <select name="generoUsuario">
        <option value="masculino">Masculino</option>
        <option value="feminino">Feminino</option>
        <option value="outro">Outro</option>
    </select><br>

    <label>Data de Nascimento:</label>
    <input type="date" name="dataNascUsuario" required><br>

    <label>CEP:</label>
    <input type="text" name="cepUsuario"><br>

    <label>Logradouro:</label>
    <input type="text" name="logradouroUsuario"><br>

    <label>Endereço:</label>
    <input type="text" name="enderecoUsuario"><br>

    <label>Rua:</label>
    <input type="text" name="ruaUsuario"><br>

    <label>Bairro:</label>
    <input type="text" name="bairroUsuario"><br>

    <label>Número:</label>
    <input type="text" name="numeroUsuario"><br>

    <label>Cidade:</label>
    <input type="text" name="cidadeUsuario"><br>

    <label>Estado:</label>
    <input type="text" name="estadoUsuario"><br>

    <label>Complemento:</label>
    <input type="text" name="complementoUsuario"><br>

    <label>Cipteia Autista:</label>
    <input type="text" name="cipteiaAutista" required><br>

    <label>Telefone:</label>
    <input type="text" name="foneUsuario"><br>

    <button type="submit">Salvar</button>
</form>
