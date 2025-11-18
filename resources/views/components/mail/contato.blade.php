@component('mail::message')
# Novo contato recebido de {{ $emailContato }}

Uma nova mensagem foi enviada através do Intea. Confira:

**Remetente:** {{ $nomeContato }}  
**Assunto:** {{ $assuntoContato }}

---

**Mensagem:**

> {{ $mensagemContato }}

---

**Resposta:**

> {{ $respostaContato }}

---

Mensagenss relevantes devem ser respondidas o quanto antes.  

Contatos podem ser acessados na aba de <strong>suporte</strong> da área de administração.

<p>Mensagem para <strong>Equipe Espectrum</strong></p>

@endcomponent