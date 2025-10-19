const textareaPostagemEdit = document.getElementById('texto_postagem_postagem_edit');
const previewPostagemEdit = document.getElementById('hashtag-preview-postagem-edit');

textareaPostagemEdit.addEventListener('input', () => {
    const text = textareaPostagemEdit.value
        .replace(/&/g, '&amp;') // escapar HTML
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>'); // colorir hashtags
    previewPostagemEdit.innerHTML = text + '\n';
});
