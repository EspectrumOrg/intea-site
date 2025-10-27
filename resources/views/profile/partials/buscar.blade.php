<div class="buscar-container" style="margin-bottom: 30px;">
    <h2 style="font-size: 20px; margin-bottom: 10px;">Buscar Usuários</h2>

    <!-- Campo de busca -->
    <input type="text" id="buscarInputPerfil" placeholder="Digite o nome ou apelido..." 
           style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;outline:none; margin-bottom:10px;">

    <!-- Lista de usuários -->
    <div id="listaUsuariosPerfil" class="user-list" style="display:flex;flex-direction:column;gap:10px;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('buscarInputPerfil');
    const lista = document.getElementById('listaUsuariosPerfil');

    if (!inputBuscar || !lista) return;

    // URL absoluta usando Laravel
    const buscarUrl = "{{ route('buscar.usuarios') }}";

    inputBuscar.addEventListener('input', function() {
        const query = this.value.trim();

        if(query === '') {
            lista.innerHTML = '';
            return;
        }

        fetch(`${buscarUrl}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(usuarios => {
                lista.innerHTML = '';

                if(!usuarios || usuarios.length === 0){
                    lista.innerHTML = '<p>Nenhum usuário encontrado.</p>';
                    return;
                }

                usuarios.forEach(u => {
                    const fotoUrl = u.foto ? `/storage/${u.foto}` : '/storage/default.jpg';

                    const a = document.createElement('a');
                    a.href = `/conta/${u.id}`;
                    a.style.display = 'flex';
                    a.style.alignItems = 'center';
                    a.style.gap = '10px';
                    a.style.background = '#fafafa';
                    a.style.padding = '10px';
                    a.style.borderRadius = '8px';
                    a.style.textDecoration = 'none';
                    a.style.color = 'inherit';

                    a.innerHTML = `
                        <img src="${fotoUrl}" alt="${u.user}" 
                             style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
                        <div>
                            <strong>${u.user}</strong><br>
                            <small style="color:#777;">${u.apelido ?? ''}</small>
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
