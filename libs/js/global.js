// ── Sidebar accordion ──────────────────────────────
document.querySelectorAll('[data-group]').forEach(btn => {
  btn.addEventListener('click', () => {
    const group = btn.closest('.sidebar__group');
    const isOpen = group.classList.contains('open');

    // Fecha todos
    document.querySelectorAll('.sidebar__group').forEach(g => g.classList.remove('open'));

    // Abre o clicado (toggle)
    if (!isOpen) group.classList.add('open');
  });
});

// ── Título do header dinâmico ──────────────────────
const titulos = {
  dashboard: 'Dashboard',
  alunos: 'Listar Alunos',
  'aluno-novo': 'Cadastrar Aluno',
  'aluno-editar': 'Editar Aluno',
  turmas: 'Turmas',
  'turma-nova': 'Nova Turma',
  'turma-editar': 'Editar Turma',
  professores: 'Listar Professores',
  'professor-novo': 'Cadastrar Professor',
  aulas: 'Temas de Aulas',
  cronograma: 'Cronograma de Aulas',
  'tema-novo': 'Novo Tema',
  'tema-editar': 'Editar Tema',
  'tema-detalhe': 'Detalhes do Tema',
  'aula-nova': 'Nova Aula',
  frequencia: 'Frequência',
  calendario: 'Calendário',
  'aula-pratica': 'Aula na Prática',
  'rel-geral': 'Frequência Geral',
  'rel-turma': 'Relatório por Turma',
  'rel-aluno': 'Relatório por Aluno',
  configuracoes: 'Configurações',
};

const params = new URLSearchParams(window.location.search);
const current = params.get('pagina') || 'dashboard';
const titleEl = document.getElementById('pageTitle');
if (titleEl && titulos[current]) titleEl.textContent = titulos[current];

// ── Mobile: hambúrguer ─────────────────────────────
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const hamburgerBtn = document.getElementById('hamburgerBtn');

function openSidebar() {
  sidebar.classList.add('is-open');
  overlay.classList.add('visible');
}

function closeSidebar() {
  sidebar.classList.remove('is-open');
  overlay.classList.remove('visible');
}

hamburgerBtn.addEventListener('click', () => {
  sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
});
overlay.addEventListener('click', closeSidebar);

// ── keyframe spin ─────────────────────────────────
if (!document.getElementById('spinStyle')) {
  const s = document.createElement('style');
  s.id = 'spinStyle';
  s.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
  document.head.appendChild(s);
}

// ══════════════════════════════════════════════════
// TURMAS — carrega select dinâmico em formulários de aluno/professor e módulo aulas
// ══════════════════════════════════════════════════
(function loadTurmasSelect() {
  const selects = document.querySelectorAll('select#turma, select#filtro-turma, select#filtro-prof-turma, select#temas-turma, select#tema-turma-select');
  if (!selects.length) return;
  fetch('turmas_crud.php')
    .then(r => r.json())
    .then(data => {
      if (!data.ok) return;
      selects.forEach(sel => {
        const isTemasSel = (sel.id === 'temas-turma' || sel.id === 'tema-turma-select');
        const val = sel.value;
        while (sel.options.length > 1) sel.remove(1);
        data.turmas.forEach(t => {
          const opt = document.createElement('option');
          // Módulo de temas usa id numérico; outros usam nome
          opt.value       = isTemasSel ? t.id : t.nome_turma;
          opt.textContent = t.nome_turma;
          sel.appendChild(opt);
        });
        if (val) sel.value = val;
      });
    })
    .catch(() => {});
})();

