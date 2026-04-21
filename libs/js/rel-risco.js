// ══════════════════════════════════════════════════
//  RELATÓRIO — ALUNOS EM RISCO
// ══════════════════════════════════════════════════
(function(){
  if(!document.getElementById('rr-ano')) return;
  const API='frequencia_crud.php';
  let rrData=null, rrLimiar=75;
  document.getElementById('rr-container').innerHTML =
    '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
    '<svg style="width:40px;height:40px;fill:currentColor;margin:0 auto var(--space-3);display:block;opacity:.3" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>' +
    '<p style="margin:0">Configure os filtros acima e clique em <strong>Gerar</strong>.</p>' +
    '</div></div>';

  fetch(API+'?recurso=anos').then(r=>r.json()).then(data=>{
    const s=document.getElementById('rr-ano');
    const anos = data.anos||[];
    anos.forEach(a=>{ const o=document.createElement('option'); o.value=a; o.textContent=a; s.appendChild(o); });
    const anoCorrente = new Date().getFullYear().toString();
    if(s.options.length > 1 && s.options[0].value === anoCorrente) {
      s.selectedIndex = 1;
    }
  });
  fetch(API+'?recurso=turmas').then(r=>r.json()).then(data=>{
    const s=document.getElementById('rr-turma');
    (data.turmas||[]).forEach(t=>{ const o=document.createElement('option'); o.value=t.id; o.textContent=t.nome_turma; s.appendChild(o); });
  });

  function gerarRr(){
    const ano      = document.getElementById('rr-ano').value;
    const tri      = document.getElementById('rr-trimestre').value;
    const turma_id = document.getElementById('rr-turma').value;
    const limiar   = parseInt(document.getElementById('rr-limiar').value)||75;
    if(!ano){ alert('Selecione o ano.'); return; }
    const alertEl = document.getElementById('rr-alert');
    alertEl.style.display='none'; alertEl.textContent='';
    document.getElementById('rr-loading').style.display='block';
    document.getElementById('rr-container').innerHTML='';
    document.getElementById('btnRrPdf').style.display='none';
    document.getElementById('btnRrXls').style.display='none';
    fetch(API+'?recurso=rel-risco&ano='+encodeURIComponent(ano)+'&trimestre='+encodeURIComponent(tri)+'&turma_id='+encodeURIComponent(turma_id)+'&limiar='+encodeURIComponent(limiar))
      .then(r=>r.json()).then(data=>{
        document.getElementById('rr-loading').style.display='none';
        if(!data.ok){
          alertEl.textContent = data.msg || 'Erro ao carregar dados.';
          alertEl.style.display='block';
          return;
        }
        rrData=data; rrLimiar=limiar;
        renderRr(data, limiar);
      }).catch(e=>{
        document.getElementById('rr-loading').style.display='none';
        alertEl.textContent = 'Erro de comunicação com o servidor.';
        alertEl.style.display='block';
      });
  }

  function renderRr(data, limiar){
    const c=document.getElementById('rr-container');

    if(!data.alunos.length){
      if(!data.has_sessions){
        c.innerHTML='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">'+
          '<svg style="width:40px;height:40px;fill:currentColor;margin:0 auto var(--space-3);display:block;opacity:.4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'+
          '<p style="margin:0">Nenhuma aula com sessão registrada para este período.<br><small>Ajuste o ano, trimestre ou turma e clique em <strong>Gerar</strong>.</small></p>'+
          '</div></div>';
      } else {
        c.innerHTML='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8)">'+
          '<svg style="width:48px;height:48px;fill:var(--color-success,#16a34a);margin:0 auto var(--space-3);display:block" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'+
          '<p style="color:var(--color-success,#16a34a);font-weight:600;margin:0">Nenhum aluno abaixo de '+limiar+'% neste período!</p>'+
          '</div></div>';
      }
      return;
    }

    document.getElementById('btnRrPdf').style.display='inline-flex';
    document.getElementById('btnRrXls').style.display='inline-flex';

    let html='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:var(--space-4);margin-bottom:var(--space-4)">';
    html+='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-4)"><div style="font-size:2rem;font-weight:800;color:var(--color-danger,#dc2626)">'+data.alunos.length+'</div><div style="font-size:var(--text-xs);color:var(--color-text-muted)">Alunos em Risco</div></div></div>';
    const medPct=data.alunos.length>0?Math.round(data.alunos.reduce((s,a)=>s+a.pct,0)/data.alunos.length):0;
    html+='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-4)"><div style="font-size:2rem;font-weight:800;color:'+corPct(medPct)+'">'+medPct+'%</div><div style="font-size:var(--text-xs);color:var(--color-text-muted)">Média do Grupo</div></div></div>';
    const criticos=data.alunos.filter(a=>a.pct<50).length;
    html+='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-4)"><div style="font-size:2rem;font-weight:800;color:var(--color-danger,#dc2626)">'+criticos+'</div><div style="font-size:var(--text-xs);color:var(--color-text-muted)">Críticos (< 50%)</div></div></div>';
    html+='</div>';

    html+='<div class="table-wrapper" id="rr-tabela"><table class="table">';
    html+='<thead><tr><th>#</th><th>Aluno</th><th>Turma</th><th>Telefone</th><th style="text-align:center">Presenças</th><th style="text-align:center">Frequência</th><th style="text-align:center">Situação</th></tr></thead><tbody>';
    data.alunos.forEach((al,i)=>{
      const cor=corPct(al.pct);
      const situ = al.pct<50
        ? '<span class="badge badge-danger">Crítico</span>'
        : '<span class="badge badge-warning">Atenção</span>';
      html+='<tr>';
      html+='<td style="color:var(--color-text-muted)">'+(i+1)+'</td>';
      html+='<td style="font-weight:500"><a href="index.php?pagina=rel-aluno" style="color:inherit;text-decoration:none" onclick="sessionStorage.setItem(\'ra_aluno_id\',\''+al.id+'\')" title="Ver frequência individual">'+escHtml(al.nome)+'</a></td>';
      html+='<td>'+escHtml(al.turma)+'</td>';
      html+='<td>'+(al.telefone?escHtml(al.telefone):'—')+'</td>';
      html+='<td style="text-align:center">'+al.presencas+' / '+al.total_aulas+'</td>';
      html+='<td style="text-align:center"><strong style="color:'+cor+'">'+al.pct+'%</strong><div class="progress-bar" style="margin-top:4px"><div class="progress-bar__fill" style="width:'+al.pct+'%;'+(al.pct<75?'background-color:'+cor:'')+'"></div></div></td>';
      html+='<td style="text-align:center">'+situ+'</td>';
      html+='</tr>';
    });
    html+='</tbody></table></div>';
    c.innerHTML=html;
  }

  document.getElementById('btnRrGerar').addEventListener('click',gerarRr);

  document.getElementById('btnRrPdf').addEventListener('click',()=>{
    if(!rrData) return;
    const data=rrData, ano=document.getElementById('rr-ano').value;
    const s=v=>esc(v);
    const _c=p=>{if(p>=75)return'#16a34a';if(p>=50)return'#d97706';return'#dc2626';};
    const medPct=data.alunos.length>0?Math.round(data.alunos.reduce((a,b)=>a+b.pct,0)/data.alunos.length):0;
    const criticos=data.alunos.filter(a=>a.pct<50).length;
    let body='<div class="summary-cards">';
    body+='<div class="s-card"><div class="s-card-val" style="color:#dc2626">'+data.alunos.length+'</div><div class="s-card-lbl">Alunos em Risco</div></div>';
    body+='<div class="s-card"><div class="s-card-val" style="color:'+_c(medPct)+'">'+medPct+'%</div><div class="s-card-lbl">Média do Grupo</div></div>';
    body+='<div class="s-card"><div class="s-card-val" style="color:#dc2626">'+criticos+'</div><div class="s-card-lbl">Críticos (&lt; 50%)</div></div>';
    body+='</div>';
    body+='<table><thead><tr><th>#</th><th>Aluno</th><th>Turma</th><th>Telefone</th><th>Presenças</th><th>Frequência</th><th>Situação</th></tr></thead><tbody>';
    data.alunos.forEach((al,i)=>{
      const situ=al.pct<50?'<span class="badge badge-danger">Crítico</span>':'<span class="badge badge-warning">Atenção</span>';
      body+='<tr><td>'+(i+1)+'</td><td style="font-weight:500">'+s(al.nome)+'</td><td>'+s(al.turma)+'</td><td>'+(al.telefone?s(al.telefone):'—')+'</td><td>'+al.presencas+'/'+al.total_aulas+'</td><td><strong style="color:'+_c(al.pct)+'">'+al.pct+'%</strong></td><td>'+situ+'</td></tr>';
    });
    body+='</tbody></table>';
    pdfOpen({title:'Alunos em Risco — '+ano,subtitle:'Limiar: <strong>'+rrLimiar+'%</strong> — Total: <strong>'+data.alunos.length+'</strong> alunos',body:body});
  });

  document.getElementById('btnRrXls').addEventListener('click',()=>{
    if(!rrData) return;
    const data=rrData, ano=document.getElementById('rr-ano').value;
    let colDefs='<Column ss:Width="35"/><Column ss:Width="180"/><Column ss:Width="130"/><Column ss:Width="100"/><Column ss:Width="80"/><Column ss:Width="75"/><Column ss:Width="70"/>';
    let rows='<Row ss:Height="26">'+xlsCell('#','hdr')+xlsCell('Aluno','hdr')+xlsCell('Turma','hdr')+xlsCell('Telefone','hdr')+xlsCell('Presenças','hdr')+xlsCell('Frequência','hdr')+xlsCell('Situação','hdr')+'</Row>\n';
    data.alunos.forEach((al,i)=>{
      const sty=al.pct<50?'dng':'wrn';
      rows+='<Row ss:Height="18">'+xlsCell(i+1)+xlsCell(al.nome)+xlsCell(al.turma)+xlsCell(al.telefone||'—')+xlsCell(al.presencas+'/'+al.total_aulas)+xlsCell(al.pct+'%',sty)+xlsCell(al.pct<50?'Crítico':'Atenção',sty)+'</Row>\n';
    });
    const xml=xlsWrap(XLS_STY.hdr+XLS_STY.dng+XLS_STY.wrn,'Alunos em Risco',colDefs,rows);
    xlsDownload(xml,'alunos_risco_'+ano);
  });
})();
