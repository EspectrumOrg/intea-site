<!-- Importa o CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/layout/barra-pesquisa.css') }}">

<div class="buscar-container">

    <div class="buscar-container">
        <input 
            class="input-barra-pesquisa"
            type="text" 
            id="buscarInputPerfil" 
            placeholder="Buscar">
        <span class="material-symbols-outlined">search</span>
        <div id="listaUsuariosPerfil" class="user-list"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('buscarInputPerfil');
    const lista = document.getElementById('listaUsuariosPerfil');

    if (!inputBuscar || !lista) return;

    // Mantém a mesma lógica de URL
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
