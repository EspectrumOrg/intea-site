function mostrarCampoRegistro() {
    const tipo = document.getElementById('tipo_registro').value;
    const box = document.getElementById('campo-registro-box');
    const label = document.getElementById('label-registro-dinamico');
    const input = document.getElementById('registro_profissional');

    if (tipo) {
        box.style.display = 'block';
        label.textContent = `Número do ${tipo} *`;
        input.setAttribute('required', 'required');
    } else {
        box.style.display = 'none';
        input.removeAttribute('required');
        input.value = '';
        esconderErro();
    }
}

function esconderErro() {
    const erro = document.getElementById('erro-registro');
    const input = document.getElementById('registro_profissional');
    if (erro) erro.style.display = 'none';
    if (input) input.setCustomValidity('');
}

document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('tipo_registro');
    const inputRegistro = document.getElementById('registro_profissional');

    if (!tipoSelect || !inputRegistro) return;

    inputRegistro.addEventListener('input', function () {
        const tipo = tipoSelect.value;
        const valor = inputRegistro.value.trim();
        const erro = document.getElementById('erro-registro');

        let regex;

        /*
        Essa talvez seja a parte de código mais díficil de se entender, pois o resto a gente já sabe como funciona pq já fizemos.
        Basicamente: O código vai abrir um switch case com o valor do tipo (se ele for CRM, CRP ou outros que forem 
        inseridos posteriormente) e vai comparar o modelo padrão na qual é escrito cada registro, utilizando de um genérico.
        Este genérico é escrito em regex.
        
        Sei que o termo assusta, mas acho que seria melhor nos acostumarmos, pois ele é
        bastante utilizado para análise de formato de textos, servindo para validação do mesmo. Enfim, regex seria a expressão
        padrão para escrita de formatos específicos de escrita (como por exemplo validação de email: lucas@gmail.com, pois TODOS
        os emails seguem um mesmo padrão escrito). Porém eles usam essa forma de escrita (/^CRP-\d{2}\/\d{4,5}$/) pois é a forma
        que a máquina compreende esse formato.
        
        Esse texto já passou do limite mas quero que entenda que não tem maldição alguma
        aqui: é apenas verificação de formatação. Ainda nem coloquei para validar em uma API externa... ainda.

        Resumo: validação de formato de acordo com o tipo. Se não for do tipo informado, ele mostra a mensagem de erro de formato
        inválido. Se não, ele só permite o prosseguimento e envio.
        */ 
        switch (tipo) {
            case 'CRM':
                regex = /^CRM-\w{2}\s\d{4,6}$/i; //Exemplo: CRM-SP 123456 ou crm-sp 12345
                break;
            case 'CRP':
                regex = /^CRP-\d{2}\/\d{4,5}$/; //Exemplo: CRP-06/12345
                break;
            default:
                regex = /.*/; 
        }


        /* Entendimento de validação de CRM:
        /^ = Inicio do regex e inicio da string a ser formatada
        CRM- = literalmente a escrita de "CRM-"
        \w{2} = Aceitação de duas letras (ou seja, o UF do registro)
        \s = espaço em branco, obrigatório para separação
        \d{4,6} = Pode incluir de 4 a 6 dígitos
        $/ = Fim da String e do regex
        i = Aceita tanto caracteres maiscúlos ou minuscúlos

        O mesmo quase acontece em CRP, com a diferença sendo o:
        \/ → Uma barra (/). O caractere / precisa estar junto de \ porque ele fecha a regex, assim o sistema reconhece
        que é apenas uma barra e não você fechando o regex.
        */
        if (!regex.test(valor)) {
            if (erro) erro.style.display = 'block';
            inputRegistro.setCustomValidity('Formato inválido');
        } else {
            if (erro) erro.style.display = 'none';
            inputRegistro.setCustomValidity('');
        }
    });
});