@extends('feed.chats.template.chat_layout')

@section('title', 'Chat')

@section('main')
<div class="chat-container" style="display:flex; height: 90vh; gap: 10px;">
    <!-- Lado esquerdo: lista de usuários/conversas -->
    <div class="chat-list">
        <h3>Minhas Conversas</h3>
        
        <!-- Caixa de busca -->
         <div class="chat-busca">
            <input type="text" id="buscarUsuario" placeholder="Pesquisar usuários">
        </div>

        <div class="chat-list-body">
            @if($conversas->isEmpty())
                <p>Você ainda não possui conversas.</p>
            @else
                <ul id="minhas-conversas">
                    @foreach ($conversas as $conversa)
                        @php
                            $outroUsuarioId = $conversa->usuario1_id == $usuarioLogado ? $conversa->usuario2_id : $conversa->usuario1_id;
                            $outroUsuario = \App\Models\Usuario::find($outroUsuarioId);
                        @endphp
                        
                        @if($outroUsuario)
                            <li class="usuario-item" data-id="{{ $outroUsuario->id }}">
                                <img src="{{ $outroUsuario->foto ? asset('storage/' . $outroUsuario->foto) : asset('storage/default.jpg') }}"  alt="{{ $outroUsuario->user }}">
                                <span title="{{ $outroUsuario->user }}">
                                    {{ $outroUsuario->user }}
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Lado direito: chat -->
    <div class="chat-window" style="width:70%; display:flex; flex-direction:column; border:1px solid #ccc;">
        <div class="top" style="padding:10px; border-bottom:1px solid #ccc; display:flex; align-items:center;">
            <img id="avatar-destinatario" src="" width="50" height="50" alt="Avatar">
            <div>
                <p id="nome-destinatario">Nenhum usuário selecionado para conversa</p>
                <small id="status-destinatario"></small>
            </div>
        </div>

        <div class="messages" id="messages"></div>

        <div class="bottom" id="chat-form-container">
            <form id="chatForm">
                <input type="text" id="message" placeholder="Insira a mensagem..." autocomplete="off">
                <button type="submit">Enviar</button>
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
    if (usuarioSelecionado && (data.remetente_id == usuarioSelecionado || data.remetente_id == usuarioLogado)) {
        appendMensagem(data);
    }
});

// Função para adicionar mensagem no chat
function appendMensagem(data) {
    const isRemetente = data.remetente_id == usuarioLogado;
    const classe = isRemetente ? 'right' : 'left';

    let avatarHtml = '';
    if (!isRemetente && data.foto) {
        avatarHtml = `<img src="{{ asset('storage/') }}/${data.foto}" alt="Avatar">`;
    }

    const messageHtml = `
        <div class="${classe} message" style="display:flex; align-items:flex-start; margin-bottom:5px; justify-content:${isRemetente ? 'flex-end' : 'flex-start'};">
            ${isRemetente ? '' : avatarHtml}
            <p style="margin: 0.75rem 0.25rem; padding:5px 7.5px; color:#F2F2F2; border-radius:1rem; background:${isRemetente ? '#048ABF' : '#262626'}; max-width:70%;">
                ${data.message}
            </p>
            ${isRemetente ? avatarHtml : ''}
        </div>
    `;
    $("#messages").append(messageHtml);
    $("#messages").scrollTop($("#messages")[0].scrollHeight);
}

// Função para abrir chat via AJAX
function abrirChat(usuarioId) {
    usuarioSelecionado = usuarioId;

    $.ajax({
        url: "{{ route('chat.carregar') }}",
        method: "GET",
        data: { usuario2: usuarioSelecionado },
        success: function(res) {
            if(res.usuario.foto) {
                $("#avatar-destinatario").attr("src", "{{ asset('storage/') }}/" + res.usuario.foto).show();
            } else {
                $("#avatar-destinatario").hide();
            }

            $("#nome-destinatario").text(res.usuario.user);
            $("#status-destinatario").text('Online');

            $("#messages").html('');
            res.mensagens.forEach(msg => appendMensagem(msg));

            $("#chat-form-container").show();
            $("#messages").scrollTop($("#messages")[0].scrollHeight);
        }
    });
}

// Clique no usuário abre o chat
$(document).on('click', '.usuario-item', function() {
    abrirChat($(this).data('id'));
});

// Busca usuários ao digitar
$("#buscarUsuario").on('keyup', function() {
    const query = $(this).val().trim();

    if(!query) {
        location.reload();
        return;
    }

    $.ajax({
        url: "{{ route('buscar.usuarios.chat') }}",
        method: "GET",
        data: { q: query },
        success: function(res) {
            const lista = $("#minhas-conversas");
            lista.html('');

            if(res.length === 0) {
                lista.append('<li>Nenhum usuário encontrado.</li>');
                return;
            }

            res.forEach(usuario => {
                let foto = usuario.foto ? "{{ asset('storage') }}/" + usuario.foto : "{{ asset('storage/default.jpg') }}";
                lista.append(`
                    <li class="usuario-item" data-id="${usuario.id}" style="margin-bottom:10px; cursor:pointer;">
                        <img src="${foto}" width="40" height="40" alt="${usuario.user}">
                        <span style="font-weight:bold; color:#048ABF;">${usuario.user}</span>
                    </li>
                `);
            });
        }
    });
});

// Enviar mensagem
$("#chatForm").submit(function(e) {
    e.preventDefault();

    const input = $(".chat-window #message");
    const texto = input.val().trim();
    if (!texto || !usuarioSelecionado) return;

    $.ajax({
        url: "{{ route('broadcast') }}",
        method: "POST",
        headers: { "X-Socket-Id": pusher.connection.socket_id },
        data: {
            _token: "{{ csrf_token() }}",
            message: texto,
            usuario2_id: usuarioSelecionado
        },
        success: function(res) {
            appendMensagem({
                remetente_id: usuarioLogado,
                message: res.message,
                foto: "{{ Auth::user()->foto }}"
            });
            input.val('');
        }
    });
});

// Detecta parametro usuario2 na URL e abre chat automaticamente
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

$(document).ready(function() {
    const usuario2 = getQueryParam('usuario2');
    if(usuario2) {
        abrirChat(usuario2);
    }
});
</script>
@endsection
