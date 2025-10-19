@extends('feed.chats.template.chat_layout')

@section('title', 'Chat')

@section('main')
<div class="chat-container" style="display:flex; height: 90vh; gap: 10px;">
    <!-- Lado esquerdo: lista de usuários/conversas -->
    <div class="chat-list" style="width:30%; overflow-y:auto; border-right:1px solid #ccc; padding:10px;">
        <h3>Usuários que você segue</h3>
        @if($usuariosSeguindo->isEmpty())
            <p>Você ainda não segue ninguém.</p>
        @else
        <ul id="usuarios-seguindo">
            @foreach ($usuariosSeguindo as $usuario)
            <li class="usuario-item" data-id="{{ $usuario->id }}" style="margin-bottom:10px; cursor:pointer;">
                <img src="{{ $usuario->foto ? asset('storage/' . $usuario->foto) : asset('storage/default.jpg') }}" width="40" height="40" alt="{{ $usuario->nome }}">
                <span>{{ $usuario->nome }} ({{ $usuario->user }})</span>
            </li>
            @endforeach
        </ul>
        @endif

        <h3>Minhas Conversas</h3>
        @if($conversas->isEmpty())
            <p>Você ainda não possui conversas.</p>
        @else
        <ul id="minhas-conversas">
            @foreach ($conversas as $conversa)
            @php
                $outroUsuarioId = $conversa->usuario1_id == $usuarioLogado ? $conversa->usuario2_id : $conversa->usuario1_id;
                $outroUsuario = \App\Models\Usuario::find($outroUsuarioId);
            @endphp
            <li class="usuario-item" data-id="{{ $outroUsuario->id }}" style="margin-bottom:10px; cursor:pointer;">
                <img src="{{ $outroUsuario->foto ? asset('storage/' . $outroUsuario->foto) : asset('storage/default.jpg') }}" width="40" height="40" alt="{{ $outroUsuario->nome }}">
                <span>{{ $outroUsuario->nome }} ({{ $outroUsuario->user }})</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    <!-- Lado direito: chat -->
    <div class="chat-window" style="width:70%; display:flex; flex-direction:column; border:1px solid #ccc;">
        <div class="top" style="padding:10px; border-bottom:1px solid #ccc; display:flex; align-items:center;">
            <img id="avatar-destinatario" src="" width="40" height="40" alt="Avatar" style="display:none; margin-right:10px;">
            <div>
                <p id="nome-destinatario">Selecione um usuário para começar</p>
                <small id="status-destinatario"></small>
            </div>
        </div>

        <div class="messages" id="messages" style="flex:1; padding:10px; overflow-y:auto; background:#f9f9f9;"></div>

        <div class="bottom" style="padding:10px; border-top:1px solid #ccc; display:none;" id="chat-form-container">
            <form id="chatForm">
                <input type="text" id="message" placeholder="Insira a mensagem..." autocomplete="off" style="width:80%; padding:5px;">
                <button type="submit" style="padding:5px 10px;">Enviar</button>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="usuarioLogado" value="{{ $usuarioLogado }}">
@endsection

@section('scripts')
<script>
const usuarioLogado = $("#usuarioLogado").val();
let usuarioSelecionado = null;

// Configuração Pusher
Pusher.logToConsole = true;
const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
    cluster: "sa1"
});
const channel = pusher.subscribe("public");

// Recebe mensagens em tempo real
channel.bind("chat", function(data) {
    if(usuarioSelecionado && (data.remetente_id == usuarioSelecionado || data.remetente_id == usuarioLogado)){
        appendMensagem(data);
    }
});

// Função para adicionar mensagem no chat
function appendMensagem(data){
    const isRemetente = data.remetente_id == usuarioLogado; // true se for você
    const classe = isRemetente ? 'right' : 'left';

    let avatarHtml = '';
    if(!isRemetente && data.foto){ s// mostra foto apenas da outra pesoa
        avatarHtml = `<img src="{{ asset('storage/') }}/${data.foto}" alt="Avatar" width="40" height="40" style="margin-right:5px;">`;
    }

    const messageHtml = `
        <div class="${classe} message" style="display:flex; align-items:flex-start; margin-bottom:5px; justify-content:${isRemetente ? 'flex-end' : 'flex-start'};">
            ${isRemetente ? '' : avatarHtml}
            <p style="margin:0 5px; padding:5px 10px; border-radius:10px; background:${isRemetente ? '#DCF8C6' : '#FFF'}; max-width:70%;">${data.message}</p>
            ${isRemetente ? avatarHtml : ''}
        </div>
    `;
    $("#messages").append(messageHtml);
    $("#messages").scrollTop($("#messages")[0].scrollHeight);
}

// Clique no usuário abre o chat diretamente
$(document).on('click', '.usuario-item', function(){
    usuarioSelecionado = $(this).data('id');

    $.ajax({
        url: "{{ route('chat.carregar') }}",
        method: "GET",
        data: { usuario2: usuarioSelecionado },
        success: function(res){
            // Atualiza topo do chat
            if(res.usuario.foto){
                $("#avatar-destinatario").attr("src", "{{ asset('storage/') }}/" + res.usuario.foto).show();
            } else {
                $("#avatar-destinatario").hide();
            }
            $("#nome-destinatario").text(res.usuario.nome);
            $("#status-destinatario").text('Online');

            // Limpa mensagens antigas e adiciona novas
            $("#messages").html('');
            res.mensagens.forEach(msg => {
                appendMensagem(msg);
            });

            // Mostra formulário
            $("#chat-form-container").show();
        }
    });
});

$("#chatForm").submit(function(e){
    e.preventDefault();

    const input = $(".chat-window #message"); // pega input
    const texto = input.val().trim();
    console.log("Submit do form disparado");
    console.log("Valor do input:", texto);
    if(!texto || !usuarioSelecionado) return;

    $.ajax({
        url: "{{ route('broadcast') }}",
        method: "POST",
        headers: { "X-Socket-Id": pusher.connection.socket_id },
        data: {
            _token: "{{ csrf_token() }}",
            message: texto,
            usuario2_id: usuarioSelecionado
        },
        success: function(res){
            console.log("Mensagem enviada:", res);
            appendMensagem({
                remetente_id: usuarioLogado,
                message: res.message,
                foto: "{{ Auth::user()->foto }}"
            });
            input.val(''); // limpa input corretamente
        }
    });
});
</script>
@endsection
