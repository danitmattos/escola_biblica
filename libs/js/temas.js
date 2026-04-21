// ══════════════════════════════════════════════════
//  TEMAS — Listagem (pagina=aulas)
// ══════════════════════════════════════════════════
(function() {
  if (!document.getElementById('temas-container')) return;

  const container  = document.getElementById('temas-container');
  const alertEl    = document.getElementById('temas-alert');
  let excluirTemaId = null;

  const TRIM_INFO = {
    1: { label: '1º Trimestre', cor: 'var(--trim1-bg)', corTexto: 'var(--trim1-cor)', corBorda: 'var(--trim1-borda)' },
    2: { label: '2º Trimestre', cor: 'var(--trim2-bg)', corTexto: 'var(--trim2-cor)', corBorda: 'var(--trim2-borda)' },
    3: { label: '3º Trimestre', cor: 'var(--trim3-bg)', corTexto: 'var(--trim3-cor)', corBorda: 'var(--trim3-borda)' },
    4: { label: '4º Trimestre', cor: 'var(--trim4-bg)', corTexto: 'var(--trim4-cor)', corBorda: 'var(--trim4-borda)' },
  };

  function trimPeriodo(trimestre, ano) {
    const inicio = [
      [1,  1],
      [1,  4],
      [1,  7],
      [1, 10],
    ][trimestre - 1];
    const fim = [
      [31, 3],
      [30, 6],
      [30, 9],
      [31, 12],
    ][trimestre - 1];
    const fmt = (d, m, a) => String(d).padStart(2,'0') + '/' + String(m).padStart(2,'0') + '/' + a;
    return fmt(inicio[0], inicio[1], ano) + ' – ' + fmt(fim[0], fim[1], ano);
  }

  function showAlert(msg, tipo) {
    alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
    alertEl.style.display = 'block';
    if (tipo !== 'danger') setTimeout(() => alertEl.style.display = 'none', 4000);
  }

  function carregarTemas() {
    const ano       = parseInt(document.getElementById('temas-ano').value) || new Date().getFullYear();
    const trimestre = document.getElementById('temas-trimestre').value;
    const turma_id  = document.getElementById('temas-turma').value;

    container.innerHTML = skeletonSections(3);

    const p = new URLSearchParams({ recurso: 'temas', ano, trimestre, turma_id });
    fetch('aulas_temas_crud.php?' + p)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Erro ao carregar.', 'danger'); container.innerHTML = ''; return; }
        renderTemas(data.temas, parseInt(trimestre));
      })
      .catch(() => showAlert('Falha na comunicação com o servidor.', 'danger'));
  }

  function renderTemas(temas, filtroTrim) {
    const trims = filtroTrim > 0 ? [filtroTrim] : [1, 2, 3, 4];
    const ano   = parseInt(document.getElementById('temas-ano').value) || new Date().getFullYear();
    let html = '';

    trims.forEach(t => {
      const info    = TRIM_INFO[t];
      const lista   = temas.filter(tm => parseInt(tm.trimestre) === t);
      const periodo = trimPeriodo(t, ano);

      html += `<div class="trim-section" style="margin-bottom:var(--space-6)">
        <div class="trim-header" style="background:${info.cor};border:1px solid ${info.corBorda}">
          <div style="display:flex;flex-direction:column;gap:2px">
            <span class="trim-title" style="color:${info.corTexto}">${info.label}</span>
            <span style="font-size:var(--text-xs);color:${info.corTexto};opacity:.75">${periodo}</span>
          </div>
          <span class="badge" style="background:${info.corBorda};color:${info.corTexto}">${lista.length} tema${lista.length !== 1 ? 's' : ''}</span>
          <a href="index.php?pagina=tema-novo" class="btn btn-sm" style="background:${info.corBorda};color:${info.corTexto};border:none;margin-left:auto" onclick="event.stopPropagation();document.getElementById('tema-trimestre') && (document.getElementById('tema-trimestre').value='${t}')">
            <svg style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Adicionar tema
          </a>
        </div>`;

      if (lista.length === 0) {
        html += `<div class="trim-empty">
          <svg style="width:24px;height:24px;fill:currentColor;opacity:.3;flex-shrink:0" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
          Nenhum tema cadastrado neste trimestre.
        </div>`;
      } else {
        html += '<div class="temas-grid">';
        lista.forEach(tm => {
          html += `<div class="tema-card">
            <div class="tema-card__head">
              <div>
                <div class="tema-card__title">${esc(tm.titulo)}</div>
                ${tm.descricao ? `<div class="tema-card__desc">${esc(tm.descricao)}</div>` : ''}
              </div>
            </div>
            <div class="tema-card__meta">
              <span class="badge badge-primary">${esc(tm.nome_turma || 'Sem turma')}</span>
              <span class="badge" style="background:var(--color-gray-100);color:var(--color-gray-600)">
                <svg style="width:11px;height:11px;fill:currentColor;margin-right:3px" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
                ${tm.total_aulas} aula${tm.total_aulas != 1 ? 's' : ''}
              </span>
            </div>
            <div class="tema-card__actions">
              <a href="index.php?pagina=tema-detalhe&id=${tm.id}" class="btn btn-primary btn-sm" style="flex:1">
                <svg class="icon" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                Ver Aulas
              </a>
              <a href="index.php?pagina=tema-editar&id=${tm.id}" class="btn btn-secondary btn-sm" title="Editar tema">
                <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
              </a>
              <button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" title="Excluir tema"
                onclick="abrirExcluirTema(${tm.id}, '${esc(tm.titulo).replace(/'/g,"\\'")}')">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              </button>
            </div>
          </div>`;
        });
        html += '</div>';
      }
      html += '</div>';
    });

    container.innerHTML = html;
  }

  // Exclusão de tema
  window.abrirExcluirTema = function(id, nome) {
    excluirTemaId = id;
    document.getElementById('excluir-tema-nome').textContent = nome;
    document.getElementById('modalExcluirTema').style.display = 'flex';
  };
  document.getElementById('btnConfirmarExcluirTema').addEventListener('click', function() {
    if (!excluirTemaId) return;
    this.disabled = true;
    this.textContent = 'Excluindo…';
    fetch('aulas_temas_crud.php?recurso=tema&id=' + excluirTemaId, { method: 'DELETE' })
      .then(r => r.json())
      .then(d => {
        document.getElementById('modalExcluirTema').style.display = 'none';
        showAlert(d.msg || (d.ok ? 'Excluído.' : 'Erro.'), d.ok ? 'success' : 'danger');
        if (d.ok) carregarTemas();
      })
      .catch(() => showAlert('Falha ao excluir.', 'danger'))
      .finally(() => {
        this.disabled = false;
        this.textContent = 'Excluir';
        excluirTemaId = null;
      });
  });

  document.getElementById('btnFiltrarTemas').addEventListener('click', carregarTemas);
  var _timerTemas;
  document.getElementById('temas-ano').addEventListener('input', function() {
    clearTimeout(_timerTemas);
    _timerTemas = setTimeout(carregarTemas, 500);
  });
  document.getElementById('temas-ano').addEventListener('keydown', function(e) { if (e.key === 'Enter') { clearTimeout(_timerTemas); carregarTemas(); } });
  document.getElementById('temas-trimestre').addEventListener('change', carregarTemas);
  document.getElementById('temas-turma').addEventListener('change', carregarTemas);

  carregarTemas();
})();

