@extends('feed.chats.template.chat_layout')

@section('title', 'Chat')

@section('main')
<div class="chat-container">
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
        <div class="chat-window">
            <div class="top"> 
                <div class="botaoFecharChat" id="btn-fechar-chat" >
                    <a href="{{ route('chat.dashboard') }}" class="botao-voltar-chat" id="fecharChatBtn">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                </div>
                <img id="avatar-destinatario" src="">
                <div class="sem-usuario">
                    <p id="nome-destinatario">Nenhum usuário selecionado para conversa</p>
                    <small id="status-destinatario"></small>
                </div>
        </div>

        <div class="messages" id="messages"></div>

        <div class="bottom" id="chat-form-container">
           <form id="chatForm" enctype="multipart/form-data">
            

                <input type="text" id="message" placeholder="Insira a mensagem..." autocomplete="off">

                <img id="preview-img" src="" style="display:none;" />
            
                <input type="file" id="foto" name="imagem" accept="image/*" style="display:none;">

                        <button type="button" id="btnEnviarFoto" class="btn-image">
                            <span class="material-symbols-outlined">
                                imagesmode
                            </span>
                        </button>
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

// Função para adicionar     mensagem no chat
function appendMensagem(data) {
    console.log("Mensagem recebida:", data);

    const isRemetente = data.remetente_id == usuarioLogado;
    const classe = isRemetente ? 'right' : 'left';

    let avatarHtml = '';
    if (!isRemetente && data.foto) {
        avatarHtml = `<img src="{{ asset('storage/') }}/${data.foto}" alt="Avatar">`;
    }

    // coloca a imagem dentro do <p>
    let imagemHtml = '';
    if (data.imagem) {
        imagemHtml = `<img src="/storage/${data.imagem}" class="msg-imagem">`;
    }

    const messageHtml = `
        <div class="${classe} message">
            ${isRemetente ? '' : avatarHtml}

            <div class="msg-content-wrapper">
                <p>
                    ${imagemHtml}
                    ${data.message ?? ''}
                    <small class="hora-msg">${data.hora ?? data.created_at ?? ''}</small>
                </p>
            </div>

            ${isRemetente ? avatarHtml : ''}
        </div>
    `;

    $("#messages").append(messageHtml);
    $("#messages").scrollTop($("#messages")[0].scrollHeight);
}
// Função para abrir chat via AJAX
function abrirChat(usuarioId) {
    usuarioSelecionado = usuarioId;
    $(".chat-container").addClass("chat-ativo");
    $("#btn-fechar-chat").show();


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
                lista.append('<p>Nenhum usuário encontrado.</p>');
                return;
            }

            res.forEach(usuario => {
                let foto = usuario.foto ? "{{ asset('storage') }}/" + usuario.foto : "{{ asset('storage/default.jpg') }}";
                lista.append(`
                    <li class="usuario-item" data-id="${usuario.id}">
                        <img src="${foto}" alt="${usuario.user}">
                        <span>${usuario.user}</span>
                    </li>
                `);
            });
        }
    });
});

$("#btnEnviarFoto").on("click", function() {
    $("#foto").click();
});

// Enviar mensagem
$("#chatForm").submit(function(e) {
    e.preventDefault();

    const input = $(".chat-window #message");
    const texto = input.val().trim();
const imagem = $("#foto")[0]?.files[0]; // primeira e única declaração

if (!usuarioSelecionado || (!texto && !imagem)) return;

let formData = new FormData();
formData.append("_token", "{{ csrf_token() }}");
formData.append("usuario2_id", usuarioSelecionado);

// só adiciona o texto se existir
if (texto) {
    formData.append("message", texto);
}

// só adiciona a imagem se existir
if (imagem) {
    formData.append("imagem", imagem);
}
 


    $.ajax({
        url: "{{ route('broadcast') }}",
        method: "POST",
        headers: { "X-Socket-Id": pusher.connection.socket_id },
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            appendMensagem({
                remetente_id: usuarioLogado,
                message: res.message,
                foto: "{{ Auth::user()->foto }}",
                 hora: res.hora,
                     imagem: res.imagem // <-- ADICIONE ISTO

            });
            input.val('');
            $("#preview-img").hide().attr("src", "");
            $("#foto").val("");
        }
    });
});

// Detecta parametro usuario2 na URL e abre chat automaticamente
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Botão fechar chat, pra responsividade
$("#fecharChatBtn").on("click", function(e) {
    if (window.innerWidth <= 764) {
        // MOBILE — apenas fecha o chat
        e.preventDefault();
        $(".chat-container").removeClass("chat-ativo");
        return;
    }

});
$("#foto").on("change", function () {
    const file = this.files[0];

    if (!file) {
        $("#preview-img").hide();
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        $("#preview-img").attr("src", e.target.result).show();
    };
    reader.readAsDataURL(file);
});

$(document).ready(function() {
    const usuario2 = getQueryParam('usuario2');
    $("#btn-fechar-chat").hide();

    if(usuario2) {
        abrirChat(usuario2);
    }
});

/* $ ('. message. right') . last () . addClass (' lida') ; */
</script>
@endsection