// ══════════════════════════════════════════════════
//  NOTIFICAÇÕES — Próximas Aulas
// ══════════════════════════════════════════════════
(function() {
  const btn      = document.getElementById('notifBtn');
  const pop      = document.getElementById('notifPopover');
  const wrap     = document.getElementById('notifWrap');
  const body     = document.getElementById('notifBody');
  const badge    = document.getElementById('notifBadge');
  const footer   = document.getElementById('notifFooter');
  const toggleBtn   = document.getElementById('notifToggleRead');
  const toggleIcon  = toggleBtn ? toggleBtn.querySelector('.notif-toggle-icon') : null;
  const toggleLabel = toggleBtn ? toggleBtn.querySelector('.notif-toggle-label') : null;
  const ICON_READ   = '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>';
  const ICON_UNREAD = '<path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>';
  if (!btn || !pop) return;

  const STORAGE_KEY = 'escola-notif-read';
  let loaded = false;
  let currentIds = [];

  function getReadIds() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
    catch { return []; }
  }

  function formatDate(str) {
    const d = new Date(str + 'T00:00:00');
    const hoje = new Date(); hoje.setHours(0,0,0,0);
    const amanha = new Date(hoje); amanha.setDate(amanha.getDate()+1);
    if (d.getTime() === hoje.getTime()) return 'Hoje';
    if (d.getTime() === amanha.getTime()) return 'Amanhã';
    return d.toLocaleDateString('pt-BR', { day:'2-digit', month:'short', year:'numeric' });
  }

  function updateBadge(aulas) {
    const readIds = getReadIds();
    const unread = aulas.filter(a => !readIds.includes(a.id));
    if (unread.length > 0) {
      badge.textContent = unread.length;
      badge.style.display = '';
    } else {
      badge.style.display = 'none';
    }
  }

  function renderAulas(aulas) {
    const readIds = getReadIds();
    if (!aulas.length) {
      body.innerHTML = '<div class="notif-popover__empty">'
        + '<svg style="width:32px;height:32px;fill:currentColor;color:var(--text-tertiary)" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>'
        + '<span>Nenhuma aula próxima</span></div>';
      footer.style.display = 'none';
      return;
    }
    body.innerHTML = aulas.map(a => {
      const isRead = readIds.includes(a.id);
      return `
        <div class="notif-aula-card${isRead ? ' notif-aula-card--read' : ''}">
          <div class="notif-aula-card__date">${formatDate(a.data_aula)}</div>
          <div class="notif-aula-card__title">${a.titulo || 'Sem título'}</div>
          <div class="notif-aula-card__meta">
            ${a.professor ? '<span>👤 ' + a.professor + '</span>' : ''}
            ${a.tema_titulo ? '<span>📖 ' + a.tema_titulo + '</span>' : ''}
          </div>
        </div>`;
    }).join('');
    footer.style.display = aulas.length ? '' : 'none';
    updateToggleBtn(aulas);
    updateBadge(aulas);
  }

  // Faz prefetch silencioso para mostrar badge ao carregar a página
  fetch('aulas_temas_crud.php?recurso=proximas-aulas&limit=5')
    .then(r => r.json())
    .then(d => {
      if (d.ok && d.aulas && d.aulas.length) {
        currentIds = d.aulas;
        updateBadge(d.aulas);
      }
    }).catch(() => {});

  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    const pp = document.getElementById('profilePopover');
    if (pp) pp.classList.remove('open');

    const isOpen = pop.classList.toggle('open');
    if (isOpen && !loaded) {
      loaded = true;
      fetch('aulas_temas_crud.php?recurso=proximas-aulas&limit=5')
        .then(r => r.json())
        .then(d => {
          if (!d.ok) throw new Error();
          currentIds = d.aulas || [];
          renderAulas(currentIds);
        })
        .catch(() => {
          body.innerHTML = '<div class="notif-popover__empty"><span>Erro ao carregar</span></div>';
        });
    }
  });

  function updateToggleBtn(aulas) {
    if (!toggleBtn) return;
    const readIds = getReadIds();
    const allRead = aulas.length > 0 && aulas.every(a => readIds.includes(a.id));
    toggleIcon.innerHTML = allRead ? ICON_UNREAD : ICON_READ;
    toggleLabel.textContent = allRead ? 'Marcar como não lido' : 'Marcar como lido';
  }

  toggleBtn.addEventListener('click', function() {
    const readIds = getReadIds();
    const allRead = currentIds.length > 0 && currentIds.every(a => readIds.includes(a.id));
    if (allRead) {
      // Desmarcar — remove os IDs atuais do localStorage
      const idsToRemove = currentIds.map(a => a.id);
      const remaining = readIds.filter(id => !idsToRemove.includes(id));
      localStorage.setItem(STORAGE_KEY, JSON.stringify(remaining));
    } else {
      // Marcar como lido — adiciona os IDs atuais
      const ids = [...new Set([...readIds, ...currentIds.map(a => a.id)])];
      localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }
    renderAulas(currentIds);
  });

  document.addEventListener('click', function(e) {
    if (!wrap.contains(e.target)) pop.classList.remove('open');
  });
})();

// ══════════════════════════════════════════════════
//  PERFIL POPOVER
// ══════════════════════════════════════════════════
(function() {
  const btn = document.getElementById('headerAvatarBtn');
  const pop = document.getElementById('profilePopover');
  const wrap = document.getElementById('headerAvatarWrap');
  if (!btn || !pop) return;

  let loaded = false;

  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    // fecha notificações se aberto
    const np = document.getElementById('notifPopover');
    if (np) np.classList.remove('open');

    const isOpen = pop.classList.toggle('open');
    if (isOpen && !loaded) {
      loaded = true;
      fetch('alunos_crud.php?meu-perfil=1')
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          const p = d.perfil;
          document.getElementById('ppNome').textContent = p.nome;
          document.getElementById('ppEmail').textContent = p.email;
          document.getElementById('ppTurma').textContent = p.turma;
          document.getElementById('ppPresencas').textContent = p.presencas;
          document.getElementById('ppPresLabel').textContent = 'Presen\u00e7as no ' + p.trimestre + '\u00ba trimestre';
          document.getElementById('ppPontos').textContent = p.pontos;
        })
        .catch(() => {});
    }
  });

  document.addEventListener('click', function(e) {
    if (!wrap.contains(e.target)) pop.classList.remove('open');
  });
})();
