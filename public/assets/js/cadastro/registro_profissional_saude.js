    function mostrarCampoRegistro() {
        const tipo = document.getElementById('tipo_registro').value;
        const box = document.getElementById('campo-registro-box');
        const label = document.getElementById('label-registro-dinamico');

        if (tipo) {
            box.style.display = 'block';
            label.textContent = `Número do ${tipo} *`;
            document.getElementById('registro_profissional').setAttribute('required', 'required');
        } else {
            box.style.display = 'none';
            document.getElementById('registro_profissional').removeAttribute('required');
        }
    }
