<!-- Campo de busca estilizado -->
<div class="buscar-container" style="margin-bottom: 30px;">
    <h2 style="font-size: 20px; margin-bottom: 10px;">Buscar Usuários</h2>

    <!-- Input de busca -->
    <input 
        type="text" 
        id="buscarInputPerfil" 
        placeholder="Digite o nome ou apelido..."
        class="input-barra-pesquisa"
        style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;outline:none; margin-bottom:10px;">

    <!-- Lista de usuários -->
    <div id="listaUsuariosPerfil" class="user-list"></div>
</div>

<!-- Importa o CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/layout/barra-pesquisa.css') }}">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('buscarInputPerfil');
    const lista = document.getElementById('listaUsuariosPerfil');

    if (!inputBuscar || !lista) return;

    // Rota Laravel
    const buscarUrl = "{{ route('buscar.usuarios') }}";

    inputBuscar.addEventListener('input', function() {
        const query = this.value.trim();

        if (query === '') {
            lista.innerHTML = '';
            return;
        }

        fetch(`${buscarUrl}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(usuarios => {
                lista.innerHTML = '';

                if (!usuarios || usuarios.length === 0) {
                    lista.innerHTML = '<p class="user-not-found">Nenhum usuário encontrado.</p>';
                    return;
                }

                usuarios.forEach(u => {
                    const fotoUrl = u.foto ? `/storage/${u.foto}` : '/storage/default.jpg';

                    const a = document.createElement('a');
                    a.href = `/conta/${u.id}`;
                    a.className = 'link-barra-pesquisa';

                    a.innerHTML = `
                        <img 
                            class="foto-usuario-barra-pesquisa"
                            src="${fotoUrl}" 
                            alt="${u.user}">
                        <div class="info-barra-pesquisa">
                            <h1>${u.user}</h1>
                            <p>${u.apelido ?? ''}</p>
                        </div>
                    `;

                    lista.appendChild(a);
                });
            })
            .catch(err => {
                console.error('Erro ao buscar usuários:', err);
                lista.innerHTML = '<p>Ocorreu um erro ao buscar usuários.</p>';
            });
    });
});
</script>
