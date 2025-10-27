@extends('feed.conta.template.layout')

@section('main')
<div class="buscar-container" style="margin-bottom: 30px;">
    <h1 style="font-size: 20px; margin-bottom: 10px;">Buscar Usuários</h1>

    <input type="text" id="buscar" placeholder="Digite o nome ou apelido..." 
           style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;outline:none;">

    <div id="listaUsuarios" class="user-list" 
         style="margin-top:15px;display:flex;flex-direction:column;gap:10px;"></div>
</div>

<script>
    const inputBuscar = document.getElementById('buscar');
    const lista = document.getElementById('listaUsuarios');

    inputBuscar.addEventListener('input', function() {
        const query = this.value.trim();

        if (query === '') {
            lista.innerHTML = '';
            return;
        }

        fetch(`/buscar?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(usuarios => {
                lista.innerHTML = '';
                if (usuarios.length === 0) {
                    lista.innerHTML = '<p>Nenhum usuário encontrado.</p>';
                    return;
                }

                usuarios.forEach(u => {
                    const fotoUrl = u.foto ? `/storage/${u.foto}` : '/storage/default.jpg';
                    const div = document.createElement('div');
                    div.classList.add('user-item');
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.gap = '10px';
                    div.style.background = '#fafafa';
                    div.style.padding = '10px';
                    div.style.borderRadius = '8px';

                    div.innerHTML = `
                        <img src="${fotoUrl}" alt="${u.user}" 
                             style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
                        <div>
                            <div style="font-weight:600;">${u.user}</div>
                            <div style="color:#777;font-size:14px;">${u.apelido ?? ''}</div>
                        </div>
                    `;

                    lista.appendChild(div);
                });
            });
    });
</script>
@endsection
