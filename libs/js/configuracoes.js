// ══════════════════════════════════════════════════
//  CONFIGURAÇÕES — Modo Noturno
// ══════════════════════════════════════════════════
(function() {
  const toggle = document.getElementById('toggleDarkMode');
  if (!toggle) return;

  const html = document.documentElement;

  // Sincroniza estado inicial do toggle com o que já foi aplicado
  toggle.checked = html.getAttribute('data-theme') === 'dark';

  toggle.addEventListener('change', function() {
    if (this.checked) {
      html.setAttribute('data-theme', 'dark');
      localStorage.setItem('escola-theme', 'dark');
    } else {
      html.removeAttribute('data-theme');
      localStorage.setItem('escola-theme', 'light');
    }
  });
})();
