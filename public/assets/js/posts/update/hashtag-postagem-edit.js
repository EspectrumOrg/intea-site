document.addEventListener('DOMContentLoaded', () => {
    // Para cada campo de texto de edição
    document.querySelectorAll('textarea[id^="texto_postagem_edit-"]').forEach(textarea => {
        const postId = textarea.id.split('-').pop();
        const previewDiv = document.getElementById(`hashtag-preview-postagem-edit-${postId}`);
        const charCount = textarea.closest('.content')?.querySelector('.char-count');

        textarea.addEventListener('input', () => {
            const text = textarea.value;
            
            // Atualiza contagem de caracteres
            if (charCount) {
                charCount.textContent = text.length;
            }

            // Mostra hashtags detectadas
            const hashtags = text.match(/#\w+/g);
            previewDiv.innerHTML = hashtags
                ? hashtags.map(tag => `<span class="hashtag">${tag}</span>`).join(' ')
                : '';
        });
    });
});
