function visualizarSenha() {
  const input = document.getElementById('password');
  const btn   = document.getElementById('btnPassword');

  const openSrc  = btn.dataset.open;
  const closeSrc = btn.dataset.close;

  if (input.type === 'password') {
    input.type = 'text';
    btn.src = closeSrc;
    btn.alt = 'esconder senha';
  } else {
    input.type = 'password';
    btn.src = openSrc;
    btn.alt = 'mostrar senha';
  }
}
