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
<img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Avatar" width="40" height="40">
            <div>
                <p>Guilherme Fermino</p>
                <small>Online</small>
            </div>
        </div>

            <div class="messages">
            @foreach ($mensagens as $msg)
                @if ($msg->remetente_id == auth()->id())
                    @include('broadcast', ['message' => $msg->texto])
                @else
                    @php
                        $remetente = App\Models\Usuario::find($msg->remetente_id);
                    @endphp
                    @include('receive', ['message' => $msg->texto, 'remetente' => $remetente])
                @endif
            @endforeach
        </div>

        <div class="bottom">
            <form id="chatForm">
                <input type="text" id="message" placeholder="Insira a Mensagem..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    </div>

    <!-- Input hidden para passar o usuário 2 dinamicamente -->
    <input type="hidden" id="usuario2_id" value="{{ $usuario2 }}">

    <script>
        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "sa1"
        });

        const channel = pusher.subscribe("public");

        channel.bind("chat", function(data) {
            $.post("/receive", {
                    _token: "{{ csrf_token() }}",
                    message: data.message,
                })
                .done(function(res) {
                    $(".messages").append(res);
                    $(document).scrollTop($(document).height());
                });
        });

        $("#chatForm").submit(function(event) {
            event.preventDefault();

            const texto = $("#message").val().trim();
            if (texto === "") return;

            const usuario2_id = $("#usuario2_id").val();

            // Envia para broadcast
            $.ajax({
                url: "/broadcast",
                method: "POST",
                headers: {
                    "X-Socket-Id": pusher.connection.socket_id
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    usuario1_id: "{{ auth()->id() }}",
                    usuario2_id: usuario2_id,
                    message: texto,
                },
            }).done(function(res) {
                $(".messages").append(res);
                $(document).scrollTop($(document).height());
            });

            // Salva no banco
            $.ajax({
                url: "/enviar-mensagem",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    usuario1_id:" {{ auth()->id() }}",
                    usuario2_id: usuario2_id,
                    texto: texto,
                },
            });

            $("#message").val("");
        });
    </script>
</body>

</html>
