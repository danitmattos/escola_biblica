// ══════════════════════════════════════════════════
//  AULA NA PRÁTICA — Sessões, Presença e Respostas
// ══════════════════════════════════════════════════

(function() {
  if (!document.getElementById('ap-sessoes-lista')) return;

  // ╔══════════════════════════════════════════════════════════════════╗
  //  AGRUPAMENTO E VISUALIZAÇÃO DE AULAS POR DOMINGO (STEPS)
  // ╚══════════════════════════════════════════════════════════════════╝
  function agruparAulasPorDomingo(aulas) {
    const porDomingo = {};
    aulas.forEach(aula => {
      const data = aula.data_aula;
      if (!data) return;
      if (!porDomingo[data]) porDomingo[data] = [];
      porDomingo[data].push(aula);
    });
    // Ordena por data
    return Object.entries(porDomingo)
      .sort((a, b) => new Date(a[0]) - new Date(b[0]));
  }

  let dataSelecionada = ''; // '' = todas as datas

  function renderizarStepsDomingos(aulas) {
    const stepsContainer = document.getElementById('ap-steps-domingos');
    if (!stepsContainer) return;
    const agrupado = agruparAulasPorDomingo(aulas);
    if (!agrupado.length) {
      stepsContainer.innerHTML = '<div class="steps-flow-empty">Nenhuma aula cadastrada neste trimestre.</div>';
      return;
    }

    // Data de hoje sem horário para comparação
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    let html = '<div class="steps-flow">';
    // Step "Todas" para limpar filtro
    html += '<div class="step step-todas active" data-data=""><div class="step-label" title="Todas as datas">Todas</div></div>';

    agrupado.forEach(([data]) => {
      const dataStep = new Date(data + 'T00:00:00');
      const label = dataStep.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });

      let estadoClass = 'future';
      if (dataStep < hoje)       estadoClass = 'past';
      else if (+dataStep === +hoje) estadoClass = 'today';

      html += `<div class="step ${estadoClass}" data-data="${data}"><div class="step-label">${label}</div></div>`;
    });

    html += '</div>';
    stepsContainer.innerHTML = html;

    // Clique nos steps: destaca ativo e refiltra sessoes
    stepsContainer.querySelectorAll('.step').forEach(el => {
      el.addEventListener('click', function() {
        stepsContainer.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        dataSelecionada = this.dataset.data;
        carregarSessoes();
      });
    });
  }


  // Busca aulas do cronograma para preencher os steps de domingos
  window.carregarStepsDomingos = function(turmaId, ano, trimestre) {
    ano = ano || new Date().getFullYear();
    trimestre = trimestre || Math.ceil((new Date().getMonth() + 1) / 3);
    turmaId = parseInt(turmaId) || 0;
    fetch(`aulas_temas_crud.php?recurso=cronograma&ano=${ano}&trimestre=${trimestre}&turma_id=${turmaId}`)
      .then(r => r.json())
      .then(data => {
        if (!data.ok || !data.turmas || !data.turmas.length) {
          const el = document.getElementById('ap-steps-domingos');
          if (el) el.innerHTML = '<div class="steps-flow-empty">Nenhuma aula cadastrada neste trimestre.</div>';
          return;
        }
        // Agrega aulas de todas as turmas (ou só da turma filtrada)
        const todasAulas = data.turmas.reduce((acc, t) => acc.concat(t.aulas || []), []);
        renderizarStepsDomingos(todasAulas);
      })
      .catch(() => {
        const el = document.getElementById('ap-steps-domingos');
        if (el) el.innerHTML = '';
      });
  }

  const API = 'aula_pratica_crud.php';
  let sessaoAtiva = null;
  let todosAlunos = [];
  let presentesSet = new Set();
  let abaAtual = 'ativas';

  /* ── Atualiza select de alunos (só presentes) ── */
  function atualizarSelectAlunos() {
    const selAluno = document.getElementById('ap-aluno');
    const valorAnterior = selAluno.value;
    const professorId = sessaoAtiva ? (parseInt(sessaoAtiva.professor_id) || 0) : 0;
    selAluno.innerHTML = '<option value="">\u2014 Selecionar aluno \u2014</option>';
    todosAlunos.forEach(function(a) {
      if (professorId && parseInt(a.id) === professorId) return;
      if (!presentesSet.has(Number(a.id))) return;
      var opt = document.createElement('option');
      opt.value = a.id;
      opt.textContent = a.nome;
      selAluno.appendChild(opt);
    });
    if (presentesSet.has(Number(valorAnterior))) {
      selAluno.value = valorAnterior;
    }
  }

  /* ── helpers ── */
  function showAlert(elId, msg, tipo) {
    const el = document.getElementById(elId);
    if (!el) return;
    el.style.display = msg ? 'block' : 'none';
    el.className = 'alert alert-' + (tipo || 'danger');
    el.textContent = msg || '';
  }

  /* ── Carrega turmas (sidebar + modal) ── */
  function carregarTurmas() {
    fetch(API + '?recurso=turmas')
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const selFiltro = document.getElementById('ap-filtro-turma');
        const selModal  = document.getElementById('ns-turma');
        data.turmas.forEach(t => {
          [selFiltro, selModal].forEach(sel => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.nome_turma;
            sel.appendChild(opt);
          });
        });
      });
  }

  function carregarDocentes() {
    fetch(API + '?recurso=docentes')
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        const sel = document.getElementById('ns-professor');
        const selSub = document.getElementById('ns-professor-substituto');
        data.docentes.forEach(d => {
          const opt = document.createElement('option');
          opt.value = d.id;
          opt.textContent = d.nome;
          sel.appendChild(opt);

          if (selSub) {
            const optSub = document.createElement('option');
            optSub.value = d.id;
            optSub.textContent = d.nome;
            selSub.appendChild(optSub);
          }
        });
      });
  }

  /* ── Carrega aulas de uma turma para o modal ── */
  function carregarAulasModal(turmaId) {
    const sel = document.getElementById('ns-aula');
    sel.innerHTML = '<option value="0">— Selecionar aula —</option>';
    const url = API + '?recurso=aulas' + (turmaId > 0 ? '&turma_id=' + turmaId : '');
    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (!data.ok || !data.aulas.length) return;
        data.aulas.forEach(a => {
          // Se houver data fixada nos steps, filtra aulas apenas do dia selecionado
          if (dataSelecionada && a.data_aula && a.data_aula !== dataSelecionada) return;

          const opt = document.createElement('option');
          opt.value = a.id;
          opt.dataset.titulo    = a.titulo;
          opt.dataset.data      = a.data_aula || '';
          opt.dataset.professor = a.professor || '';
          if (a.tem_sessao) {
            opt.textContent = a.titulo + (a.data_aula ? ' (' + fmtData(a.data_aula) + ')' : '') + ' — já possui sessão';
            opt.disabled = true;
            opt.style.color = 'var(--color-text-muted, #9ca3af)';
          } else {
            opt.textContent = a.titulo + (a.data_aula ? ' (' + fmtData(a.data_aula) + ')' : '');
          }
          sel.appendChild(opt);
        });
      });
  }

  /* ── Quando turma muda no modal, recarrega aulas ── */
  document.getElementById('ns-turma').addEventListener('change', function() {
    carregarAulasModal(parseInt(this.value) || 0);
  });

  /* ── Quando aula é selecionada no modal, auto-preenche ── */
  document.getElementById('ns-aula').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!opt || !opt.dataset.titulo) return;
    const titulo = document.getElementById('ns-titulo');
    titulo.value = opt.dataset.titulo;
    if (opt.dataset.data) document.getElementById('ns-data').value = opt.dataset.data;

    const nomeProfAula = (opt.dataset.professor || '').trim().toLowerCase();
    const selProf = document.getElementById('ns-professor');
    if (nomeProfAula) {
      let matched = false;
      Array.from(selProf.options).forEach(o => {
        if (o.value !== '0' && o.textContent.trim().toLowerCase().includes(nomeProfAula)) {
          selProf.value = o.value;
          matched = true;
        }
      });
      if (!matched) selProf.value = '0';
    } else {
      selProf.value = '0';
    }
  });

  /* ── Carrega alunos para o select ── */
  function carregarAlunos(turmaId) {
    const url = API + '?recurso=alunos' + (turmaId > 0 ? '&turma_id=' + turmaId : '');
    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        todosAlunos = data.alunos;
        const sel = document.getElementById('ap-aluno');
        sel.innerHTML = '<option value="">— Selecionar aluno —</option>';
        data.alunos.forEach(a => {
          const opt = document.createElement('option');
          opt.value = a.id;
          opt.textContent = a.nome;
          sel.appendChild(opt);
        });
      });
  }

  /* ── Carrega lista de sessões ── */
  function carregarSessoes() {
    const turmaId = parseInt(document.getElementById('ap-filtro-turma').value) || 0;
    const encerrada = abaAtual === 'arquivadas' ? 1 : 0;
    let url = API + '?recurso=sessoes&encerrada=' + encerrada;
    if (turmaId > 0) url += '&turma_id=' + turmaId;
    if (dataSelecionada) url += '&data=' + encodeURIComponent(dataSelecionada);
    const lista = document.getElementById('ap-sessoes-lista');
    lista.innerHTML = skeletonCards(3);

    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          lista.innerHTML = '<div style="padding:var(--space-6);color:var(--color-danger)">Erro ao carregar.</div>';
          return;
        }
        const msgVazio = encerrada
          ? 'Nenhuma sessão arquivada.'
          : 'Nenhuma sessão cadastrada.';
        if (!data.sessoes.length) {
          lista.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">' + msgVazio + '</div>';
          return;
        }
        lista.innerHTML = '';
        data.sessoes.forEach(s => {
          const item = document.createElement('div');
          item.className = 'ap-sessao-item' + (sessaoAtiva && sessaoAtiva.id == s.id ? ' active' : '');
          item.dataset.id = s.id;
          item.innerHTML =
            '<div class="ap-sessao-titulo">' + escHtml(s.titulo) + '</div>' +
            '<div class="ap-sessao-meta">' +
              (s.nome_turma ? '<span>' + escHtml(s.nome_turma) + '</span> · ' : '') +
              '<span>' + fmtData(s.data_sessao) + '</span>' +
            '</div>' +
            '<div class="ap-sessao-stats">' +
              '<span class="badge badge-primary">' + s.total_respostas + ' respostas</span>' +
              '<span class="badge badge-warning">' + s.total_pontos + ' pts</span>' +
            '</div>';
          item.addEventListener('click', () => abrirSessao(s.id));
          lista.appendChild(item);
        });
      });
  }

  /* ── Abre e exibe painel de uma sessão ── */
  function abrirSessao(id) {
    document.getElementById('ap-painel').style.display = 'none';
    document.getElementById('ap-placeholder').style.display = 'flex';

    fetch(API + '?recurso=sessao&id=' + id)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert('ap-alert', data.msg, 'danger'); return; }
        sessaoAtiva = data.sessao;

        document.querySelectorAll('.ap-sessao-item').forEach(el => {
          el.classList.toggle('active', el.dataset.id == id);
        });

        document.getElementById('ap-sessao-titulo').textContent = data.sessao.titulo;
        document.getElementById('ap-sessao-info').textContent =
          (data.sessao.nome_turma ? data.sessao.nome_turma + ' · ' : '') +
          fmtData(data.sessao.data_sessao) +
          (data.sessao.descricao ? ' · ' + data.sessao.descricao : '');

        const isEncerrada = parseInt(data.sessao.encerrada) === 1;
        document.getElementById('btnEncerrarSessao').style.display = isEncerrada ? 'none' : 'inline-flex';
        document.getElementById('btnReabrirSessao').style.display  = isEncerrada ? 'inline-flex' : 'none';
        document.querySelectorAll('#ap-presenca-card .ap-presenca-chk').forEach(function(chk) { chk.disabled = isEncerrada; });
        const cardsEditar = document.querySelectorAll('#ap-painel > .card');
        if (cardsEditar[2]) cardsEditar[2].style.display = isEncerrada ? 'none' : '';
        sessaoAtiva._encerrada = isEncerrada;

        todosAlunos = data.alunos || [];
        const professorId = parseInt(data.sessao.professor_id) || 0;
        presentesSet = new Set((data.presentes || []).map(Number));
        if (professorId) presentesSet.add(professorId);
        atualizarSelectAlunos();
        renderizarPresenca(todosAlunos, data.presentes || [], professorId);

        if (professorId && !(data.presentes || []).map(Number).includes(professorId)) {
          fetch(API + '?recurso=presenca', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sessao_id: data.sessao.id, aluno_id: professorId })
          });
        }

        popularPerguntasSel(data.perguntas || []);

        renderizarRanking(data.ranking);
        renderizarHistorico(data.historico);

        document.getElementById('ap-placeholder').style.display = 'none';
        document.getElementById('ap-painel').style.display = 'flex';
        showAlert('ap-alert', '', '');
      });
  }

  /* ── Popula o select de perguntas ── */
  function popularPerguntasSel(perguntas) {
    const sel = document.getElementById('ap-pergunta-sel');
    const txt = document.getElementById('ap-pergunta-txt');
    sel.innerHTML = '<option value="">— Selecionar pergunta —</option>';
    perguntas.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.pergunta;
      opt.textContent = p.pergunta;
      if (p.resposta) opt.dataset.resposta = p.resposta;
      sel.appendChild(opt);
    });
    const outra = document.createElement('option');
    outra.value = '__outra__';
    outra.textContent = '✏️ Outra pergunta…';
    sel.appendChild(outra);
    txt.style.display = 'none';
    txt.value = '';
    document.getElementById('ap-resposta-preview').style.display = 'none';
  }

  /* ── Mostra/esconde campo texto e prévia da resposta ── */
  document.getElementById('ap-pergunta-sel').addEventListener('change', function() {
    const txt      = document.getElementById('ap-pergunta-txt');
    const preview  = document.getElementById('ap-resposta-preview');
    const prevTxt  = document.getElementById('ap-resposta-preview-txt');
    const selOpt   = this.options[this.selectedIndex];
    const resposta = selOpt?.dataset?.resposta || '';

    txt.style.display = this.value === '__outra__' ? 'block' : 'none';

    if (resposta && this.value && this.value !== '__outra__') {
      prevTxt.textContent = resposta;
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
  });

  /* ── Renderiza ranking ── */
  function renderizarRanking(ranking) {
    const container = document.getElementById('ap-ranking-container');
    document.getElementById('ap-ranking-count').textContent = ranking.length + (ranking.length === 1 ? ' aluno' : ' alunos');

    if (!ranking.length) {
      container.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">Nenhuma resposta ainda.</div>';
      return;
    }

    const medalhas = ['🥇', '🥈', '🥉'];
    container.innerHTML = ranking.map((r, i) => {
      const medal = medalhas[i] || (i + 1 + 'º');
      const barra = ranking[0].total_pontos > 0
        ? Math.round((r.total_pontos / ranking[0].total_pontos) * 100) : 0;
      return '<div class="ap-rank-item">' +
        '<div class="ap-rank-pos">' + medal + '</div>' +
        '<div class="ap-rank-info">' +
          '<div class="ap-rank-nome">' + escHtml(r.aluno_nome) + '</div>' +
          '<div class="ap-rank-bar-wrap"><div class="ap-rank-bar" style="width:' + barra + '%"></div></div>' +
          '<div class="ap-rank-detalhe">' +
            r.qtd_sem_leitura + '× sem leitura &nbsp;·&nbsp; ' +
            r.qtd_com_leitura + '× com leitura' +
          '</div>' +
        '</div>' +
        '<div class="ap-rank-pts">' + r.total_pontos + ' <small>pts</small></div>' +
      '</div>';
    }).join('');
  }

  /* ── Renderiza histórico ── */
  function renderizarHistorico(historico) {
    const tbody = document.getElementById('ap-historico-tbody');
    const isEnc = sessaoAtiva && sessaoAtiva._encerrada;
    if (!historico.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--color-text-muted)">Nenhuma resposta ainda.</td></tr>';
      return;
    }
    tbody.innerHTML = historico.map(h =>
      '<tr>' +
        '<td>' + escHtml(h.aluno_nome) + '</td>' +
        '<td style="max-width:200px;white-space:normal;font-size:var(--text-xs);color:var(--color-text-muted)">' + (h.pergunta ? escHtml(h.pergunta) : '—') + '</td>' +
        '<td>' + (h.tipo === 'sem_leitura'
          ? '<span class="badge badge-warning">Sem leitura</span>'
          : '<span class="badge badge-primary">Com leitura</span>') +
        '</td>' +
        '<td><strong>+' + h.pontos + '</strong></td>' +
        '<td style="font-size:var(--text-xs);color:var(--color-text-muted)">' + fmtHora(h.criado_em) + '</td>' +
        '<td>' +
          (isEnc ? '' :
          '<button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" ' +
            'onclick="apRemoverResposta(' + h.id + ')" title="Remover">' +
            '<svg style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>' +
          '</button>') +
        '</td>' +
      '</tr>'
    ).join('');
  }

  /* ── Renderiza card de presença ── */
  function renderizarPresenca(alunos, presentes, professorId) {
    const container = document.getElementById('ap-presenca-container');
    const badge     = document.getElementById('ap-presenca-badge');
    if (!container) return;

    const presentesSet = new Set((presentes || []).map(Number));
    if (professorId) presentesSet.add(Number(professorId));
    if (badge) badge.textContent = presentesSet.size + ' presente' + (presentesSet.size !== 1 ? 's' : '');

    if (!alunos || !alunos.length) {
      container.innerHTML = '<div style="padding:var(--space-4);text-align:center;color:var(--color-text-muted)">Nenhum aluno ativo nesta turma.</div>';
      return;
    }

    const checkSvg = '<svg style="width:13px;height:13px;fill:var(--color-success,#22c55e);flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';

    container.innerHTML =
      '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(175px,1fr));gap:2px">' +
      alunos.map(function(a) {
        const pres = presentesSet.has(Number(a.id));
        const disabled = sessaoAtiva && sessaoAtiva._encerrada;
        return '<label class="ap-presenca-item' + (pres ? ' presente' : '') + '" data-aluno-id="' + a.id + '">' +
          '<input type="checkbox" class="ap-presenca-chk"' + (pres ? ' checked' : '') +
            (disabled ? ' disabled' : '') +
            ' onchange="togglePresenca(' + a.id + ', this)">' +
          '<span class="ap-presenca-nome">' + escHtml(a.nome) + '</span>' +
          (pres ? checkSvg : '') +
          '</label>';
      }).join('') + '</div>';
  }

  /* ── Toggle presença individual ── */
  window.togglePresenca = function(alunoId, checkbox) {
    if (!sessaoAtiva) return;
    checkbox.disabled = true;

    fetch(API + '?recurso=presenca', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ sessao_id: sessaoAtiva.id, aluno_id: alunoId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      checkbox.disabled = false;
      if (!data.ok) { checkbox.checked = !checkbox.checked; return; }

      const badge = document.getElementById('ap-presenca-badge');
      if (badge) badge.textContent = data.total_presentes + ' presente' + (data.total_presentes !== 1 ? 's' : '');

      if (data.presente) { presentesSet.add(Number(alunoId)); } else { presentesSet.delete(Number(alunoId)); }
      atualizarSelectAlunos();

      const label = checkbox.closest('.ap-presenca-item');
      if (label) {
        label.classList.toggle('presente', data.presente);
        const existingIcon = label.querySelector('svg');
        if (data.presente && !existingIcon) {
          label.insertAdjacentHTML('beforeend',
            '<svg style="width:13px;height:13px;fill:var(--color-success,#22c55e);flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>');
        } else if (!data.presente && existingIcon) {
          existingIcon.remove();
        }
      }
    })
    .catch(function() { checkbox.disabled = false; checkbox.checked = !checkbox.checked; });
  };

  /* ── Registrar resposta ao clicar no botão ── */
  function registrarResposta(tipo) {
    if (!sessaoAtiva) return;
    const alunoId = parseInt(document.getElementById('ap-aluno').value) || 0;
    if (!alunoId) { showAlert('ap-resposta-alert', 'Selecione um aluno.', 'danger'); return; }

    const sel = document.getElementById('ap-pergunta-sel');
    let pergunta = sel.value === '__outra__'
      ? document.getElementById('ap-pergunta-txt').value.trim()
      : (sel.value || '');

    const btn = document.getElementById(tipo === 'sem_leitura' ? 'btnSemLeitura' : 'btnComLeitura');
    btn.disabled = true;
    showAlert('ap-resposta-alert', '', '');

    fetch(API + '?recurso=resposta', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        sessao_id: sessaoAtiva.id,
        aluno_id:  alunoId,
        tipo:      tipo,
        pergunta:  pergunta,
      })
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      if (!data.ok) { showAlert('ap-resposta-alert', data.msg, 'danger'); return; }
      showAlert('ap-resposta-alert', data.msg, 'success');
      document.getElementById('ap-pergunta-sel').value = '';
      document.getElementById('ap-pergunta-txt').value = '';
      document.getElementById('ap-pergunta-txt').style.display = 'none';
      document.getElementById('ap-resposta-preview').style.display = 'none';
      document.getElementById('ap-aluno').value = '';
      renderizarRanking(data.ranking);
      abrirSessaoSilencioso(sessaoAtiva.id);
      carregarSessoes();
    })
    .catch(() => { btn.disabled = false; showAlert('ap-resposta-alert', 'Erro de conexão.', 'danger'); });
  }

  function abrirSessaoSilencioso(id) {
    fetch(API + '?recurso=sessao&id=' + id)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) return;
        renderizarHistorico(data.historico);
        renderizarRanking(data.ranking);
      });
  }

  /* ── Remover resposta ── */
  window.apRemoverResposta = function(id) {
    if (!confirm('Remover esta resposta e seus pontos?')) return;
    fetch(API + '?recurso=resposta&id=' + id, { method: 'DELETE' })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert('ap-alert', data.msg, 'danger'); return; }
        if (sessaoAtiva) abrirSessaoSilencioso(sessaoAtiva.id);
        carregarSessoes();
      });
  };

  /* ── Eventos ── */
  document.getElementById('btnNovaSessao').addEventListener('click', () => {
    showAlert('ns-alert', '', '');

    // Sincroniza a turma do formulário com o filtro atual da página
    const filtroTurma = parseInt(document.getElementById('ap-filtro-turma').value) || 0;
    const selTurma = document.getElementById('ns-turma');
    if (selTurma && filtroTurma > 0) selTurma.value = filtroTurma;

    const turmaAtual = parseInt(selTurma.value) || 0;
    carregarAulasModal(turmaAtual);

    // Pré-preenche a data de acordo com a data selecionada no step
    const inputData = document.getElementById('ns-data');
    if (inputData) {
      if (dataSelecionada) {
        inputData.value = dataSelecionada;
        inputData.readOnly = true;
        inputData.style.backgroundColor = 'var(--color-bg-muted, #f1f5f9)';
        inputData.style.cursor = 'not-allowed';
      } else {
        inputData.value = new Date().toISOString().split('T')[0];
        inputData.readOnly = false;
        inputData.style.backgroundColor = '';
        inputData.style.cursor = '';
      }
    }

    document.getElementById('modalNovaSessao').style.display = 'flex';
  });
  document.getElementById('btnFecharModalSessao').addEventListener('click', () => {
    document.getElementById('modalNovaSessao').style.display = 'none';
  });
  document.getElementById('btnCancelarSessao').addEventListener('click', () => {
    document.getElementById('modalNovaSessao').style.display = 'none';
  });

  // ── Validação em tempo real ──────────────────────
  vfBind('ns-titulo', 'ns-titulo-error', vfRequired('Título'), {onInput:true});

  document.getElementById('btnSalvarSessao').addEventListener('click', () => {
    const titulo       = document.getElementById('ns-titulo').value.trim();
    const data_s       = document.getElementById('ns-data').value;
    const turma_id     = parseInt(document.getElementById('ns-turma').value) || 0;
    const aula_id      = parseInt(document.getElementById('ns-aula').value)  || 0;
    const professor_id = parseInt(document.getElementById('ns-professor').value) || 0;
    const professor_sub_id = document.getElementById('ns-professor-substituto') ? (parseInt(document.getElementById('ns-professor-substituto').value) || 0) : 0;
    const descricao    = document.getElementById('ns-descricao').value.trim();
    if (!titulo) { vf('ns-titulo','ns-titulo-error','Informe o título da sessão.'); return; }

    document.getElementById('btnSalvarSessao').disabled = true;
    fetch(API + '?recurso=sessao', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ titulo, data_sessao: data_s, turma_id, aula_id, professor_id, professor_substituto_id: professor_sub_id, descricao })
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('btnSalvarSessao').disabled = false;
      if (!data.ok) { showAlert('ns-alert', data.msg, 'danger'); return; }
      document.getElementById('modalNovaSessao').style.display = 'none';
      document.getElementById('ns-titulo').value = '';
      document.getElementById('ns-descricao').value = '';
      document.getElementById('ns-professor').value = '0';
      if (document.getElementById('ns-professor-substituto')) document.getElementById('ns-professor-substituto').value = '0';
      carregarSessoes();
      setTimeout(() => abrirSessao(data.id), 300);
    })
    .catch(() => {
      document.getElementById('btnSalvarSessao').disabled = false;
      showAlert('ns-alert', 'Erro de conexão.', 'danger');
    });
  });

  document.getElementById('btnSemLeitura').addEventListener('click', () => registrarResposta('sem_leitura'));
  document.getElementById('btnComLeitura').addEventListener('click', () => registrarResposta('com_leitura'));

  document.getElementById('ap-filtro-turma').addEventListener('change', carregarSessoes);

  /* Excluir sessão */
  document.getElementById('btnExcluirSessao').addEventListener('click', () => {
    if (!sessaoAtiva) return;
    document.getElementById('excluir-sessao-nome').textContent = sessaoAtiva.titulo;
    document.getElementById('modalConfirmarExcluirSessao').style.display = 'flex';
  });

  document.getElementById('btnConfirmarExcluirSessao').addEventListener('click', () => {
    if (!sessaoAtiva) return;
    fetch(API + '?recurso=sessao&id=' + sessaoAtiva.id, { method: 'DELETE' })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalConfirmarExcluirSessao').style.display = 'none';
        if (!data.ok) { showAlert('ap-alert', data.msg, 'danger'); return; }
        sessaoAtiva = null;
        document.getElementById('ap-painel').style.display = 'none';
        document.getElementById('ap-placeholder').style.display = 'flex';
        carregarSessoes();
      });
  });

  /* ── Abas: Ativas / Arquivadas ── */
  document.getElementById('ap-tab-ativas').addEventListener('click', function() {
    abaAtual = 'ativas';
    this.style.borderBottomColor = 'var(--color-primary)';
    this.style.color = 'var(--color-primary)';
    this.classList.add('active');
    const outro = document.getElementById('ap-tab-arquivadas');
    outro.style.borderBottomColor = 'transparent';
    outro.style.color = 'var(--color-text-muted)';
    outro.classList.remove('active');
    sessaoAtiva = null;
    document.getElementById('ap-painel').style.display = 'none';
    document.getElementById('ap-placeholder').style.display = 'flex';
    carregarSessoes();
  });
  document.getElementById('ap-tab-arquivadas').addEventListener('click', function() {
    abaAtual = 'arquivadas';
    this.style.borderBottomColor = 'var(--color-primary)';
    this.style.color = 'var(--color-primary)';
    this.classList.add('active');
    const outro = document.getElementById('ap-tab-ativas');
    outro.style.borderBottomColor = 'transparent';
    outro.style.color = 'var(--color-text-muted)';
    outro.classList.remove('active');
    sessaoAtiva = null;
    document.getElementById('ap-painel').style.display = 'none';
    document.getElementById('ap-placeholder').style.display = 'flex';
    carregarSessoes();
  });

  /* ── Encerrar sessão ── */
  document.getElementById('btnEncerrarSessao').addEventListener('click', function() {
    if (!sessaoAtiva) return;
    if (!confirm('Deseja encerrar a sessão "' + sessaoAtiva.titulo + '"?\nEla será movida para a aba Arquivadas.')) return;
    fetch(API + '?recurso=encerrar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ sessao_id: sessaoAtiva.id, acao: 'encerrar' })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.ok) { showAlert('ap-alert', data.msg, 'danger'); return; }
      showAlert('ap-alert', data.msg, 'success');
      sessaoAtiva = null;
      document.getElementById('ap-painel').style.display = 'none';
      document.getElementById('ap-placeholder').style.display = 'flex';
      carregarSessoes();
    });
  });

  /* ── Reabrir sessão ── */
  document.getElementById('btnReabrirSessao').addEventListener('click', function() {
    if (!sessaoAtiva) return;
    if (!confirm('Deseja reabrir a sessão "' + sessaoAtiva.titulo + '"?\nEla voltará para a aba Ativas.')) return;
    fetch(API + '?recurso=encerrar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ sessao_id: sessaoAtiva.id, acao: 'reabrir' })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.ok) { showAlert('ap-alert', data.msg, 'danger'); return; }
      showAlert('ap-alert', data.msg, 'success');
      sessaoAtiva = null;
      document.getElementById('ap-painel').style.display = 'none';
      document.getElementById('ap-placeholder').style.display = 'flex';
      carregarSessoes();
    });
  });

  /* ── helper: lê trimestre/ano selecionados nos steps ── */
  function getTrimAnoSteps() {
    const t = parseInt(document.getElementById('ap-steps-trimestre')?.value) || Math.ceil((new Date().getMonth() + 1) / 3);
    const a = parseInt(document.getElementById('ap-steps-ano')?.value)      || new Date().getFullYear();
    return { trimestre: t, ano: a };
  }

  /* ── helper: recarrega steps respeitando turma/trim/ano ── */
  function recarregarSteps() {
    dataSelecionada = '';
    const { trimestre, ano } = getTrimAnoSteps();
    const turmaId = parseInt(document.getElementById('ap-filtro-turma').value) || 0;
    window.carregarStepsDomingos(turmaId, ano, trimestre);
  }

  /* ── Init ── */
  carregarTurmas();
  carregarDocentes();
  carregarSessoes();

  // Pré-seleciona trimestre atual no select
  const trimAtual = Math.ceil((new Date().getMonth() + 1) / 3);
  const selTrim = document.getElementById('ap-steps-trimestre');
  if (selTrim) selTrim.value = String(trimAtual);

  // Carrega steps com trimestre/ano atuais
  recarregarSteps();

  // Listeners dos selects de período
  ['ap-steps-trimestre', 'ap-steps-ano'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', recarregarSteps);
  });

  // Ao trocar turma: reseta data e recarrega steps
  document.getElementById('ap-filtro-turma').addEventListener('change', recarregarSteps);
})();
