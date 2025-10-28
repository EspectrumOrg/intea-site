document.addEventListener('DOMContentLoaded', () => {
    const inputFileCreateRespostaComentarioFocus = document.getElementById('caminho_imagem_create_resposta_comentario_focus');
    const previewContainerCreateRespostaComentarioFocus = document.getElementById('image-preview-create-resposta-comentario-focus');
    const previewImageCreateRespostaComentarioFocus = document.getElementById('preview-img-create-resposta-comentario-focus');
    const removeButtonCreateRespostaComentarioFocus = document.getElementById('remove-image-create-resposta-comentario-focus');

    if (!inputFileCreateRespostaComentarioFocus || !previewContainerCreateRespostaComentarioFocus || !previewImageCreateRespostaComentarioFocus || !removeButtonCreateRespostaComentarioFocus) {
        console.warn("Alguns elementos não foram encontrados no DOM.");
        return;
    }

    inputFileCreateRespostaComentarioFocus.addEventListener('change', () => {
        const fileRespostaComentarioFocus = inputFileCreateRespostaComentarioFocus.files[0];
        if (fileRespostaComentarioFocus) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImageCreateRespostaComentarioFocus.src = e.target.result;
                previewContainerCreateRespostaComentarioFocus.style.display = 'block';
            };
            reader.readAsDataURL(fileRespostaComentarioFocus);
        }
    });

    removeButtonCreateRespostaComentarioFocus.addEventListener('click', () => {
        inputFileCreateRespostaComentarioFocus.value = ''; // limpa o input
        previewImageCreateRespostaComentarioFocus.src = '';
        previewContainerCreateRespostaComentarioFocus.style.display = 'none';
    });
});