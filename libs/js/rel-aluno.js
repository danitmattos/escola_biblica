// ══════════════════════════════════════════════════
//  RELATÓRIO — FREQUÊNCIA INDIVIDUAL
// ══════════════════════════════════════════════════
(function(){
  if(!document.getElementById('ra-turma')) return;
  const API='frequencia_crud.php';
  const TRI_LABEL={1:'1º Trimestre',2:'2º Trimestre',3:'3º Trimestre',4:'4º Trimestre'};
  let raData = null;
  fetch(API+'?recurso=anos').then(r=>r.json()).then(data=>{
    const s=document.getElementById('ra-ano');
    (data.anos||[]).forEach(a=>{ const o=document.createElement('option'); o.value=a; o.textContent=a; s.appendChild(o); });
  });
  fetch(API+'?recurso=turmas').then(r=>r.json()).then(data=>{
    const s=document.getElementById('ra-turma');
    (data.turmas||[]).forEach(t=>{ const o=document.createElement('option'); o.value=t.id; o.textContent=t.nome_turma; s.appendChild(o); });
  });

  document.getElementById('ra-turma').addEventListener('change', function(){
    const tid=parseInt(this.value)||0;
    const sel=document.getElementById('ra-aluno');
    sel.innerHTML='<option value="0">Carregando…</option>'; sel.disabled=true;
    if(!tid){ sel.innerHTML='<option value="0">— Selecione a turma —</option>'; return; }
    fetch(API+'?recurso=alunos-turma&turma_id='+tid).then(r=>r.json()).then(data=>{
      sel.innerHTML='<option value="0">— Selecione —</option>';
      (data.alunos||[]).forEach(a=>{ const o=document.createElement('option'); o.value=a.id; o.textContent=a.nome; sel.appendChild(o); });
      sel.disabled=false;
    });
  });

  document.getElementById('btnRaGerar').addEventListener('click',()=>{
    const aluno_id=parseInt(document.getElementById('ra-aluno').value)||0;
    const ano=document.getElementById('ra-ano').value;
    if(!aluno_id){ alert('Selecione um aluno.'); return; }
    document.getElementById('ra-loading').style.display='block';
    document.getElementById('ra-container').innerHTML='';
    document.getElementById('btnRaPdf').style.display='none';
    document.getElementById('btnRaXls').style.display='none';
    fetch(API+'?recurso=rel-aluno&aluno_id='+aluno_id+'&ano='+ano)
      .then(r=>r.json()).then(data=>{
        document.getElementById('ra-loading').style.display='none';
        if(!data.ok){ document.getElementById('ra-alert').style.display='block'; document.getElementById('ra-alert').textContent=data.msg; return; }
        raData=data;
        renderRa(data);
      }).catch(()=>document.getElementById('ra-loading').style.display='none');
  });

  function renderRa(data){
    const c=document.getElementById('ra-container');
    document.getElementById('btnRaPdf').style.display='inline-flex';
    document.getElementById('btnRaXls').style.display='inline-flex';

    const al=data.aluno;
    const pct=data.pct_geral;
    const corGeral=pct!==null?corPct(pct):'var(--color-text-muted)';

    /* Card do aluno */
    let html='<div class="card" style="margin-bottom:var(--space-6)" id="ra-resultado">';
    html+='<div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-6);align-items:center">';
    html+='<div style="flex:1;min-width:200px">';
    html+='<div style="font-size:var(--text-xl);font-weight:700">'+escHtml(al.nome)+'</div>';
    html+='<div style="font-size:var(--text-sm);color:var(--color-text-muted);margin-top:4px">'+escHtml(al.turma)+'</div>';
    if(al.telefone) html+='<div style="font-size:var(--text-sm);color:var(--color-text-muted)">'+escHtml(al.telefone)+'</div>';
    html+='</div>';
    html+='<div style="text-align:center;padding:var(--space-4) var(--space-6);background:var(--color-gray-50);border-radius:var(--radius-lg)">';
    html+='<div style="font-size:2.5rem;font-weight:800;color:'+corGeral+'">'+(pct!==null?pct+'%':'—')+'</div>';
    html+='<div style="font-size:var(--text-xs);color:var(--color-text-muted)">Frequência Geral</div>';
    html+='<div style="font-size:var(--text-xs);color:var(--color-text-muted)">'+data.total_presencas+' de '+data.total_aulas+' aulas — '+data.ano+'</div>';
    html+='</div>';
    html+='</div></div>';

    /* Cards por trimestre */
    if(data.trimestres&&data.trimestres.length){
      html+='<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:var(--space-4);margin-bottom:var(--space-4)">';
      data.trimestres.forEach(tri=>{
        const cp=tri.pct!==null?corPct(tri.pct):'var(--color-text-muted)';
        html+='<div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center">';
        html+='<span class="card-title">'+(TRI_LABEL[tri.trimestre]||tri.trimestre+'º Tri')+'</span>';
        html+='<span style="font-size:var(--text-xl);font-weight:800;color:'+cp+'">'+(tri.pct!==null?tri.pct+'%':'—')+'</span>';
        html+='</div><div class="card-body" style="padding:0">';
        html+='<table class="table" style="font-size:var(--text-xs)">';
        html+='<thead><tr><th>Aula</th><th style="text-align:center">Data</th><th style="text-align:center">Presença</th></tr></thead><tbody>';
        tri.aulas.forEach(a=>{
          const ok=a.presente;
          html+='<tr>';
          html+='<td style="max-width:200px;white-space:normal">'+escHtml(a.titulo)+'</td>';
          html+='<td style="text-align:center;white-space:nowrap">'+fmtData(a.data_aula)+'</td>';
          html+='<td style="text-align:center">'+(ok?'<span style="color:var(--color-success,#16a34a);font-size:16px">✓</span>':'<span style="color:var(--color-danger,#dc2626);font-size:14px">✗</span>')+'</td>';
          html+='</tr>';
        });
        html+='</tbody></table>';
        html+='<div style="padding:var(--space-3) var(--space-4);background:var(--color-gray-50);font-size:var(--text-xs);color:var(--color-text-muted);display:flex;justify-content:space-between">';
        html+='<span>Presenças: <strong>'+tri.presencas+'/'+tri.total+'</strong></span>';
        html+='<span style="color:'+cp+';font-weight:700">'+(tri.pct!==null?tri.pct+'%':'—')+'</span>';
        html+='</div></div></div>';
      });
      html+='</div>';
    } else {
      html+='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">Nenhuma aula com sessão encontrada para '+data.ano+'.</div></div>';
    }

    c.innerHTML=html;
  }

  document.getElementById('btnRaPdf').addEventListener('click',()=>{
    if(!raData) return;
    const d=raData, al=d.aluno;
    const s=v=>esc(v);
    const _c=p=>{if(p===null||p===undefined)return'#9ca3af';if(p>=75)return'#16a34a';if(p>=50)return'#d97706';return'#dc2626';};
    let body='<div class="summary-cards">';
    body+='<div class="s-card"><div class="s-card-val" style="color:'+_c(d.pct_geral)+'">'+(d.pct_geral!==null?d.pct_geral+'%':'—')+'</div><div class="s-card-lbl">Frequência Geral</div></div>';
    body+='<div class="s-card"><div class="s-card-val" style="color:#1E40AF">'+d.total_presencas+'/'+d.total_aulas+'</div><div class="s-card-lbl">Presenças</div></div>';
    body+='<div class="s-card"><div class="s-card-val" style="color:#1E40AF">'+d.trimestres.length+'</div><div class="s-card-lbl">Trimestres</div></div>';
    body+='</div>';
    if(d.trimestres&&d.trimestres.length){
      d.trimestres.forEach(tri=>{
        const cp=_c(tri.pct);
        body+='<div style="margin-bottom:14px"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px"><strong>'+(TRI_LABEL[tri.trimestre]||tri.trimestre+'º Tri')+'</strong><span style="font-weight:800;color:'+cp+'">'+(tri.pct!==null?tri.pct+'%':'—')+'</span></div>';
        body+='<table><thead><tr><th>Aula</th><th>Data</th><th>Presença</th></tr></thead><tbody>';
        tri.aulas.forEach(a=>{
          body+='<tr><td>'+s(a.titulo)+'</td><td>'+fmtData(a.data_aula)+'</td>';
          body+='<td>'+(a.presente?'<span style="color:#16a34a;font-weight:700">✓ Presente</span>':'<span style="color:#dc2626">✗ Ausente</span>')+'</td></tr>';
        });
        body+='</tbody></table>';
        body+='<div style="text-align:right;font-size:10px;color:#6b7280;margin-top:3px">Presenças: <strong>'+tri.presencas+'/'+tri.total+'</strong></div></div>';
      });
    }
    pdfOpen({title:'Frequência Individual — '+d.ano,subtitle:'<strong>'+s(al.nome)+'</strong> — '+s(al.turma)+(al.telefone?' — '+s(al.telefone):''),body:body});
  });

  document.getElementById('btnRaXls').addEventListener('click',()=>{
    if(!raData) return;
    const d=raData, al=d.aluno;
    let colDefs='<Column ss:Width="200"/><Column ss:Width="100"/><Column ss:Width="90"/>';
    let rows='<Row ss:Height="26">'+xlsCell('Aula','hdr')+xlsCell('Data','hdr')+xlsCell('Presença','hdr')+'</Row>\n';
    d.trimestres.forEach(tri=>{
      rows+='<Row ss:Height="20">'+xlsCell(TRI_LABEL[tri.trimestre]||tri.trimestre+'º Tri','sub')+xlsCell('','sub')+xlsCell(tri.pct!==null?tri.pct+'%':'—','sub')+'</Row>\n';
      tri.aulas.forEach(a=>{
        rows+='<Row ss:Height="16">'+xlsCell(a.titulo)+xlsCell(fmtData(a.data_aula))+xlsCell(a.presente?'Presente':'Ausente',a.presente?'pre':'aus')+'</Row>\n';
      });
      rows+='<Row ss:Height="18">'+xlsCell('Subtotal','tot')+xlsCell(tri.presencas+'/'+tri.total,'tot')+xlsCell(tri.pct!==null?tri.pct+'%':'—','tot')+'</Row>\n';
    });
    rows+='<Row ss:Height="22">'+xlsCell('TOTAL GERAL','hdr')+xlsCell(d.total_presencas+'/'+d.total_aulas,'hdr')+xlsCell(d.pct_geral!==null?d.pct_geral+'%':'—','hdr')+'</Row>\n';
    const xml=xlsWrap(XLS_STY.hdr+XLS_STY.sub+XLS_STY.tot+XLS_STY.pre+XLS_STY.aus,al.nome,colDefs,rows);
    xlsDownload(xml,'freq_individual_'+al.nome.replace(/\s+/g,'_')+'_'+d.ano);
  });
})();