// ══════════════════════════════════════════════════
//  FORMULÁRIO TEMA (criar / editar)
// ══════════════════════════════════════════════════
(function() {
  const form = document.getElementById('formTema');
  if (!form) return;

  const modo   = form.dataset.modo;
  const temaId = parseInt(form.dataset.id || '0');
  const alertEl = document.getElementById('tema-form-alert');

  function showAlert(msg, tipo) {
    alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + msg + '</span></div>';
    alertEl.style.display = 'block';
  }

  // Pré-carrega dados se for edição
  if (modo === 'editar' && temaId > 0) {
    fetch('aulas_temas_crud.php?recurso=tema&id=' + temaId)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Tema não encontrado.', 'danger'); return; }
        const t = data.tema;
        document.getElementById('tema-titulo').value    = t.titulo    || '';
        document.getElementById('tema-trimestre').value = t.trimestre || '';
        document.getElementById('tema-ano').value       = t.ano       || new Date().getFullYear();
        document.getElementById('tema-turma-select').value = t.turma_id || '0';
        document.getElementById('tema-descricao').value = t.descricao || '';
      })
      .catch(() => showAlert('Erro ao carregar dados do tema.', 'danger'));
  }

  // ── Validação em tempo real ────────────────────
  vfBind('tema-titulo',    'tema-titulo-error',    vfRequired('Título'), {onInput:true});
  vfBind('tema-trimestre', 'tema-trimestre-error',  vfSelect('o trimestre'));

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const titulo    = document.getElementById('tema-titulo').value.trim();
    const trimestre = parseInt(document.getElementById('tema-trimestre').value);
    const ano       = parseInt(document.getElementById('tema-ano').value);
    const turma_id  = parseInt(document.getElementById('tema-turma-select').value) || 0;
    const descricao = document.getElementById('tema-descricao').value.trim();

    // Validação
    let valid = true;
    if (!titulo) {
      document.getElementById('tema-titulo-error').textContent = 'Título é obrigatório.';
      valid = false;
    } else { document.getElementById('tema-titulo-error').textContent = ''; }
    if (!trimestre || trimestre < 1 || trimestre > 4) {
      document.getElementById('tema-trimestre-error').textContent = 'Selecione o trimestre.';
      valid = false;
    } else { document.getElementById('tema-trimestre-error').textContent = ''; }
    if (!valid) return;

    const btn = document.getElementById('btnSalvarTema');
    btn.disabled = true;
    btn.textContent = 'Salvando…';

    const body   = { titulo, trimestre, turma_id, ano, descricao };
    const method = modo === 'editar' ? 'PUT' : 'POST';
    if (modo === 'editar') body.id = temaId;

    fetch('aulas_temas_crud.php', {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Erro ao salvar.', 'danger'); return; }
        showAlert(data.msg || 'Salvo!', 'success');
        setTimeout(() => {
          window.location.href = modo === 'editar'
            ? 'index.php?pagina=tema-detalhe&id=' + temaId
            : (data.id ? 'index.php?pagina=tema-detalhe&id=' + data.id : 'index.php?pagina=aulas');
        }, 900);
      })
      .catch(() => showAlert('Falha na comunicação.', 'danger'))
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> ' + (modo === 'editar' ? 'Salvar Alterações' : 'Criar Tema');
      });
  });
})();

