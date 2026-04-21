// ══════════════════════════════════════════════════
//  CERTIFICADOS — Trimestral & Anual
// ══════════════════════════════════════════════════
(function(){
  if(!document.getElementById('cert-turma')) return;

  const API = 'certificados_crud.php';
  let certData = null;

  /* ── Placeholder inicial ── */
  document.getElementById('cert-container').innerHTML =
    '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
    '<svg style="width:40px;height:40px;fill:currentColor;margin:0 auto var(--space-3);display:block;opacity:.3" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' +
    '<p style="margin:0">Configure os filtros acima e clique em <strong>Consultar</strong>.</p>' +
    '</div></div>';

  /* ── Carregar selects ── */
  fetch(API+'?recurso=turmas').then(function(r){return r.json();}).then(function(data){
    var s = document.getElementById('cert-turma');
    s.innerHTML = '<option value="">Selecione…</option>';
    (data.turmas||[]).forEach(function(t){
      var o = document.createElement('option');
      o.value = t.id; o.textContent = t.nome_turma;
      s.appendChild(o);
    });
  });

  fetch(API+'?recurso=anos').then(function(r){return r.json();}).then(function(data){
    var s = document.getElementById('cert-ano');
    s.innerHTML = '';
    (data.anos||[]).forEach(function(a){
      var o = document.createElement('option');
      o.value = a; o.textContent = a;
      s.appendChild(o);
    });
  });

  /* ── Toggle trimestre ── */
  var selTipo = document.getElementById('cert-tipo');
  var triWrap = document.getElementById('cert-tri-wrap');
  selTipo.addEventListener('change', function(){
    triWrap.style.display = this.value === 'trimestral' ? '' : 'none';
  });

  /* ── Consultar ── */
  document.getElementById('btnCertGerar').addEventListener('click', consultar);

  function consultar(){
    var turma = document.getElementById('cert-turma').value;
    var ano   = document.getElementById('cert-ano').value;
    var tipo  = selTipo.value;
    var tri   = document.getElementById('cert-trimestre').value;

    if(!turma){ showAlert('Selecione uma turma.','warning'); return; }

    showAlert('',null);
    document.getElementById('cert-loading').style.display = '';
    document.getElementById('cert-container').innerHTML = '';

    var url = API+'?recurso=dados&turma_id='+turma+'&ano='+ano+'&tipo='+tipo;
    if(tipo==='trimestral') url += '&trimestre='+tri;

    fetch(url).then(function(r){return r.json();}).then(function(json){
      document.getElementById('cert-loading').style.display = 'none';
      if(!json.ok){ showAlert(json.msg||'Erro ao consultar.','danger'); return; }
      certData = json;
      renderResultados(json);
    }).catch(function(){
      document.getElementById('cert-loading').style.display = 'none';
      showAlert('Falha na comunicação com o servidor.','danger');
    });
  }

  /* ── Render tabela de resultados ── */
  function renderResultados(data){
    var alunos = data.alunos || [];
    var html = '<div class="card">';
    html += '<div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3)">';
    html += '<span class="card-title">' + esc(data.turma) + ' — ' + esc(data.periodo) + ' (' + alunos.length + ' aluno' + (alunos.length!==1?'s':'') + ')</span>';
    html += '</div>';

    html += '<div style="overflow-x:auto"><table class="cert-table">';
    html += '<thead><tr>';
    html += '<th>#</th><th>Aluno</th><th>Presença</th><th>Frequência</th><th>Pontuação</th><th>Conceito</th><th>Desempenho</th><th style="text-align:center">Certificado</th>';
    html += '</tr></thead><tbody>';

    alunos.forEach(function(al, i){
      var freqLabel = al.pct_freq !== null ? al.pct_freq + '%' : '—';
      var freqColor = corPct(al.pct_freq);
      html += '<tr>';
      html += '<td>' + (i+1) + '</td>';
      html += '<td>' + esc(al.aluno_nome) + '</td>';
      html += '<td>' + al.presencas + '/' + al.total_aulas + '</td>';
      html += '<td style="color:' + freqColor + ';font-weight:600">' + freqLabel + '</td>';
      html += '<td>' + al.total_pontos + ' pts <span style="color:var(--color-text-muted);font-size:var(--text-xs)">(' + al.total_respostas + ' resp.)</span></td>';
      html += '<td><span class="cert-conceito cert-conceito--' + al.conceito + '">' + al.conceito + '</span></td>';
      html += '<td>' + esc(al.desempenho) + '</td>';
      html += '<td style="text-align:center"><button class="btn btn-primary" style="padding:4px 12px;font-size:var(--text-xs)" data-cert="'+i+'">';
      html += '<svg style="width:14px;height:14px;fill:currentColor;vertical-align:-2px;margin-right:4px" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>';
      html += 'Gerar</button></td>';
      html += '</tr>';
    });

    html += '</tbody></table></div></div>';
    document.getElementById('cert-container').innerHTML = html;

    /* Botões individuais de certificado */
    document.querySelectorAll('[data-cert]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var idx = parseInt(this.getAttribute('data-cert'));
        abrirCertificado(alunos[idx]);
      });
    });
  }

  /* ── Gerar certificado visual ── */
  function abrirCertificado(aluno){
    var d = certData;
    var hoje = new Date();
    var dataStr = hoje.toLocaleDateString('pt-BR', {day:'2-digit',month:'long',year:'numeric'});

    var freqLabel = aluno.pct_freq !== null ? aluno.pct_freq + '%' : 'N/D';

    var html = '<div class="cert-print">';
    html += '<div class="cert-print__border">';
    html += '<div class="cert-print__watermark">CERTIFICADO</div>';

    /* Título */
    html += '<div class="cert-print__title">Certificado de Conclusão</div>';
    html += '<div class="cert-print__subtitle">Escola Bíblica Dominical</div>';

    /* Corpo */
    html += '<div class="cert-print__body">';
    html += 'Certificamos que';
    html += '<div class="cert-print__name">' + esc(aluno.aluno_nome) + '</div>';
    html += 'concluiu com ' + (aluno.desempenho === 'Excelente' ? 'excelente' : aluno.desempenho === 'Bom' ? 'bom' : aluno.desempenho === 'Regular' ? 'regular' : 'insuficiente') + ' desempenho';
    html += ' o período letivo referente ao<br><strong>' + esc(d.periodo) + '</strong>';
    html += ' na turma <strong>' + esc(d.turma) + '</strong>.';
    html += '</div>';

    /* Estatísticas */
    html += '<div class="cert-print__stats">';
    html += '<div class="cert-print__stat"><div class="cert-print__stat-value">' + freqLabel + '</div><div class="cert-print__stat-label">Frequência</div></div>';
    html += '<div class="cert-print__stat"><div class="cert-print__stat-value">' + aluno.total_pontos + '</div><div class="cert-print__stat-label">Pontuação</div></div>';
    html += '<div class="cert-print__stat"><div class="cert-print__stat-value">' + aluno.conceito + '</div><div class="cert-print__stat-label">Conceito</div></div>';
    html += '<div class="cert-print__stat"><div class="cert-print__stat-value">' + esc(aluno.desempenho) + '</div><div class="cert-print__stat-label">Desempenho</div></div>';
    html += '</div>';

    /* Assinaturas */
    html += '<div class="cert-print__footer">';
    html += '<div class="cert-print__sign"><div class="cert-print__sign-line"></div><div class="cert-print__sign-label">Superintendente</div></div>';
    html += '<div class="cert-print__sign"><div class="cert-print__sign-line"></div><div class="cert-print__sign-label">Professor(a)</div></div>';
    html += '</div>';

    html += '<div class="cert-print__date">' + esc(dataStr) + '</div>';

    html += '</div>'; // border
    html += '</div>'; // cert-print

    document.getElementById('cert-print-area').innerHTML = html;
    document.getElementById('cert-modal').style.display = '';
  }

  /* ── Modal: Imprimir ── */
  document.getElementById('btnCertPrint').addEventListener('click', function(){
    window.print();
  });

  /* ── Modal: Fechar ── */
  document.getElementById('btnCertFechar').addEventListener('click', fecharModal);
  document.getElementById('cert-modal').addEventListener('click', function(e){
    if(e.target === this) fecharModal();
  });
  function fecharModal(){
    document.getElementById('cert-modal').style.display = 'none';
  }

  /* ── Alerta helper ── */
  function showAlert(msg, type){
    var el = document.getElementById('cert-alert');
    if(!msg){ el.style.display='none'; return; }
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = '';
  }

})();
