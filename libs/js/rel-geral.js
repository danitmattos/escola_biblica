// ══════════════════════════════════════════════════
//  RELATÓRIO — FREQUÊNCIA GERAL
// ══════════════════════════════════════════════════
(function(){
  if(!document.getElementById('rg-ano')) return;
  const API = 'frequencia_crud.php';
  const TRI_LABEL = {1:'1º Tri',2:'2º Tri',3:'3º Tri',4:'4º Tri'};
  const TRI_FULL  = {1:'1º Trimestre',2:'2º Trimestre',3:'3º Trimestre',4:'4º Trimestre'};
  let dadosCarregados = null;

  function bgPct(p) {
    if(p===null) return '';
    if(p>=75) return 'background:var(--freq-high-bg)';
    if(p>=50) return 'background:var(--freq-mid-bg)';
    return 'background:var(--freq-low-bg)';
  }

  /* Carrega anos */
  fetch(API+'?recurso=anos').then(r=>r.json()).then(data=>{
    if(!data.ok) return;
    const sel = document.getElementById('rg-ano');
    data.anos.forEach(a=>{
      const o=document.createElement('option'); o.value=a; o.textContent=a; sel.appendChild(o);
    });
    gerarRg();
  });

  function gerarRg(){
    const ano = document.getElementById('rg-ano').value;
    document.getElementById('rg-loading').style.display='block';
    document.getElementById('rg-container').innerHTML='';
    document.getElementById('btnRgPdf').style.display='none';
    document.getElementById('btnRgXls').style.display='none';
    fetch(API+'?recurso=rel-geral&ano='+ano).then(r=>r.json()).then(data=>{
      document.getElementById('rg-loading').style.display='none';
      if(!data.ok){ document.getElementById('rg-alert').style.display='block'; document.getElementById('rg-alert').textContent=data.msg; return; }
      dadosCarregados=data;
      renderRg(data);
    }).catch(()=>{ document.getElementById('rg-loading').style.display='none'; });
  }

  function renderRg(data){
    const c = document.getElementById('rg-container');
    if(!data.dados.length){ c.innerHTML='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">Nenhuma aula com sessão encontrada para este ano.</div></div>'; return; }
    document.getElementById('btnRgPdf').style.display='inline-flex';
    document.getElementById('btnRgXls').style.display='inline-flex';

    /* Cards de resumo */
    const totalTurmas = data.dados.length;
    const pctGlobal   = data.pct_global;
    const mediaTurmas = data.dados.filter(d=>d.pct_geral!==null).reduce((s,d)=>s+d.pct_geral,0) / (data.dados.filter(d=>d.pct_geral!==null).length||1);
    const melhor = data.dados.reduce((m,d)=> d.pct_geral!==null&&(m===null||d.pct_geral>m.pct_geral)?d:m, null);
    const pior   = data.dados.reduce((m,d)=> d.pct_geral!==null&&(m===null||d.pct_geral<m.pct_geral)?d:m, null);

    let html = '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:var(--space-4);margin-bottom:var(--space-6)">';
    html += cartao('Média Global', (pctGlobal!==null?pctGlobal+'%':'—'), corPct(pctGlobal));
    html += cartao('Turmas Avaliadas', totalTurmas, 'var(--color-primary)');
    html += cartao('Melhor Turma', melhor?(melhor.nome_turma+' ('+melhor.pct_geral+'%)') :'—', 'var(--color-success,#16a34a)');
    html += cartao('Turma em Alerta', pior?(pior.nome_turma+' ('+pior.pct_geral+'%)') :'—', 'var(--color-danger,#dc2626)');
    html += '</div>';

    /* Tabela comparativa */
    html += '<div class="table-wrapper" id="rg-tabela"><table class="table">';
    html += '<thead><tr>';
    html += '<th style="min-width:160px">Turma</th>';
    html += '<th style="text-align:center">Alunos</th>';
    data.trimestres.forEach(tri=>{
      html += '<th style="text-align:center">'+TRI_FULL[tri]+'</th>';
    });
    html += '<th style="text-align:center;background:var(--color-green-50,#f0fdf4)">Geral</th>';
    html += '</tr></thead><tbody>';

    data.dados.forEach((turma,i)=>{
      const bg = i%2===1?'background:var(--color-gray-50)':'';
      html += '<tr style="'+bg+'">';
      html += '<td style="font-weight:500">'+escHtml(turma.nome_turma)+'</td>';
      html += '<td style="text-align:center">'+turma.total_alunos+'</td>';
      data.trimestres.forEach(tri=>{
        const td = turma.por_trimestre[tri];
        if(!td||td.pct===null){
          html += '<td style="text-align:center;color:var(--color-text-muted)">—</td>';
        } else {
          html += '<td style="text-align:center;'+bgPct(td.pct)+'">';
          html += '<strong style="color:'+corPct(td.pct)+'">'+td.pct+'%</strong>';
          html += '<div style="font-size:10px;color:var(--color-text-muted)">'+td.presencas+'/'+td.possivel+'</div>';
          html += '</td>';
        }
      });
      const pg = turma.pct_geral;
      html += '<td style="text-align:center;font-weight:700;background:var(--color-green-50,#f0fdf4);color:'+corPct(pg)+'">'+( pg!==null ? pg+'%' : '—' )+'</td>';
      html += '</tr>';
    });
    html += '</tbody></table></div>';

    /* Barras por turma */
    html += '<div class="card" style="margin-top:var(--space-6)"><div class="card-header"><span class="card-title">Frequência Geral por Turma</span></div><div class="card-body">';
    data.dados.forEach(turma=>{
      const p = turma.pct_geral ?? 0;
      const barCor = p>=75?'':( p>=50?'background-color:var(--color-warning,#d97706)':'background-color:var(--color-danger,#dc2626)' );
      html += '<div style="margin-bottom:var(--space-4)">';
      html += '<div style="display:flex;justify-content:space-between;font-size:var(--text-sm);margin-bottom:2px">';
      html += '<span style="font-weight:500">'+escHtml(turma.nome_turma)+'</span>';
      html += '<strong style="color:'+corPct(turma.pct_geral)+'">'+( turma.pct_geral!==null ? turma.pct_geral+'%' : '—' )+'</strong>';
      html += '</div>';
      html += '<div class="progress-bar"><div class="progress-bar__fill" style="width:'+p+'%;'+barCor+'"></div></div>';
      html += '</div>';
    });
    html += '</div></div>';

    c.innerHTML = html;
  }

  function cartao(label, valor, cor){
    return '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-4)">'+
      '<div style="font-size:var(--text-xl);font-weight:700;color:'+cor+'">'+valor+'</div>'+
      '<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:4px">'+label+'</div>'+
      '</div></div>';
  }

  document.getElementById('btnRgGerar').addEventListener('click', gerarRg);

  document.getElementById('btnRgPdf').addEventListener('click', ()=>{
    if(!dadosCarregados) return;
    const data = dadosCarregados;
    const ano  = document.getElementById('rg-ano').value;

    const s = v => esc(v);

    /* Summary cards */
    const pctGlobal = data.pct_global;
    const melhor = data.dados.reduce((m,d)=> d.pct_geral!==null&&(m===null||d.pct_geral>m.pct_geral)?d:m, null);
    const pior   = data.dados.reduce((m,d)=> d.pct_geral!==null&&(m===null||d.pct_geral<m.pct_geral)?d:m, null);
    const pCorG = pctGlobal!==null ? _corPdf(pctGlobal) : '#9ca3af';

    let body = '<div class="summary-cards">';
    body += '<div class="s-card"><div class="s-card-val" style="color:'+pCorG+'">'+(pctGlobal!==null?pctGlobal+'%':'—')+'</div><div class="s-card-lbl">Média Global</div></div>';
    body += '<div class="s-card"><div class="s-card-val" style="color:#1E40AF">'+data.dados.length+'</div><div class="s-card-lbl">Turmas Avaliadas</div></div>';
    body += '<div class="s-card"><div class="s-card-val" style="color:#16a34a">'+(melhor?s(melhor.nome_turma)+' ('+melhor.pct_geral+'%)':'—')+'</div><div class="s-card-lbl">Melhor Turma</div></div>';
    body += '<div class="s-card"><div class="s-card-val" style="color:#dc2626">'+(pior?s(pior.nome_turma)+' ('+pior.pct_geral+'%)':'—')+'</div><div class="s-card-lbl">Turma em Alerta</div></div>';
    body += '</div>';

    /* Table */
    body += '<table><thead><tr><th>Turma</th><th>Alunos</th>';
    data.trimestres.forEach(tri=>{ body += '<th>'+TRI_FULL[tri]+'</th>'; });
    body += '<th style="background:#166534">Geral</th></tr></thead><tbody>';
    data.dados.forEach(turma=>{
      body += '<tr><td style="font-weight:500">'+s(turma.nome_turma)+'</td><td>'+turma.total_alunos+'</td>';
      data.trimestres.forEach(tri=>{
        const td = turma.por_trimestre[tri];
        if(!td||td.pct===null){
          body += '<td style="color:#9ca3af">—</td>';
        } else {
          body += '<td><strong style="color:'+_corPdf(td.pct)+'">'+td.pct+'%</strong><div style="font-size:9px;color:#6b7280">'+td.presencas+'/'+td.possivel+'</div></td>';
        }
      });
      const pg = turma.pct_geral;
      body += '<td style="font-weight:700;color:'+_corPdf(pg)+'">'+(pg!==null?pg+'%':'—')+'</td></tr>';
    });
    body += '</tbody></table>';

    /* Bars */
    body += '<div style="margin-top:14px">';
    data.dados.forEach(turma=>{
      const p = turma.pct_geral ?? 0;
      const c = _corPdf(p);
      body += '<div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:2px"><span style="font-weight:500">'+s(turma.nome_turma)+'</span><strong style="color:'+c+'">'+(turma.pct_geral!==null?turma.pct_geral+'%':'—')+'</strong></div>';
      body += '<div class="bar-wrap"><div class="bar-fill" style="width:'+p+'%;background:'+c+'"></div></div></div>';
    });
    body += '</div>';

    pdfOpen({
      title: 'Frequência Geral — ' + ano,
      subtitle: 'Comparativo de frequência por turma e trimestre<br>Ano: <strong>' + ano + '</strong>',
      body: body,
      css: '.bar-wrap{height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden}.bar-fill{height:100%;border-radius:4px}'
    });
  });

  document.getElementById('btnRgXls').addEventListener('click', ()=>{
    if(!dadosCarregados) return;
    const data = dadosCarregados;
    const ano  = document.getElementById('rg-ano').value;
    const TF = {1:'1º Trimestre',2:'2º Trimestre',3:'3º Trimestre',4:'4º Trimestre'};

    let colDefs = '<Column ss:Width="160"/><Column ss:Width="60"/>';
    data.trimestres.forEach(()=>{ colDefs += '<Column ss:Width="90"/>'; });
    colDefs += '<Column ss:Width="70"/>';

    let rows = '<Row ss:Height="26">' + xlsCell('Turma','hdr') + xlsCell('Alunos','hdr');
    data.trimestres.forEach(tri=>{ rows += xlsCell(TF[tri]||tri+'º Tri','hdr'); });
    rows += xlsCell('Geral','tot') + '</Row>\n';

    data.dados.forEach(turma=>{
      rows += '<Row ss:Height="18">' + xlsCell(turma.nome_turma) + xlsCell(turma.total_alunos);
      data.trimestres.forEach(tri=>{
        const td = turma.por_trimestre[tri];
        if(!td||td.pct===null){
          rows += xlsCell('—','aus');
        } else {
          rows += xlsCell(td.pct+'%  ('+td.presencas+'/'+td.possivel+')','sub');
        }
      });
      const pg = turma.pct_geral;
      rows += xlsCell(pg!==null?pg+'%':'—','tot') + '</Row>\n';
    });

    const xml = xlsWrap(XLS_STY.hdr+XLS_STY.sub+XLS_STY.tot+XLS_STY.aus, 'Frequência Geral', colDefs, rows);
    xlsDownload(xml, 'freq_geral_'+ano);
  });

  function _corPdf(p) {
    if(p===null||p===undefined) return '#9ca3af';
    if(p>=75) return '#16a34a';
    if(p>=50) return '#d97706';
    return '#dc2626';
  }
})();
