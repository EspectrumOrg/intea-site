@component('mail::message')

![Logo da Empresa de TI]({{ url('assets/images/logos/intea/logo-lamp.png') }})

# Novo contato recebido de {{ $emailContato }}

Olá,

Você recebeu uma nova mensagem através do site. Aqui estão os detalhes:

**Remetente:** {{ $nomeContato }}  
**Assunto:** {{ $assuntoContato }}

---

**Mensagem:**

> {{ $mensagemContato }}

---

Recomendamos que mensagens relevantes sejam respondidas o quanto antes.  
Caso a mensagem seja de um usuário banido, por favor, consulte um administrador do sistema.

@component('mail::button', ['url' => route('landpage')])
Acessar o site
@endcomponent

Atenciosamente,  
Equipe {{ config('app.name') }}

@endcomponent