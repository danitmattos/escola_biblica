// ══════════════════════════════════════════════════
//  DASHBOARD — Estatísticas e Cards
// ══════════════════════════════════════════════════
(function initDashboard() {
  const valAlunos   = document.getElementById('dash-val-alunos');
  const trendAlunos = document.getElementById('dash-trend-alunos');
  const valProf     = document.getElementById('dash-val-prof');
  const trendProf   = document.getElementById('dash-trend-prof');
  const valTurmas   = document.getElementById('dash-val-turmas');
  const trendTurmas = document.getElementById('dash-trend-turmas');
  if (!valAlunos) return;

  const svgUp   = '<svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>';
  const svgDown = '<svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';

  fetch('alunos_crud.php?stats=1')
    .then(r => r.json())
    .then(d => {
      if (!d.ok) return;
      valAlunos.textContent = d.total;
      const diff = d.novos_mes - d.novos_mes_anterior;
      const label = (diff >= 0 ? '+' : '') + d.novos_mes + ' este mês';
      trendAlunos.className = 'trend ' + (diff >= 0 ? 'trend-up' : 'trend-down');
      trendAlunos.innerHTML  = (diff >= 0 ? svgUp : svgDown) + ' ' + label;
      if (valProf) {
        valProf.textContent = d.docentes;
        trendProf.innerHTML = svgUp + ' docente' + (d.docentes !== 1 ? 's' : '') + ' ativo' + (d.docentes !== 1 ? 's' : '');
      }
    })
    .catch(() => { valAlunos.textContent = '—'; });

  fetch('turmas_crud.php')
    .then(r => r.json())
    .then(d => {
      if (!d.ok) return;
      const total = typeof d.total === 'number' ? d.total : (d.turmas?.length ?? 0);
      valTurmas.textContent = total;
      trendTurmas.innerHTML = svgUp + ' ' + total + ' cadastrada' + (total !== 1 ? 's' : '');
    })
    .catch(() => { valTurmas.textContent = '—'; });

  const valAulas   = document.getElementById('dash-val-aulas');
  const trendAulas = document.getElementById('dash-trend-aulas');
  if (valAulas) {
    fetch('aulas_temas_crud.php?recurso=aulas-stats')
      .then(r => r.json())
      .then(d => {
        if (!d.ok) return;
        valAulas.textContent = d.atual;
        const diff  = d.diff;
        const label = (diff === 0 ? 'igual ao' : (diff > 0 ? '+' + diff + ' vs' : diff + ' vs')) + ' mês anterior';
        trendAulas.className  = 'trend ' + (diff >= 0 ? 'trend-up' : 'trend-down');
        trendAulas.innerHTML  = (diff >= 0 ? svgUp : svgDown) + ' ' + label;
      })
      .catch(() => { valAulas.textContent = '—'; });
  }

  // Últimas matrículas
  const tbodyMat = document.getElementById('tbody-ultimas-matriculas');
  // Aniversariantes do mês
  const tbodyAniv = document.getElementById('tbody-aniversariantes');
  if (tbodyAniv) {
    const nomeMes = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                     'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const mesAtual = new Date().getMonth(); // 0-based
    const badge = document.getElementById('dash-aniv-mes');
    if (badge) badge.textContent = nomeMes[mesAtual] + ' ' + new Date().getFullYear();
    fetch('alunos_crud.php?aniversariantes=1')
      .then(r => r.json())
      .then(d => {
        if (!d.ok || !d.alunos.length) {
          tbodyAniv.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Nenhum aniversariante este mês.</td></tr>';
          return;
        }
        const hoje = new Date().getDate();
        tbodyAniv.innerHTML = d.alunos.map(a => {
          const [y, m, dia] = a.data_nascimento.split('-');
          const diaNum = parseInt(dia, 10);
          const isHoje = diaNum === hoje;
          const dataFmt = dia + '/' + m;
          const anoLabel = y ? '<small style="color:var(--color-text-muted)"> (' + (new Date().getFullYear() - parseInt(y,10)) + ' anos)</small>' : '';
          const turma = a.turma || '<span style="color:var(--color-text-muted)">—</span>';
          const destaque = isHoje ? 'background:var(--color-warning-light,#fef9c3)' : '';
          return `<tr style="${destaque}">
            <td><strong>${a.nome}</strong>${isHoje ? ' 🎂' : ''}</td>
            <td>${turma}</td>
            <td>${dataFmt}${anoLabel}</td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        tbodyAniv.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--color-text-muted)">Erro ao carregar.</td></tr>';
      });
  }
  if (tbodyMat) {
    const badgeCls = { ativo: 'badge-success', pendente: 'badge-warning', inativo: 'badge-danger' };
    const badgeLbl = { ativo: 'Ativo', pendente: 'Pendente', inativo: 'Inativo' };
    const fmtDate  = s => {
      if (!s) return '—';
      const [y, m, d] = s.split('-');
      return (d || '?') + '/' + (m || '?') + '/' + (y || '?');
    };
    fetch('alunos_crud.php?recentes=5')
      .then(r => r.json())
      .then(d => {
        if (!d.ok || !d.alunos.length) {
          tbodyMat.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Nenhum aluno cadastrado ainda.</td></tr>';
          return;
        }
        tbodyMat.innerHTML = d.alunos.map(a => {
          const st  = (a.status || 'pendente').toLowerCase();
          const cls = badgeCls[st] || 'badge-secondary';
          const lbl = badgeLbl[st] || a.status;
          const email = a.usuario_email
            ? '<br><small class="text-muted">' + a.usuario_email + '</small>'
            : '';
          const turma = a.turma || '<span style="color:var(--color-text-muted)">—</span>';
          return `<tr>
            <td><strong>${a.nome}</strong>${email}</td>
            <td>${turma}</td>
            <td>${fmtDate(a.data_matricula)}</td>
            <td><span class="badge ${cls}">${lbl}</span></td>
          </tr>`;
        }).join('');
      })
      .catch(() => {
        tbodyMat.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--color-text-muted)">Erro ao carregar.</td></tr>';
      });
  }
})();

// ══════════════════════════════════════════════════
//  DASHBOARD — Ranking Aula na Prática
// ══════════════════════════════════════════════════
(function() {
  const body = document.getElementById('dash-ranking-pratica-body');
  const selAno = document.getElementById('dash-ranking-ano');
  if (!body || !selAno) return;

  const medalhas = ['🥇','🥈','🥉'];

  function carregarRanking(ano) {
    body.innerHTML = skeletonBars(5);
    fetch('aula_pratica_crud.php?recurso=ranking-dashboard&ano=' + ano)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          body.innerHTML = '<div style="padding:var(--space-4);color:var(--color-danger);font-size:var(--text-sm)">' + (data.msg || 'Erro ao carregar.') + '</div>';
          return;
        }

        /* Popula o select de anos na primeira carga */
        if (data.anos && data.anos.length) {
          const valorAtual = selAno.value;
          selAno.innerHTML = '';
          data.anos.forEach(function(a) {
            const opt = document.createElement('option');
            opt.value = a;
            opt.textContent = a;
            if (String(a) === String(valorAtual)) opt.selected = true;
            selAno.appendChild(opt);
          });
        }

        if (!data.ranking.length) {
          body.innerHTML = '<div style="padding:var(--space-8);text-align:center;color:var(--color-text-muted)">' +
            '<svg style="width:32px;height:32px;fill:currentColor;opacity:.3;margin:0 auto var(--space-2);display:block" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>' +
            'Nenhuma pontuação registrada em ' + ano + '.</div>';
          return;
        }

        const maxPts = parseInt(data.ranking[0].total_pontos) || 1;
        body.innerHTML = data.ranking.map(function(r, i) {
          const barra = Math.round((r.total_pontos / maxPts) * 100);
          const medal = medalhas[i] || '<span style="font-size:.75rem;color:var(--color-text-muted)">' + (i+1) + 'º</span>';
          const turma = r.turma ? '<span style="font-size:var(--text-xs);color:var(--color-text-muted)">' + r.turma + '</span>' : '';
          return '<div class="ap-rank-item">' +
            '<div class="ap-rank-pos">' + medal + '</div>' +
            '<div class="ap-rank-info">' +
              '<div style="display:flex;align-items:baseline;gap:var(--space-2)">' +
                '<div class="ap-rank-nome">' + r.aluno_nome + '</div>' + turma +
              '</div>' +
              '<div class="ap-rank-bar-wrap"><div class="ap-rank-bar" style="width:' + barra + '%"></div></div>' +
              '<div class="ap-rank-detalhe">' + r.total_respostas + ' resposta' + (r.total_respostas != 1 ? 's' : '') + ' &nbsp;·&nbsp; ' +
                r.qtd_sem_leitura + '× sem leitura &nbsp;·&nbsp; ' + r.qtd_com_leitura + '× com leitura' +
              '</div>' +
            '</div>' +
            '<div class="ap-rank-pts">' + r.total_pontos + ' <small>pts</small></div>' +
          '</div>';
        }).join('');
      })
      .catch(function() {
        body.innerHTML = '<div style="padding:var(--space-4);color:var(--color-danger);font-size:var(--text-sm)">Erro de conexão.</div>';
      });
  }

  selAno.addEventListener('change', function() { carregarRanking(selAno.value); });
  carregarRanking(selAno.value);
})();

// ══════════════════════════════════════════════════
//  DASHBOARD — Frequência por Turma
// ══════════════════════════════════════════════════
(function() {
  const body      = document.getElementById('dash-freq-body');
  const selTri    = document.getElementById('dash-freq-trimestre');
  const selAnoF   = document.getElementById('dash-freq-ano');
  if (!body || !selTri || !selAnoF) return;

  function carregarFreqDash() {
    const tri = selTri.value;
    const ano = selAnoF.value;
    body.innerHTML = skeletonBars(4);
    fetch('frequencia_crud.php?recurso=dashboard-freq&ano=' + ano + '&trimestre=' + tri)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data.ok) {
          body.innerHTML = '<div style="padding:var(--space-4);color:var(--color-danger);font-size:var(--text-sm)">' + (data.msg || 'Erro.') + '</div>';
          return;
        }

        /* Popula select de anos na primeira carga */
        if (data.anos && data.anos.length && selAnoF.options.length <= 1) {
          const valAtual = selAnoF.value;
          selAnoF.innerHTML = '';
          data.anos.forEach(function(a) {
            const opt = document.createElement('option');
            opt.value = a; opt.textContent = a;
            if (String(a) === String(valAtual)) opt.selected = true;
            selAnoF.appendChild(opt);
          });
        }

        if (!data.turmas || !data.turmas.length) {
          body.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">Nenhuma turma encontrada.</div>';
          return;
        }

        /* Filtra turmas que têm pelo menos uma aula no período */
        const ativas = data.turmas.filter(function(t) { return t.total_aulas > 0; });

        if (!ativas.length) {
          const label = selTri.options[selTri.selectedIndex].text;
          body.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">Sem aulas registradas para ' + label + ' de ' + selAnoF.value + '.</div>';
          return;
        }

        body.innerHTML = ativas.map(function(t) {
          const pct     = t.pct;
          const cor     = pct >= 75 ? 'var(--color-success,#16a34a)' : pct >= 50 ? 'var(--color-warning,#d97706)' : 'var(--color-danger,#dc2626)';
          const barCor  = pct >= 75 ? '' : pct >= 50 ? 'background-color:var(--color-warning,#d97706)' : 'background-color:var(--color-danger,#dc2626)';
          const info    = t.total_alunos + ' aluno' + (t.total_alunos !== 1 ? 's' : '') +
                          ' · ' + t.total_aulas + ' aula' + (t.total_aulas !== 1 ? 's' : '');
          return '<div style="margin-bottom:var(--space-4)">' +
            '<div style="display:flex;justify-content:space-between;align-items:baseline;font-size:var(--text-sm);margin-bottom:2px">' +
              '<div>' +
                '<span style="font-weight:500">' + t.nome_turma + '</span>' +
                '<span style="font-size:var(--text-xs);color:var(--color-text-muted);margin-left:var(--space-2)">' + info + '</span>' +
              '</div>' +
              '<strong style="color:' + cor + ';white-space:nowrap;margin-left:var(--space-2)">' + pct + '%</strong>' +
            '</div>' +
            '<div class="progress-bar">' +
              '<div class="progress-bar__fill" style="width:' + pct + '%;' + barCor + '"></div>' +
            '</div>' +
          '</div>';
        }).join('');
      })
      .catch(function() {
        body.innerHTML = '<div style="padding:var(--space-4);color:var(--color-danger);font-size:var(--text-sm)">Erro de conexão.</div>';
      });
  }

  selTri.addEventListener('change', carregarFreqDash);
  selAnoF.addEventListener('change', carregarFreqDash);
  carregarFreqDash();
})();

// ══════════════════════════════════════════════════
//  DASHBOARD — Aulas do Próximo Domingo
// ══════════════════════════════════════════════════
(function() {
  const lista = document.getElementById('dash-domingo-lista');
  if (!lista) return;

  // Calcula a data do próximo domingo (ou hoje se já for domingo)
  function proximoDomingo() {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    const diasAte = (7 - hoje.getDay()) % 7; // 0 = hoje é domingo
    const dom = new Date(hoje);
    dom.setDate(hoje.getDate() + diasAte);
    return dom;
  }

  function fmtDateBR(d) {
    return String(d.getDate()).padStart(2,'0') + '/' +
           String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
  }
  function fmtDateISO(d) {
    return d.getFullYear() + '-' +
           String(d.getMonth()+1).padStart(2,'0') + '-' +
           String(d.getDate()).padStart(2,'0');
  }

  const TRIM_COR = { '1':'var(--trim1-cor)','2':'var(--trim2-cor)','3':'var(--trim3-cor)','4':'var(--trim4-cor)' };
  const TRIM_BG  = { '1':'var(--trim1-bg)','2':'var(--trim2-bg)','3':'var(--trim3-bg)','4':'var(--trim4-bg)' };

  const domingo = proximoDomingo();
  const eHoje   = domingo.getDay() === new Date().getDay() && domingo.toDateString() === new Date().toDateString();

  // Atualiza badge
  const badge = document.getElementById('dash-domingo-data');
  if (badge) badge.textContent = (eHoje ? 'Hoje · ' : '') + fmtDateBR(domingo);

  fetch('aulas_temas_crud.php?recurso=aulas-data&data=' + fmtDateISO(domingo))
    .then(r => r.json())
    .then(data => {
      if (!data.ok) { lista.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Erro ao carregar aulas.</div>'; return; }
      if (!data.aulas.length) {
        lista.innerHTML = '<div style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
          '<svg style="width:28px;height:28px;fill:currentColor;display:block;margin:0 auto var(--space-2)" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>' +
          'Nenhuma aula cadastrada para este domingo.</div>';
        return;
      }
      lista.innerHTML = data.aulas.map(a => {
        const cor  = TRIM_COR[a.trimestre] || 'var(--color-primary)';
        const corBg = TRIM_BG[a.trimestre] || 'var(--color-primary-light)';
        return `<div style="display:flex;gap:var(--space-3);padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);align-items:flex-start">
          <span style="width:6px;height:6px;border-radius:50%;background:${cor};flex-shrink:0;margin-top:6px"></span>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:var(--text-sm);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(a.titulo)}</div>
            <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px">
              ${a.nome_turma ? `<span style="margin-right:6px">📚 ${esc(a.nome_turma)}</span>` : ''}
              ${a.professor  ? `<span>🎓 ${esc(a.professor)}</span>` : ''}
            </div>
            <div style="margin-top:4px">
              <span class="badge" style="background:${corBg};color:${cor};font-size:10px">${esc(a.tema_titulo)}</span>
            </div>
          </div>
        </div>`;
      }).join('');
    })
    .catch(() => { lista.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Falha na comunicação.</div>'; });
})();

// ══════════════════════════════════════════════════
//  DASHBOARD — Próximos Compromissos
// ══════════════════════════════════════════════════
(function() {
  const container = document.getElementById('dash-proximos-lista');
  if (!container) return;

  const CAT_COLOR = {
    geral:   { bg: 'var(--cat-geral-bg)',   text: 'var(--cat-geral-text)',   dot: 'var(--cat-geral-dot)' },
    aula:    { bg: 'var(--cat-aula-bg)',    text: 'var(--cat-aula-text)',    dot: 'var(--cat-aula-dot)' },
    evento:  { bg: 'var(--cat-evento-bg)',  text: 'var(--cat-evento-text)',  dot: 'var(--cat-evento-dot)' },
    reuniao: { bg: 'var(--cat-reuniao-bg)', text: 'var(--cat-reuniao-text)', dot: 'var(--cat-reuniao-dot)' },
    urgente: { bg: 'var(--cat-urgente-bg)', text: 'var(--cat-urgente-text)', dot: 'var(--cat-urgente-dot)' },
  };

  const CAT_LABEL = {
    geral: 'Geral', aula: 'Aula', evento: 'Evento', reuniao: 'Reunião', urgente: 'Urgente'
  };

  function diasAte(dateStr) {
    const hoje = new Date(); hoje.setHours(0,0,0,0);
    const ev   = new Date(dateStr + 'T00:00:00');
    const diff = Math.round((ev - hoje) / 86400000);
    if (diff === 0) return '<span style="color:var(--color-primary);font-weight:600">Hoje</span>';
    if (diff === 1) return '<span style="color:var(--color-warning);font-weight:600">Amanhã</span>';
    return 'em ' + diff + ' dias';
  }

  fetch('calendario_crud.php?proximos=30')
    .then(r => r.json())
    .then(data => {
      if (!data.ok || !data.eventos.length) {
        container.innerHTML = '<div style="padding:var(--space-4) 0;display:flex;align-items:center;gap:var(--space-3);color:var(--color-text-muted)">'
          + '<svg style="width:20px;height:20px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>'
          + 'Nenhum compromisso nos próximos 30 dias.</div>';
        return;
      }

      container.innerHTML = data.eventos.map(ev => {
        const c    = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
        const hora = ev.hora_inicio ? ' · ' + fmtHora(ev.hora_inicio) : '';
        const dur  = (ev.hora_inicio && ev.hora_fim) ? ' – ' + fmtHora(ev.hora_fim) : '';
        return `<div class="dash-compromisso-item">
          <div class="dash-comp-dot" style="background:${c.dot}"></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-gray-800)">${escH(ev.titulo)}</div>
            <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:1px">
              ${fmtDataBR(ev.data_evento)}${hora}${dur}
            </div>
            ${ev.descricao ? `<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.descricao)}</div>` : ''}
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0">
            <span class="badge" style="background:${c.bg};color:${c.text}">${CAT_LABEL[ev.categoria] || ev.categoria}</span>
            <span style="font-size:var(--text-xs);color:var(--color-text-muted)">${diasAte(ev.data_evento)}</span>
          </div>
        </div>`;
      }).join('');
    })
    .catch(() => {
      container.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Erro ao carregar compromissos.</div>';
    });
})();
