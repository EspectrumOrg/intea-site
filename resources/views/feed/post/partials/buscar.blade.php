<!-- style -->
<link rel="stylesheet" href="{{ asset('assets/css/layout/barra-pesquisa.css') }}">

<div class="buscar-container">
    <input 
        type="text" 
        id="buscar" 
        placeholder="Digite o nome ou apelido...">
    <div id="listaUsuarios" class="user-list"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscar');
        const lista = document.getElementById('listaUsuarios');

        if (!inputBuscar || !lista) return;

        // URL absoluta usando Blade
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
                        a.className = 'link-barra-pesquisa'

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