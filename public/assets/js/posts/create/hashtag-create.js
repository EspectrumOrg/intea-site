const textarea = document.getElementById('texto_postagem');
const preview = document.getElementById('hashtag-preview');

textarea.addEventListener('input', () => {
    const text = textarea.value
        .replace(/&/g, '&amp;') // escapar HTML
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>'); // colorir hashtags
    preview.innerHTML = text + '\n';
});
