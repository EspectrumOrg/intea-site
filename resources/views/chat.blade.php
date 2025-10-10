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
                <p>{{ Auth::user()->nome }}</p>
                <small>Online</small>
            </div>
        </div>

        <div class="messages">
            @foreach ($mensagens as $msg)
                @if ($msg->remetente_id == auth()->id())
                    <div class="right message">
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Avatar" width="40" height="40">
                        <p>{{ $msg->texto }}</p>
                    </div>
                @else
                    @php
                        $remetente = App\Models\Usuario::find($msg->remetente_id);
                    @endphp
                    <div class="left message">
                        <img src="{{ asset('storage/' . $remetente->foto) }}" alt="Avatar" width="40" height="40" style="border-radius:50%;">
                        <p>{{ $msg->texto }}</p>
                    </div>
                @endif
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

        // Inicializa Pusher
        Pusher.logToConsole = true;
        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "sa1"
        });
        const channel = pusher.subscribe("public");

        // Receber mensagens em tempo real
        channel.bind("chat", function(data) {
            const classe = data.remetente_id == usuarioLogado ? 'right' : 'left';
            const avatar = data.remetente_id == usuarioLogado
                ? '{{ asset("storage/" . Auth::user()->foto) }}'
                : '{{ asset("storage/" . $remetente->foto ?? "default.jpg") }}';

            $(".messages").append(`
                <div class="${classe} message">
                    <img src="${avatar}" alt="Avatar" width="40" height="40">
                    <p>${data.message}</p>
                </div>
            `);
            $(".messages").scrollTop($(".messages")[0].scrollHeight);
        });

        // Enviar mensagem (Pusher + salvar no banco)
       $("#chatForm").submit(function(e){
    e.preventDefault();
    const texto = $("#message").val().trim();
    const usuario2_id = $("#usuario2_id").val();
    if(!texto) return;

    $.ajax({
        url: "/broadcast",
        method: "POST",
        headers: { "X-Socket-Id": pusher.connection.socket_id },
        data: {
            _token: "{{ csrf_token() }}",
            message: texto,
            usuario2_id: usuario2_id
        },
        success: function(res){
            $(".messages").append(`
                <div class="right message">
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Avatar" width="40" height="40">
                    <p>${res.message}</p>
                </div>
            `);
            $("#message").val('');
            $(".messages").scrollTop($(".messages")[0].scrollHeight);
        }
    });
});
    </script>
</body>
</html>
