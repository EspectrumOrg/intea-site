document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea[id^="texto_postagem_edit-"]').forEach(textarea => {

        const postId = textarea.id.split('-').pop();
        const previewDiv = document.getElementById(`hashtag-preview-postagem-edit-${postId}`);
        const charCount = textarea.closest('.content')?.querySelector('.char-count');

        textarea.addEventListener('input', () => {
            const text = textarea.value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');

                previewDiv.innerHTML = text;
        });
    });
});
