// ══════════════════════════════════════════════════
//  CALENDÁRIO DE COMPROMISSOS
// ══════════════════════════════════════════════════
(function() {
  if (!document.getElementById('calGrid')) return;

  const MESES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                 'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
  const CAT_COLOR = {
    geral:   { bg: 'var(--cat-geral-bg)',   text: 'var(--cat-geral-text)',   dot: 'var(--cat-geral-dot)' },
    aula:    { bg: 'var(--cat-aula-bg)',    text: 'var(--cat-aula-text)',    dot: 'var(--cat-aula-dot)' },
    evento:  { bg: 'var(--cat-evento-bg)',  text: 'var(--cat-evento-text)',  dot: 'var(--cat-evento-dot)' },
    reuniao: { bg: 'var(--cat-reuniao-bg)', text: 'var(--cat-reuniao-text)', dot: 'var(--cat-reuniao-dot)' },
    urgente: { bg: 'var(--cat-urgente-bg)', text: 'var(--cat-urgente-text)', dot: 'var(--cat-urgente-dot)' },
  };

  let viewAno  = new Date().getFullYear();
  let viewMes  = new Date().getMonth() + 1;
  let todayStr = new Date().toISOString().split('T')[0];
  let eventosCache = {};
  let editandoId   = null;

  // ── Utilitários ────────────────────────────────
  function cacheKey(a, m) { return a + '-' + String(m).padStart(2,'0'); }
  function showCompAlert(msg, tipo) {
    const el = document.getElementById('comp-alert');
    el.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + escH(msg) + '</span></div>';
    el.style.display = 'block';
    if (tipo !== 'danger') setTimeout(() => el.style.display = 'none', 3000);
  }
  function showBanner(msg, tipo) {
    const el = document.getElementById('calAlertBanner');
    el.innerHTML = '<div class="alert alert-' + tipo + '" style="display:flex;gap:var(--space-3);align-items:flex-start"><svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0;margin-top:1px" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg><span>' + msg + '</span></div>';
    el.style.display = 'block';
  }

  const TRIMESTRE_LABEL = ['', 'Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Trimestre 4'];
  function trimestre(mes) { return Math.ceil(mes / 3); }

  // ── Renderiza o calendário ──────────────────────
  function renderCal(eventos) {
    const grid  = document.getElementById('calGrid');
    const title = document.getElementById('calTitle');
    const trim  = trimestre(viewMes);
    title.innerHTML = escH(MESES[viewMes - 1] + ' ' + viewAno)
      + ' <span style="font-size:var(--text-sm);font-weight:500;color:var(--color-text-muted);background:var(--color-gray-100);padding:3px 10px;border-radius:var(--radius-full);vertical-align:middle;margin-left:8px">'
      + escH(TRIMESTRE_LABEL[trim]) + '</span>';

    const primeiroDia = new Date(viewAno, viewMes - 1, 1).getDay();
    const totalDias   = new Date(viewAno, viewMes, 0).getDate();

    const evPorDia = {};
    (eventos || []).forEach(ev => {
      const d = ev.data_evento;
      if (!evPorDia[d]) evPorDia[d] = [];
      evPorDia[d].push(ev);
    });

    let html = '';
    for (let i = 0; i < primeiroDia; i++) {
      html += '<div class="cal-cell cal-cell--empty"></div>';
    }
    for (let d = 1; d <= totalDias; d++) {
      const dateStr = viewAno + '-' + String(viewMes).padStart(2,'0') + '-' + String(d).padStart(2,'0');
      const isToday = dateStr === todayStr;
      const dayEvs  = evPorDia[dateStr] || [];
      const pills   = dayEvs.slice(0, 3).map(ev => {
        const c = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
        const hora = ev.hora_inicio ? ' · ' + fmtHora(ev.hora_inicio) : '';
        return `<div class="cal-pill" style="background:${c.bg};color:${c.text}" data-ev-id="${ev.id}" title="${escH(ev.titulo)}">${escH(ev.titulo.length > 14 ? ev.titulo.slice(0,13)+'…' : ev.titulo)}${hora}</div>`;
      }).join('');
      const moreTag = dayEvs.length > 3 ? `<div class="cal-pill cal-pill--more">+${dayEvs.length - 3} mais</div>` : '';

      html += `<div class="cal-cell${isToday ? ' cal-cell--today' : ''}" data-date="${dateStr}">
        <span class="cal-day-num">${d}</span>
        <div class="cal-pills">${pills}${moreTag}</div>
      </div>`;
    }
    const total = primeiroDia + totalDias;
    const resto = total % 7;
    if (resto !== 0) {
      for (let i = 0; i < 7 - resto; i++) {
        html += '<div class="cal-cell cal-cell--empty"></div>';
      }
    }
    grid.innerHTML = html;

    grid.querySelectorAll('.cal-cell:not(.cal-cell--empty)').forEach(cell => {
      cell.addEventListener('click', function(e) {
        if (e.target.closest('.cal-pill[data-ev-id]')) return;
        abrirModalCriar(this.dataset.date);
      });
    });

    grid.querySelectorAll('.cal-pill[data-ev-id]').forEach(pill => {
      pill.addEventListener('click', function(e) {
        e.stopPropagation();
        abrirModalEditar(this.dataset.evId);
      });
    });
  }

  // ── Lista lateral ───────────────────────────────
  function renderLista(eventos) {
    const el    = document.getElementById('calEventList');
    const count = document.getElementById('calListCount');
    const title = document.getElementById('calListTitle');
    title.textContent = 'Compromissos — ' + MESES[viewMes - 1] + ' (' + TRIMESTRE_LABEL[trimestre(viewMes)] + ')';
    count.textContent = (eventos || []).length;

    if (!eventos || !eventos.length) {
      el.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)"><svg style="width:28px;height:28px;fill:currentColor;margin:0 auto var(--space-2);display:block" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>Nenhum compromisso.</div>';
      return;
    }

    el.innerHTML = eventos.map(ev => {
      const c = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
      const [, mes, dia] = (ev.data_evento || '').split('-');
      const horaStr = ev.hora_inicio ? fmtHora(ev.hora_inicio) : '';
      return `<div class="cal-list-item" style="cursor:pointer" data-ev-id="${ev.id}">
        <div class="cal-list-dot" style="background:${c.dot}"></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-gray-800);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.titulo)}</div>
          <div style="font-size:var(--text-xs);color:var(--color-text-muted)">${dia}/${mes}${horaStr ? ' · ' + horaStr : ''}</div>
          ${ev.descricao ? `<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.descricao)}</div>` : ''}
        </div>
        <span class="badge" style="background:${c.bg};color:${c.text};flex-shrink:0">${escH(ev.categoria)}</span>
      </div>`;
    }).join('');

    el.querySelectorAll('.cal-list-item').forEach(item => {
      item.addEventListener('click', () => abrirModalEditar(item.dataset.evId));
    });
  }

  // ── Carrega eventos ─────────────────────────────
  function carregarMes(ano, mes) {
    const key = cacheKey(ano, mes);
    if (eventosCache[key]) {
      renderCal(eventosCache[key]);
      renderLista(eventosCache[key]);
      return;
    }
    fetch('calendario_crud.php?ano=' + ano + '&mes=' + mes)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        eventosCache[key] = data.eventos;
        renderCal(data.eventos);
        renderLista(data.eventos);
      })
      .catch(() => {});
  }

  function invalidarCache() {
    eventosCache = {};
  }

  // ── Modal ───────────────────────────────────────
  function abrirModal() {
    document.getElementById('comp-alert').style.display = 'none';
    document.getElementById('modalCompromisso').style.display = 'flex';
  }
  function fecharModal() {
    document.getElementById('modalCompromisso').style.display = 'none';
    editandoId = null;
  }

  function abrirModalCriar(dateStr) {
    editandoId = null;
    document.getElementById('modalCompTitulo').textContent = 'Novo Compromisso';
    document.getElementById('btnExcluirComp').style.display = 'none';
    document.getElementById('comp-titulo').value      = '';
    document.getElementById('comp-data').value        = dateStr || todayStr;
    document.getElementById('comp-hora-inicio').value = '';
    document.getElementById('comp-hora-fim').value    = '';
    document.getElementById('comp-categoria').value   = 'geral';
    document.getElementById('comp-lembrete').value    = '30';
    document.getElementById('comp-descricao').value   = '';
    abrirModal();
    document.getElementById('comp-titulo').focus();
  }

  function abrirModalEditar(id) {
    fetch('calendario_crud.php?id=' + id)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const ev = data.evento;
        editandoId = ev.id;
        document.getElementById('modalCompTitulo').textContent = 'Editar Compromisso';
        document.getElementById('btnExcluirComp').style.display = '';
        document.getElementById('comp-titulo').value      = ev.titulo;
        document.getElementById('comp-data').value        = ev.data_evento;
        document.getElementById('comp-hora-inicio').value = ev.hora_inicio ? ev.hora_inicio.slice(0,5) : '';
        document.getElementById('comp-hora-fim').value    = ev.hora_fim    ? ev.hora_fim.slice(0,5)    : '';
        document.getElementById('comp-categoria').value   = ev.categoria   || 'geral';
        document.getElementById('comp-lembrete').value    = ev.lembrete_minutos || '30';
        document.getElementById('comp-descricao').value   = ev.descricao   || '';
        abrirModal();
      });
  }

  // ── Validação em tempo real ──────────────────────
  vfBind('comp-titulo', 'comp-titulo-error', vfRequired('Título'), {onInput:true});
  vfBind('comp-data',   'comp-data-error',   vfRequired('Data'));

  // ── Salvar ──────────────────────────────────────
  document.getElementById('btnSalvarComp').addEventListener('click', function() {
    const titulo = document.getElementById('comp-titulo').value.trim();
    const data   = document.getElementById('comp-data').value;

    let ok = true;
    if (!titulo) { vf('comp-titulo','comp-titulo-error','O título é obrigatório.'); ok = false; }
    if (!data)   { vf('comp-data','comp-data-error','A data é obrigatória.'); ok = false; }
    if (!ok) return;

    const body = {
      titulo,
      descricao:       document.getElementById('comp-descricao').value.trim(),
      data_evento:     data,
      hora_inicio:     document.getElementById('comp-hora-inicio').value,
      hora_fim:        document.getElementById('comp-hora-fim').value,
      categoria:       document.getElementById('comp-categoria').value,
      lembrete_minutos: parseInt(document.getElementById('comp-lembrete').value) || 0,
    };

    const method = editandoId ? 'PUT' : 'POST';
    if (editandoId) body.id = editandoId;

    this.disabled = true;
    this.textContent = 'Salvando…';

    fetch('calendario_crud.php', {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    })
      .then(r => r.json())
      .then(d => {
        if (!d.ok) { showCompAlert(d.msg || 'Erro ao salvar.', 'danger'); return; }
        showCompAlert(d.msg || 'Salvo!', 'success');
        invalidarCache();
        carregarMes(viewAno, viewMes);
        setTimeout(fecharModal, 900);
        agendarNotificacoes();
      })
      .catch(() => showCompAlert('Falha na comunicação.', 'danger'))
      .finally(() => {
        this.disabled = false;
        this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar';
      });
  });

  // ── Excluir ─────────────────────────────────────
  document.getElementById('btnExcluirComp').addEventListener('click', function() {
    if (!editandoId) return;
    if (!confirm('Excluir este compromisso?')) return;
    fetch('calendario_crud.php?id=' + editandoId, { method: 'DELETE' })
      .then(r => r.json())
      .then(d => {
        if (!d.ok) { showCompAlert(d.msg || 'Erro ao excluir.', 'danger'); return; }
        fecharModal();
        invalidarCache();
        carregarMes(viewAno, viewMes);
        agendarNotificacoes();
      })
      .catch(() => showCompAlert('Falha ao excluir.', 'danger'));
  });

  // ── Fechar modal ────────────────────────────────
  document.getElementById('btnFecharModalComp').addEventListener('click', fecharModal);
  document.getElementById('btnCancelarComp').addEventListener('click', fecharModal);
  document.getElementById('modalCompromisso').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
  });

  // ── Botão "Novo Compromisso" ────────────────────
  document.getElementById('btnNovoCompromisso').addEventListener('click', () => abrirModalCriar(todayStr));

  // ── Navegação mês ───────────────────────────────
  document.getElementById('calPrev').addEventListener('click', () => {
    viewMes--;
    if (viewMes < 1) { viewMes = 12; viewAno--; }
    carregarMes(viewAno, viewMes);
  });
  document.getElementById('calNext').addEventListener('click', () => {
    viewMes++;
    if (viewMes > 12) { viewMes = 1; viewAno++; }
    carregarMes(viewAno, viewMes);
  });
  document.getElementById('calHoje').addEventListener('click', () => {
    const now = new Date();
    viewAno = now.getFullYear();
    viewMes = now.getMonth() + 1;
    carregarMes(viewAno, viewMes);
  });

  // ══════════════════════════════════════════════
  //  SISTEMA DE NOTIFICAÇÕES / LEMBRETES
  // ══════════════════════════════════════════════
  const notifTimers = [];

  function agendarNotificacoes() {
    notifTimers.forEach(t => clearTimeout(t));
    notifTimers.length = 0;

    fetch('calendario_crud.php?proximos=2')
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const agora = new Date();
        const hoje  = agora.toISOString().split('T')[0];
        const eventosHoje = [];

        data.eventos.forEach(ev => {
          if (!ev.lembrete_minutos || ev.lembrete_minutos <= 0) return;

          const hora = ev.hora_inicio ? ev.hora_inicio.slice(0,5) : '00:00';
          const dtEvento = new Date(ev.data_evento + 'T' + hora + ':00');
          const dtLembrete = new Date(dtEvento.getTime() - ev.lembrete_minutos * 60000);
          const msAte = dtLembrete.getTime() - agora.getTime();

          if (ev.data_evento === hoje) eventosHoje.push(ev);

          if (msAte > 0 && msAte < 24 * 60 * 60 * 1000) {
            const t = setTimeout(() => {
              dispararNotificacao(ev);
            }, msAte);
            notifTimers.push(t);
          }
        });

        if (eventosHoje.length > 0) {
          const lista = eventosHoje.map(ev => {
            const h = ev.hora_inicio ? ' às ' + fmtHora(ev.hora_inicio) : '';
            return '<strong>' + escH(ev.titulo) + '</strong>' + h;
          }).join(' · ');
          showBanner('Compromissos de hoje: ' + lista, 'info');
        }
      })
      .catch(() => {});
  }

  function dispararNotificacao(ev) {
    const hora = ev.hora_inicio ? ' às ' + fmtHora(ev.hora_inicio) : '';
    mostrarToast(ev.titulo + hora, ev.categoria);
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('📅 Lembrete — Escola Bíblica', {
        body: ev.titulo + hora + (ev.descricao ? '\n' + ev.descricao : ''),
        icon: 'uploads/fotos/icon.png',
        tag:  'comp-' + ev.id,
      });
    }
  }

  function mostrarToast(msg, categoria) {
    const c = CAT_COLOR[categoria] || CAT_COLOR.geral;
    const toast = document.createElement('div');
    toast.style.cssText = `
      position:fixed;bottom:var(--space-6);right:var(--space-6);
      background:${c.bg};color:${c.text};
      border:1px solid ${c.dot};border-radius:var(--radius-md);
      padding:var(--space-3) var(--space-5);
      box-shadow:var(--shadow-lg);
      font-size:var(--text-sm);font-weight:500;
      z-index:9999;display:flex;align-items:center;gap:var(--space-2);
      animation:slideInToast .3s ease;
      max-width:320px;
    `;
    toast.innerHTML = `<svg style="width:16px;height:16px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
      <span>🔔 Lembrete: <strong>${escH(msg)}</strong></span>`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .4s'; setTimeout(() => toast.remove(), 400); }, 5000);
  }

  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

  // Estilo da animação do toast
  if (!document.getElementById('toastStyle')) {
    const s = document.createElement('style');
    s.id = 'toastStyle';
    s.textContent = '@keyframes slideInToast { from { transform:translateY(20px);opacity:0; } to { transform:none;opacity:1; } }';
    document.head.appendChild(s);
  }

  // ── Inicialização ───────────────────────────────
  carregarMes(viewAno, viewMes);
  agendarNotificacoes();
})();