// ══════════════════════════════════════════════════
//  TEMA DETALHE — aulas (pagina=tema-detalhe)
// ══════════════════════════════════════════════════
(function() {
  const wrap = document.getElementById('tema-detalhe-wrap');
  if (!wrap) return;

  const params   = new URLSearchParams(window.location.search);
  const temaId   = parseInt(params.get('id') || '0');
  const tbodyEl  = document.getElementById('tdh-tbody');
  const totalEl  = document.getElementById('tdh-total');
  const alertEl  = document.getElementById('tdh-alert');
  let editandoAulaId = null;
  let perguntasLocais = [];

  const TRIM_LABELS = { '1':'1º Trimestre','2':'2º Trimestre','3':'3º Trimestre','4':'4º Trimestre' };

  function showAlert(msg, tipo) {
    alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
    alertEl.style.display = 'block';
    if (tipo !== 'danger') setTimeout(() => alertEl.style.display = 'none', 4000);
  }
  function showModalAlert(msg, tipo) {
    const el = document.getElementById('modal-aula-alert');
    el.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
    el.style.display = 'block';
  }

  if (!temaId) {
    document.getElementById('tdh-titulo').textContent = 'Tema não encontrado';
    return;
  }

  // Carrega informações do tema
  function carregarTema() {
    fetch('aulas_temas_crud.php?recurso=tema&id=' + temaId)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const t = data.tema;
        document.getElementById('tdh-titulo').textContent = t.titulo;
        document.title = t.titulo + ' — Escola Bíblica';
        document.getElementById('pageTitle').textContent  = t.titulo;
        document.getElementById('tdh-sub').textContent   = (t.nome_turma || 'Sem turma') + ' · ' + t.ano;
        const info = document.getElementById('tdh-info');
        info.innerHTML =
          `<span class="badge badge-primary" style="font-size:var(--text-sm);padding:6px 12px">${TRIM_LABELS[t.trimestre] || ''}</span>
           <span class="badge" style="background:var(--color-gray-100);color:var(--color-gray-700);font-size:var(--text-sm);padding:6px 12px">
             <svg style="width:13px;height:13px;fill:currentColor;margin-right:4px;vertical-align:middle" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
             ${esc(t.nome_turma || 'Sem turma')}
           </span>
           ${t.descricao ? `<span style="font-size:var(--text-sm);color:var(--color-text-muted)">${esc(t.descricao)}</span>` : ''}`;
      });
  }

  // Carrega aulas
  function carregarAulas() {
    tbodyEl.innerHTML = skeletonTable(6, 4);
    fetch('aulas_temas_crud.php?recurso=aulas&tema_id=' + temaId)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Erro.', 'danger'); return; }
        totalEl.textContent = data.total;
        if (!data.aulas.length) {
          tbodyEl.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-10);color:var(--color-gray-400)">Nenhuma aula cadastrada ainda. Clique em "Nova Aula" para começar.</td></tr>';
          return;
        }
        tbodyEl.innerHTML = data.aulas.map((a, i) => `
          <tr>
            <td style="text-align:center;color:var(--color-gray-400);font-size:var(--text-xs)">${i+1}</td>
            <td><strong>${esc(a.titulo)}</strong></td>
            <td style="white-space:nowrap">${fmtData(a.data_aula)}</td>
            <td>
              ${esc(a.professor || '—')}
              ${a.professor_substituto ? `<br><small style="color:var(--color-text-muted)">Subst: ${esc(a.professor_substituto)}</small>` : ''}
            </td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--color-text-muted);font-size:var(--text-sm)">${esc(a.descricao || '—')}</td>
            <td style="text-align:right;white-space:nowrap">
              <button class="btn btn-ghost btn-sm" title="Editar" onclick="editarAula(${a.id})">
                <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
              </button>
            </td>
          </tr>`).join('');
      })
      .catch(() => showAlert('Falha na comunicação.', 'danger'));
  }

  // Carrega professores (docente=S) no select
  function carregarProfessores(valorTitular, valorSubstituto) {
    fetch('alunos_crud.php?docente=S')
      .then(r => r.json())
      .then(data => {
        const sel = document.getElementById('aula-professor');
        const selSub = document.getElementById('aula-professor-substituto');
        sel.innerHTML = '<option value="">— Selecionar professor —</option>';
        if (selSub) selSub.innerHTML = '<option value="">— Nenhum substituto —</option>';
        (data.alunos || []).forEach(a => {
          const opt = document.createElement('option');
          opt.value = a.nome;
          opt.textContent = a.nome;
          if (valorTitular && a.nome === valorTitular) opt.selected = true;
          sel.appendChild(opt);

          if (selSub) {
            const optSub = document.createElement('option');
            optSub.value = a.nome;
            optSub.textContent = a.nome;
            if (valorSubstituto && a.nome === valorSubstituto) optSub.selected = true;
            selSub.appendChild(optSub);
          }
        });
      })
      .catch(() => {});
  }

  // Renderiza perguntas locais no modal
  function renderPerguntas() {
    const container  = document.getElementById('aula-perguntas-lista');
    const btnAdd     = document.getElementById('btnAdicionarPergunta');
    if (!container) return;
    if (btnAdd) btnAdd.disabled = perguntasLocais.length >= 5;

    if (perguntasLocais.length === 0) {
      container.innerHTML = '<p class="perg-empty">Nenhuma pergunta adicionada.</p>';
      return;
    }

    container.innerHTML = perguntasLocais.map((p, i) => `
      <div class="perg-row">
        <span class="perg-num">${i + 1}</span>
        <div class="perg-fields">
          <input type="text" class="form-control" placeholder="Pergunta…" maxlength="500"
                 value="${esc(p.pergunta)}" data-pi="${i}" data-pf="pergunta">
          <textarea class="form-control" rows="2" placeholder="Resposta… (opcional)" maxlength="1000"
                    data-pi="${i}" data-pf="resposta">${esc(p.resposta)}</textarea>
        </div>
        <button type="button" class="perg-del" data-pd="${i}" title="Remover pergunta">
          <svg viewBox="0 0 20 20" width="16" height="16"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" fill="currentColor"/></svg>
        </button>
      </div>`).join('');

    container.querySelectorAll('[data-pf]').forEach(el => {
      el.addEventListener('input', e => {
        perguntasLocais[+e.target.dataset.pi][e.target.dataset.pf] = e.target.value;
      });
    });
    container.querySelectorAll('[data-pd]').forEach(btn => {
      btn.addEventListener('click', () => {
        perguntasLocais.splice(+btn.dataset.pd, 1);
        renderPerguntas();
      });
    });
  }

  // Carrega perguntas de uma aula existente
  function carregarPerguntas(aulaId) {
    fetch('aulas_temas_crud.php?recurso=perguntas&aula_id=' + aulaId)
      .then(r => r.json())
      .then(d => {
        perguntasLocais = (d.perguntas || []).map(p => ({
          pergunta: p.pergunta || '',
          resposta: p.resposta || '',
        }));
        renderPerguntas();
      })
      .catch(() => { perguntasLocais = []; renderPerguntas(); });
  }

  // Abrir modal nova aula
  window.abrirNovaAula = function() {
    editandoAulaId = null;
    document.getElementById('modalAulaTitulo').textContent  = 'Nova Aula';
    document.getElementById('btnExcluirAula').style.display = 'none';
    document.getElementById('modal-aula-alert').style.display = 'none';
    document.getElementById('aula-titulo').value     = '';
    document.getElementById('aula-data').value       = '';
    document.getElementById('aula-descricao').value  = '';
    perguntasLocais = [];
    renderPerguntas();
    carregarProfessores('', '');
    document.getElementById('modalAula').style.display = 'flex';
    document.getElementById('aula-titulo').focus();
  };

  // Abrir modal editar aula
  window.editarAula = function(id) {
    fetch('aulas_temas_crud.php?recurso=aula&id=' + id)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const a = data.aula;
        editandoAulaId = a.id;
        document.getElementById('modalAulaTitulo').textContent  = 'Editar Aula';
        document.getElementById('btnExcluirAula').style.display = '';
        document.getElementById('modal-aula-alert').style.display = 'none';
        document.getElementById('aula-titulo').value    = a.titulo    || '';
        document.getElementById('aula-data').value      = a.data_aula || '';
        document.getElementById('aula-descricao').value = a.descricao  || '';
        carregarProfessores(a.professor || '', a.professor_substituto || '');
        perguntasLocais = [];
        renderPerguntas();
        carregarPerguntas(a.id);
        document.getElementById('modalAula').style.display = 'flex';
      });
  };

  // Salvar aula
  document.getElementById('btnSalvarAula').addEventListener('click', function() {
    const titulo = document.getElementById('aula-titulo').value.trim();
    if (!titulo) { showModalAlert('O título é obrigatório.', 'danger'); return; }

    const body = {
      tema_id:   temaId,
      titulo,
      data_aula:  document.getElementById('aula-data').value,
      professor:  document.getElementById('aula-professor').value,
      professor_substituto: document.getElementById('aula-professor-substituto') ? document.getElementById('aula-professor-substituto').value : '',
      descricao:  document.getElementById('aula-descricao').value.trim(),
      perguntas:  perguntasLocais.filter(p => p.pergunta.trim() !== ''),
    };
    const method = editandoAulaId ? 'PUT' : 'POST';
    if (editandoAulaId) body.id = editandoAulaId;

    this.disabled = true;
    this.textContent = 'Salvando…';
    fetch('aulas_temas_crud.php?recurso=aula', {
      method,
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(body),
    })
      .then(r => r.json())
      .then(d => {
        if (!d.ok) { showModalAlert(d.msg || 'Erro.', 'danger'); return; }
        document.getElementById('modalAula').style.display = 'none';
        showAlert(d.msg || 'Salvo!', 'success');
        carregarAulas();
      })
      .catch(() => showModalAlert('Falha na comunicação.', 'danger'))
      .finally(() => {
        this.disabled = false;
        this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar';
      });
  });

  // Excluir aula
  document.getElementById('btnExcluirAula').addEventListener('click', function() {
    if (!editandoAulaId) return;
    if (!confirm('Excluir esta aula?')) return;
    fetch('aulas_temas_crud.php?recurso=aula&id=' + editandoAulaId, { method: 'DELETE' })
      .then(r => r.json())
      .then(d => {
        if (!d.ok) { showModalAlert(d.msg || 'Erro.', 'danger'); return; }
        document.getElementById('modalAula').style.display = 'none';
        showAlert(d.msg || 'Excluída!', 'success');
        carregarAulas();
      })
      .catch(() => showModalAlert('Falha ao excluir.', 'danger'));
  });

  // Fechar modal
  const fecharModal = () => {
    document.getElementById('modalAula').style.display = 'none';
    editandoAulaId = null;
  };
  document.getElementById('btnFecharModalAula').addEventListener('click', fecharModal);
  document.getElementById('btnCancelarAula').addEventListener('click', fecharModal);
  document.getElementById('modalAula').addEventListener('click', e => { if (e.target === document.getElementById('modalAula')) fecharModal(); });

  document.getElementById('btnNovaAula').addEventListener('click', abrirNovaAula);

  document.getElementById('btnAdicionarPergunta').addEventListener('click', function() {
    if (perguntasLocais.length >= 5) return;
    perguntasLocais.push({ pergunta: '', resposta: '' });
    renderPerguntas();
  });

  carregarTema();
  carregarAulas();
})();
