// ══════════════════════════════════════════════════
//  RELATÓRIO — FREQUÊNCIA POR TURMA
// ══════════════════════════════════════════════════
(function(){
  if(!document.getElementById('rt-turma')) return;
  const API = 'frequencia_crud.php';
  const TRI_LABEL = {1:'1º Trimestre',2:'2º Trimestre',3:'3º Trimestre',4:'4º Trimestre'};
  let rtData = null;

  fetch(API+'?recurso=anos').then(r=>r.json()).then(data=>{
    if(!data.ok) return;
    const s=document.getElementById('rt-ano');
    data.anos.forEach(a=>{ const o=document.createElement('option'); o.value=a; o.textContent=a; s.appendChild(o); });
  });
  fetch(API+'?recurso=turmas').then(r=>r.json()).then(data=>{
    if(!data.ok) return;
    const s=document.getElementById('rt-turma');
    data.turmas.forEach(t=>{ const o=document.createElement('option'); o.value=t.id; o.textContent=t.nome_turma; s.appendChild(o); });
  });

  document.getElementById('btnRtGerar').addEventListener('click', ()=>{
    const turma_id = parseInt(document.getElementById('rt-turma').value)||0;
    const ano      = document.getElementById('rt-ano').value;
    if(!turma_id){ alert('Selecione uma turma.'); return; }
    document.getElementById('rt-loading').style.display='block';
    document.getElementById('rt-container').innerHTML='';
    ['btnRtPdf','btnRtXls'].forEach(id=>document.getElementById(id).style.display='none');
    fetch(API+'?recurso=relatorio&turma_id='+turma_id+'&ano='+ano)
      .then(r=>r.json()).then(data=>{
        document.getElementById('rt-loading').style.display='none';
        if(!data.ok){ document.getElementById('rt-alert').style.display='block'; document.getElementById('rt-alert').textContent=data.msg; return; }
        rtData=data;
        renderRt(data);
      }).catch(()=>document.getElementById('rt-loading').style.display='none');
  });

  function renderRt(data){
    const c=document.getElementById('rt-container');
    if(!data.trimestres||!data.trimestres.length){
      c.innerHTML='<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">Nenhuma aula com sessão encontrada.</div></div>';
      return;
    }
    ['btnRtPdf','btnRtXls'].forEach(id=>document.getElementById(id).style.display='inline-flex');

    let html='<div class="table-wrapper" id="rt-tabela"><table class="table" style="font-size:var(--text-xs)">';
    html+='<thead><tr>';
    html+='<th rowspan="2" style="min-width:160px;position:sticky;left:0;background:var(--color-gray-50);z-index:2">Aluno</th>';
    data.trimestres.forEach(tri=>{
      html+='<th colspan="'+(tri.aulas.length+2)+'" style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">'+(TRI_LABEL[tri.trimestre]||tri.trimestre+'º Tri')+'</th>';
    });
    html+='<th rowspan="2" style="text-align:center;background:var(--color-green-50,#f0fdf4);border-left:2px solid var(--color-border)">Total<br>Geral</th>';
    html+='<th rowspan="2" style="text-align:center;background:var(--color-green-50,#f0fdf4)">%</th>';
    html+='</tr><tr>';
    data.trimestres.forEach(tri=>{
      tri.aulas.forEach(a=>{
        html+='<th style="text-align:center;max-width:70px;white-space:normal;font-weight:500;border-left:1px solid var(--color-border);font-size:10px" title="'+escHtml(a.titulo)+'">'+escHtml(a.titulo.length>20?a.titulo.substring(0,18)+'…':a.titulo)+'</th>';
      });
      html+='<th style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">Sub</th>';
      html+='<th style="text-align:center;background:var(--color-primary-50,#eff6ff)">%</th>';
    });
    html+='</tr></thead><tbody>';

    const totalAulasGeral = data.trimestres.reduce((s,t)=>s+t.aulas.length,0);
    data.alunos.forEach((al,i)=>{
      const bgRow = i%2===1?'background:var(--color-gray-50)':'';
      html+='<tr style="'+bgRow+'">';
      html+='<td style="font-weight:500;position:sticky;left:0;background:'+(i%2===1?'var(--color-gray-50)':'var(--color-surface)')+';z-index:1">'+escHtml(al.nome)+'</td>';
      data.trimestres.forEach(tri=>{
        const td=al.porTrimestre[tri.trimestre]||{detalhe:{},total:0};
        tri.aulas.forEach(a=>{
          const pres=td.detalhe&&td.detalhe[a.id];
          html+='<td style="text-align:center;border-left:1px solid var(--color-border)">'+(pres?'<span style="color:var(--color-success,#16a34a)">✓</span>':'<span style="color:var(--color-gray-300,#d1d5db)">—</span>')+'</td>';
        });
        const pctTri = tri.aulas.length>0?Math.round(td.total/tri.aulas.length*100):null;
        html+='<td style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border);font-weight:600">'+td.total+'/'+tri.aulas.length+'</td>';
        html+='<td style="text-align:center;background:var(--color-primary-50,#eff6ff);font-weight:700;color:'+(pctTri!==null?corPct(pctTri):'var(--color-text-muted)')+'">'+(pctTri!==null?pctTri+'%':'—')+'</td>';
      });
      const pctGeral = totalAulasGeral>0?Math.round(al.totalGeral/totalAulasGeral*100):null;
      html+='<td style="text-align:center;font-weight:700;background:var(--color-green-50,#f0fdf4);border-left:2px solid var(--color-border)">'+al.totalGeral+'</td>';
      html+='<td style="text-align:center;font-weight:700;background:var(--color-green-50,#f0fdf4);color:'+(pctGeral!==null?corPct(pctGeral):'var(--color-text-muted)')+'">'+(pctGeral!==null?pctGeral+'%':'—')+'</td>';
      html+='</tr>';
    });
    html+='</tbody></table></div>';
    c.innerHTML=html;
  }

  document.getElementById('btnRtPdf').addEventListener('click',()=>{
    const t=document.getElementById('rt-tabela'); if(!t) return;
    const turma=document.getElementById('rt-turma');
    const nomeTurma=turma.options[turma.selectedIndex]?turma.options[turma.selectedIndex].text:'';
    const ano=document.getElementById('rt-ano').value;
    pdfOpen({
      title:'Frequência por Turma — '+ano,
      subtitle:'Turma: <strong>'+esc(nomeTurma)+'</strong>',
      body:t.outerHTML,
      css:':root{--color-gray-50:#f9fafb;--color-surface:#fff;--color-border:#e5e7eb;--color-text-muted:#6b7280;--color-primary-50:#eff6ff;--color-green-50:#f0fdf4;--color-success:#16a34a;--color-danger:#dc2626;--color-gray-300:#d1d5db}',
      orientation:'landscape'
    });
  });

  document.getElementById('btnRtXls').addEventListener('click',()=>{
    if(!rtData) return;
    const data=rtData;
    const cell=(v,si,type)=>{
      type=type||'String';
      const s=String(v===null||v===undefined?'':v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      return '<Cell'+(si?' ss:StyleID="'+si+'"':'')+'>'+' <Data ss:Type="'+type+'">'+s+'</Data></Cell>';
    };
    const hdr='<Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="10"/><Interior ss:Color="#1E40AF" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/></Style>';
    const sub='<Style ss:ID="sub"><Font ss:Bold="1" ss:Size="10"/><Interior ss:Color="#DBEAFE" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center"/></Style>';
    const tot='<Style ss:ID="tot"><Font ss:Bold="1" ss:Color="#166534" ss:Size="10"/><Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center"/></Style>';
    const pre='<Style ss:ID="pre"><Font ss:Color="#166534"/><Alignment ss:Horizontal="Center"/></Style>';
    const aus='<Style ss:ID="aus"><Font ss:Color="#9CA3AF"/><Alignment ss:Horizontal="Center"/></Style>';

    let row1='<Row ss:Height="22">'+cell(data.nome_turma,'hdr');
    data.trimestres.forEach(tri=>{
      const lbl=({1:'1º Trimestre',2:'2º Trimestre',3:'3º Trimestre',4:'4º Trimestre'})[tri.trimestre]||tri.trimestre+'º Tri';
      row1+='<Cell ss:StyleID="hdr" ss:MergeAcross="'+(tri.aulas.length+1)+'"><Data ss:Type="String">'+lbl+'</Data></Cell>';
      for(let i=0;i<=tri.aulas.length;i++) row1+='<Cell ss:Index="9999"/>';
    });
    row1+=cell('Total','tot')+cell('%','tot')+'</Row>\n';

    let row2='<Row ss:Height="30">'+cell('','hdr');
    data.trimestres.forEach(tri=>{
      tri.aulas.forEach(a=>{ row2+=cell(a.titulo,'hdr'); });
      row2+=cell('Sub','sub')+cell('%','sub');
    });
    row2+=cell('','tot')+cell('','tot')+'</Row>\n';

    const totalAulasGeral=data.trimestres.reduce((s,t)=>s+t.aulas.length,0);
    let bodyRows='';
    data.alunos.forEach(al=>{
      let row='<Row ss:Height="18">'+cell(al.nome);
      data.trimestres.forEach(tri=>{
        const td=al.porTrimestre[tri.trimestre]||{detalhe:{},total:0};
        tri.aulas.forEach(a=>{ const p=td.detalhe&&td.detalhe[a.id]; row+=cell(p?'✓':'—',p?'pre':'aus'); });
        const pctTri=tri.aulas.length>0?Math.round(td.total/tri.aulas.length*100):null;
        row+=cell(td.total+'/'+tri.aulas.length,'sub')+cell(pctTri!==null?pctTri+'%':'—','sub');
      });
      const pctGeral=totalAulasGeral>0?Math.round(al.totalGeral/totalAulasGeral*100):null;
      row+=cell(al.totalGeral,'tot')+cell(pctGeral!==null?pctGeral+'%':'—','tot')+'</Row>\n';
      bodyRows+=row;
    });

    let colDefs='<Column ss:Width="160"/>';
    data.trimestres.forEach(tri=>{ tri.aulas.forEach(()=>{colDefs+='<Column ss:Width="65"/>'}); colDefs+='<Column ss:Width="45"/><Column ss:Width="45"/>'; });
    colDefs+='<Column ss:Width="55"/><Column ss:Width="45"/>';

    const xml='<'+'?xml version="1.0" encoding="UTF-8"?>\n<'+'?mso-application progid="Excel.Sheet"?>\n'+
      '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n'+
      '<Styles>'+hdr+sub+tot+pre+aus+'</Styles>\n'+
      '<Worksheet ss:Name="Frequência">\n<Table>\n'+colDefs+'\n'+row2+bodyRows+'</Table>\n</Worksheet>\n</Workbook>';

    const blob=new Blob(['\uFEFF'+xml],{type:'application/vnd.ms-excel;charset=utf-8;'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url; a.download='freq_turma_'+data.nome_turma.replace(/\s+/g,'_')+'_'+document.getElementById('rt-ano').value+'.xls'; a.click();
    URL.revokeObjectURL(url);
  });
})();
