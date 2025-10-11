<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style2.css') }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>
<body>
    <div class="chat">
        <div class="top">
            @php
                $destinatario = App\Models\Usuario::find($usuario2);
            @endphp
            @if($destinatario && !empty($destinatario->foto))
                <img src="{{ asset('storage/' . $destinatario->foto) }}" alt="{{ $destinatario->nome }}" width="40" height="40">
            @endif
            <div>
                <p>{{ $destinatario->nome ?? 'Usuário' }}</p>
                <small>Online</small>
            </div>
        </div>

        <div class="messages" id="messages">
            @foreach ($mensagens as $msg)
                @php
                    $remetente = $msg->remetente_id == auth()->id() ? Auth::user() : App\Models\Usuario::find($msg->remetente_id);
                @endphp

                <div class="{{ $msg->remetente_id == auth()->id() ? 'right' : 'left' }} message">
                    @if(!empty($remetente->foto))
                        <img src="{{ asset('storage/' . $remetente->foto) }}" alt="{{ $remetente->nome }}" width="40" height="40">
                    @endif
                    <p>{{ $msg->texto }}</p>
                </div>
            @endforeach
        </div>

        <div class="bottom">
            <form id="chatForm">
                <input type="text" id="message" placeholder="Insira a Mensagem..." autocomplete="off">
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>

    <input type="hidden" id="usuario2_id" value="{{ $usuario2 }}">

    <script>
        const usuarioLogado = "{{ auth()->id() ?? 0 }}";

        Pusher.logToConsole = true;
        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "sa1"
        });
        const channel = pusher.subscribe("public");

        // Receber mensagens em tempo real
        channel.bind("chat", function(data) {
            const classe = data.remetente_id == usuarioLogado ? 'left' : 'right';
            let avatarHtml = '';                               
            if(data.foto) {
                avatarHtml = `<img src="{{ asset('storage/') }}/${data.foto}" alt="Avatar" width="40" height="40">`;
            }

            $("#messages").append(`
                <div class="${classe} message">
                    ${avatarHtml}
                    <p>${data.message}</p>
                </div>
            `);
            $("#messages").scrollTop($("#messages")[0].scrollHeight);
        });

        // Enviar mensagem
        $("#chatForm").submit(function(e){
            e.preventDefault();
            const texto = $("#message").val().trim();
            const usuario2_id = $("#usuario2_id").val();
            if(!texto) return;

            $.ajax({
                url: "{{ route('broadcast') }}",
                method: "POST",
                headers: { "X-Socket-Id": pusher.connection.socket_id },
                data: {
                    _token: "{{ csrf_token() }}",
                    message: texto,
                    usuario2_id: usuario2_id
                },
                success: function(res){
                    let avatarHtml = '';
                    if("{{ Auth::user()->foto }}") {
                        avatarHtml = `<img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->nome }}" width="40" height="40">`;
                    }
                    $("#messages").append(`
                        <div class="right message">
                            ${avatarHtml}
                            <p>${res.message}</p>
                        </div>
                    `);
                    $("#message").val('');
                    $("#messages").scrollTop($("#messages")[0].scrollHeight);
                }
            });
        });
    </script>
</body>
</html>
