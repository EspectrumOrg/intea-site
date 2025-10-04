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
            <img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">
            <!-- Pelo que entendi seria como o avatar do nosso usuário, teriamos que puxar a imagem do banco, tlgd? -->
            <div>
                <p>Guilherme Fermino</p>
                <small>Online</small>
            </div>
        </div>

        <div class="messages">
            @if (!empty($message))
                @include('receive', ['message' => $message])
            @endif
        </div>

        <div class="bottom">
            <form>
                <input type="text" id="message" placeholder="Insira a Mensagem..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    </div>
</body>

<script>
    const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", { cluster: "sa1" });
    const channel = pusher.subscribe("public");

    // Receber mensagens
    channel.bind("chat", function (data) {
        $.post("/receive", {
            _token: "{{ csrf_token() }}",
            message: data.message,
        })
        .done(function (res) {
            $(".messages").append(res);
            $(document).scrollTop($(document).height());
        });
    });

    // Enviar mensagens
    $("form").submit(function (event) {
        event.preventDefault();

        $.ajax({
            url: "/broadcast",
            method: "POST",
            headers: {
                "X-Socket-Id": pusher.connection.socket_id
            },
            data: {
                _token: "{{ csrf_token() }}",
                message: $("form #message").val(),
            },
        }).done(function (res) {
            $(".messages").append(res);
            $("form #message").val("");
            $(document).scrollTop($(document).height());
        });
    });
</script>

</html>
