const textareaCreateModal = document.getElementById('texto_postagem_create_modal');
const previewCreateModal = document.getElementById('hashtag-preview-create-modal');

textareaCreateModal.addEventListener('input', () => {
    const text = textareaCreateModal.value
        .replace(/&/g, '&amp;') // escapar HTML
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>'); // colorir hashtags
    previewCreateModal.innerHTML = text + '\n';
});
