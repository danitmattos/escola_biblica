// ══════════════════════════════════════════════════
//  FREQUÊNCIA DE ALUNOS — Relatório
// ══════════════════════════════════════════════════
(function() {
  if (!document.getElementById('freq-turma')) return;

  const API = 'frequencia_crud.php';
  const TRIMESTRE_LABEL = { 1:'1º Trimestre', 2:'2º Trimestre', 3:'3º Trimestre', 4:'4º Trimestre' };
  let freqData = null;

  function showAlert(msg, tipo) {
    const el = document.getElementById('freq-alert');
    el.style.display = msg ? 'block' : 'none';
    el.className = 'alert alert-' + (tipo || 'danger');
    el.textContent = msg;
  }

  /* Carrega anos */
  fetch(API + '?recurso=anos')
    .then(r => r.json())
    .then(data => {
      if (!data.ok) return;
      const sel = document.getElementById('freq-ano');
      data.anos.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a;
        opt.textContent = a;
        sel.appendChild(opt);
      });
    });

  /* Carrega turmas */
  fetch(API + '?recurso=turmas')
    .then(r => r.json())
    .then(data => {
      if (!data.ok) return;
      const sel = document.getElementById('freq-turma');
      data.turmas.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.nome_turma;
        sel.appendChild(opt);
      });
    });

  /* Gera relatório */
  document.getElementById('btnGerarFreq').addEventListener('click', gerarRelatorio);

  function gerarRelatorio() {
    const turma_id = parseInt(document.getElementById('freq-turma').value) || 0;
    const ano      = parseInt(document.getElementById('freq-ano').value)   || 0;
    if (!turma_id) { showAlert('Selecione uma turma.', 'danger'); return; }
    showAlert('', '');
    document.getElementById('freq-loading').style.display = 'block';
    document.getElementById('freq-container').innerHTML = '';
    document.getElementById('freq-export-btns').style.display = 'none';

    fetch(API + '?recurso=relatorio&turma_id=' + turma_id + '&ano=' + ano)
      .then(r => r.json())
      .then(data => {
        document.getElementById('freq-loading').style.display = 'none';
        if (!data.ok) { showAlert(data.msg, 'danger'); return; }
        renderRelatorio(data);
      })
      .catch(() => {
        document.getElementById('freq-loading').style.display = 'none';
        showAlert('Erro de conexão.', 'danger');
      });
  }

  function renderRelatorio(data) {
    freqData = data;
    const container = document.getElementById('freq-container');

    if (!data.trimestres || !data.trimestres.length) {
      container.innerHTML =
        '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
        'Nenhuma aula encontrada para esta turma e ano.</div></div>';
      return;
    }

    document.getElementById('freq-export-btns').style.display = 'flex';

    let html = '<div style="margin-bottom:var(--space-4)">' +
      '<span style="font-size:var(--text-lg);font-weight:700">' + escHtml(data.nome_turma) + '</span>' +
      '<span style="font-size:var(--text-sm);color:var(--color-text-muted);margin-left:var(--space-3)">' + data.ano + '</span>' +
      '</div>';

    html += '<div class="table-wrapper" id="freq-tabela"><table class="table" style="font-size:var(--text-xs)">';

    html += '<thead><tr>';
    html += '<th rowspan="2" style="min-width:160px;position:sticky;left:0;background:var(--color-gray-50);z-index:2">Aluno</th>';

    data.trimestres.forEach(tri => {
      const span = tri.aulas.length + 1;
      html += '<th colspan="' + span + '" style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">' +
        (TRIMESTRE_LABEL[tri.trimestre] || tri.trimestre + 'º Tri') + '</th>';
    });
    html += '<th rowspan="2" style="text-align:center;background:var(--color-green-50,#f0fdf4);border-left:2px solid var(--color-border)">Total<br>Geral</th>';
    html += '</tr>';

    html += '<tr>';
    data.trimestres.forEach(tri => {
      tri.aulas.forEach(a => {
        html += '<th style="text-align:center;max-width:80px;white-space:normal;font-weight:500;border-left:1px solid var(--color-border);font-size:10px" title="' + escHtml(a.titulo) + '">' +
          escHtml(a.titulo.length > 22 ? a.titulo.substring(0,20)+'…' : a.titulo) + '</th>';
      });
      html += '<th style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">Sub</th>';
    });
    html += '</tr></thead>';

    html += '<tbody>';
    if (!data.alunos.length) {
      const totalCols = data.trimestres.reduce((s, t) => s + t.aulas.length + 1, 0) + 2;
      html += '<tr><td colspan="' + totalCols + '" style="text-align:center;color:var(--color-text-muted)">Nenhum aluno ativo nesta turma.</td></tr>';
    }

    data.alunos.forEach((aluno, idx) => {
      const bg = idx % 2 === 1 ? 'background:var(--color-gray-50)' : '';
      html += '<tr style="' + bg + '">';
      html += '<td style="font-weight:500;position:sticky;left:0;background:' + (idx%2===1?'var(--color-gray-50)':'var(--color-surface)') + ';z-index:1">' + escHtml(aluno.nome) + '</td>';

      data.trimestres.forEach(tri => {
        const triData = aluno.porTrimestre[tri.trimestre] || { detalhe:{}, total:0 };
        tri.aulas.forEach(a => {
          const presente = triData.detalhe && triData.detalhe[a.id];
          html += '<td style="text-align:center;border-left:1px solid var(--color-border)">' +
            (presente
              ? '<span title="Presente" style="color:var(--color-success,#16a34a);font-size:14px">✓</span>'
              : '<span title="Ausente"  style="color:var(--color-gray-300,#d1d5db);font-size:14px">—</span>') +
            '</td>';
        });
        html += '<td style="text-align:center;font-weight:700;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">' +
          triData.total + '/' + tri.aulas.length + '</td>';
      });

      html += '<td style="text-align:center;font-weight:700;background:var(--color-green-50,#f0fdf4);border-left:2px solid var(--color-border);color:var(--color-success,#16a34a)">' +
        aluno.totalGeral + '</td>';
      html += '</tr>';
    });

    if (data.alunos.length > 1) {
      html += '<tr style="border-top:2px solid var(--color-border);font-weight:700;background:var(--color-gray-50)">';
      html += '<td style="position:sticky;left:0;background:var(--color-gray-50);z-index:1">Total presenças</td>';
      let totalGeralSum = 0;
      data.trimestres.forEach(tri => {
        let subTri = 0;
        tri.aulas.forEach(a => {
          let cnt = 0;
          data.alunos.forEach(aluno => {
            const td = aluno.porTrimestre[tri.trimestre];
            if (td && td.detalhe && td.detalhe[a.id]) cnt++;
          });
          subTri += cnt;
          html += '<td style="text-align:center;border-left:1px solid var(--color-border)">' + cnt + '</td>';
        });
        totalGeralSum += subTri;
        html += '<td style="text-align:center;background:var(--color-primary-50,#eff6ff);border-left:2px solid var(--color-border)">' + subTri + '</td>';
      });
      html += '<td style="text-align:center;background:var(--color-green-50,#f0fdf4);border-left:2px solid var(--color-border)">' + totalGeralSum + '</td>';
      html += '</tr>';
    }

    html += '</tbody></table></div>';
    container.innerHTML = html;
  }

  /* Exportar XLS (SpreadsheetML) */
  document.getElementById('btnFreqXls').addEventListener('click', () => {
    if (!freqData) return;
    const data = freqData;
    const cell = (val, styleId, type) => {
      type = type || 'String';
      const safe = String(val === null || val === undefined ? '' : val)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      return '<Cell' + (styleId ? ' ss:StyleID="' + styleId + '"' : '') + '>' +
        '<Data ss:Type="' + type + '">' + safe + '</Data></Cell>';
    };

    const hdrStyle = '<Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>' +
      '<Interior ss:Color="#1E40AF" ss:Pattern="Solid"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/></Style>';
    const subStyle = '<Style ss:ID="sub"><Font ss:Bold="1" ss:Size="10"/>' +
      '<Interior ss:Color="#DBEAFE" ss:Pattern="Solid"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
    const totStyle = '<Style ss:ID="tot"><Font ss:Bold="1" ss:Color="#166534" ss:Size="10"/>' +
      '<Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
    const preStyle = '<Style ss:ID="pre"><Font ss:Color="#166534"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
    const ausStyle = '<Style ss:ID="aus"><Font ss:Color="#9CA3AF"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
    const ftrStyle = '<Style ss:ID="ftr"><Font ss:Bold="1" ss:Size="10"/>' +
      '<Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>' +
      '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';

    let row2 = '<Row ss:Height="36">' + cell('', 'hdr');
    data.trimestres.forEach(tri => {
      tri.aulas.forEach(a => { row2 += cell(a.titulo, 'hdr'); });
      row2 += cell('Sub', 'sub');
    });
    row2 += cell('', 'tot') + '</Row>\n';

    let bodyRows = '';
    data.alunos.forEach(aluno => {
      let row = '<Row ss:Height="18">' + cell(aluno.nome);
      data.trimestres.forEach(tri => {
        const td = aluno.porTrimestre[tri.trimestre] || { detalhe:{}, total:0 };
        tri.aulas.forEach(a => {
          const pres = td.detalhe && td.detalhe[a.id];
          row += cell(pres ? '✓' : '–', pres ? 'pre' : 'aus');
        });
        row += cell(td.total + '/' + tri.aulas.length, 'sub');
      });
      row += cell(aluno.totalGeral, 'tot') + '</Row>\n';
      bodyRows += row;
    });

    if (data.alunos.length > 1) {
      let ftrRow = '<Row ss:Height="18">' + cell('Total presenças', 'ftr');
      data.trimestres.forEach(tri => {
        let subTri = 0;
        tri.aulas.forEach(a => {
          let cnt = 0;
          data.alunos.forEach(al => { const td = al.porTrimestre[tri.trimestre]; if (td && td.detalhe && td.detalhe[a.id]) cnt++; });
          subTri += cnt;
          ftrRow += cell(cnt, 'ftr');
        });
        ftrRow += cell(subTri, 'sub');
      });
      const totalSum = data.alunos.reduce((s, al) => s + al.totalGeral, 0);
      ftrRow += cell(totalSum, 'tot') + '</Row>\n';
      bodyRows += ftrRow;
    }

    let colDefs = '<Column ss:Width="160"/>';
    data.trimestres.forEach(tri => {
      tri.aulas.forEach(() => { colDefs += '<Column ss:Width="70"/>'; });
      colDefs += '<Column ss:Width="50"/>';
    });
    colDefs += '<Column ss:Width="60"/>';

    const xml = '<' + '?xml version="1.0" encoding="UTF-8"?>\n' +
      '<' + '?mso-application progid="Excel.Sheet"?>\n' +
      '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n' +
      '<Styles>' + hdrStyle + subStyle + totStyle + preStyle + ausStyle + ftrStyle + '</Styles>\n' +
      '<Worksheet ss:Name="Frequência">\n<Table>\n' + colDefs + '\n' +
      row2 + bodyRows +
      '</Table>\n</Worksheet>\n</Workbook>';

    const blob = new Blob(['\uFEFF' + xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url;
    a.download = 'frequencia_' + escHtml(data.nome_turma).replace(/\s+/g,'_') + '_' + data.ano + '.xls';
    a.click();
    URL.revokeObjectURL(url);
  });

  /* Exportar PDF via print */
  document.getElementById('btnFreqPdf').addEventListener('click', () => {
    const tabela = document.getElementById('freq-tabela');
    if (!tabela) return;
    pdfOpen({
      title: 'Frequência Detalhada — ' + (freqData ? freqData.ano : ''),
      subtitle: 'Turma: <strong>' + esc(freqData ? freqData.nome_turma : '') + '</strong>',
      body: tabela.outerHTML,
      css: ':root{--color-gray-50:#f9fafb;--color-surface:#fff;--color-border:#e5e7eb;--color-text-muted:#6b7280;--color-primary-50:#eff6ff;--color-green-50:#f0fdf4;--color-success:#16a34a;--color-danger:#dc2626;--color-gray-300:#d1d5db}',
      orientation: 'landscape'
    });
  });
})();
