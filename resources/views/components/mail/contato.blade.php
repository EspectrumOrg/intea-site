@component('mail::message')
# Novo contato recebido de {{ $emailContato }}

    Aviso!

Uma nova mensagem foi enviada através do Intea. Confira:

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

At.te,  
Equipe {{ config('app.name') }}

@endcomponent