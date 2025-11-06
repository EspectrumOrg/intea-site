/*document.addEventListener('DOMContentLoaded', () => {
    const inputFileCreateComentario = document.getElementById('caminho_imagem_create_comentario');
    const previewContainerCreateComentario = document.getElementById('image-preview-create-comentario');
    const previewImageCreateComentario = document.getElementById('preview-img-create-comentario');
    const removeButtonCreateComentario = document.getElementById('remove-image-create-comentario');

    if (!inputFileCreateComentario || !previewContainerCreateComentario || !previewImageCreateComentario || !removeButtonCreateComentario) {
        console.warn("Alguns elementos não foram encontrados no DOM.");
        return;
    }

    inputFileCreateComentario.addEventListener('change', () => {
        const file = inputFileCreateComentario.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImageCreateComentario.src = e.target.result;
                previewContainerCreateComentario.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeButtonCreateComentario.addEventListener('click', () => {
        inputFileCreateComentario.value = ''; // limpa o input
        previewImageCreateComentario.src = '';
        previewContainerCreateComentario.style.display = 'none';
    });
});*/