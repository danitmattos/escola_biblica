// ══════════════════════════════════════════════════
//  CRONOGRAMA DE AULAS
// ══════════════════════════════════════════════════
(function() {
  const cronContainer = document.getElementById('cron-container');
  if (!cronContainer) return;

  const alertEl   = document.getElementById('cron-alert');
  const exportBtns = document.getElementById('cron-export-btns');
  let   cronData  = [];

  const TRIM_INFO = {
    '1': { label: '1º Trimestre', cor: 'var(--trim1-cor)', bg: 'var(--trim1-bg)', borda: 'var(--trim1-borda)' },
    '2': { label: '2º Trimestre', cor: 'var(--trim2-cor)', bg: 'var(--trim2-bg)', borda: 'var(--trim2-borda)' },
    '3': { label: '3º Trimestre', cor: 'var(--trim3-cor)', bg: 'var(--trim3-bg)', borda: 'var(--trim3-borda)' },
    '4': { label: '4º Trimestre', cor: 'var(--trim4-cor)', bg: 'var(--trim4-bg)', borda: 'var(--trim4-borda)' },
  };
  const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

  function fmtDiaSemana(d) {
    if (!d) return '';
    const dias = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
    return dias[new Date(d + 'T00:00:00').getDay()];
  }
  function showAlert(msg, tipo) {
    alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
    alertEl.style.display = 'block';
    if (tipo !== 'danger') setTimeout(() => alertEl.style.display = 'none', 4000);
  }

  // Popula select de turmas
  const selTurma = document.getElementById('cron-turma');
  if (selTurma && selTurma.options.length <= 1) {
    fetch('turmas_crud.php')
      .then(r => r.json())
      .then(d => {
        (d.turmas || []).forEach(t => {
          const o = document.createElement('option');
          o.value = t.id;
          o.textContent = t.nome_turma;
          selTurma.appendChild(o);
        });
      });
  }

  function carregarCronograma() {
    const ano       = parseInt(document.getElementById('cron-ano').value) || new Date().getFullYear();
    const trimestre = document.getElementById('cron-trimestre').value;
    const turma_id  = document.getElementById('cron-turma').value;

    cronContainer.innerHTML = skeletonSections(2);
    if (exportBtns) exportBtns.style.display = 'none';

    const p = new URLSearchParams({ recurso: 'cronograma', ano, trimestre, turma_id });
    fetch('aulas_temas_crud.php?' + p)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Erro ao carregar.', 'danger'); cronContainer.innerHTML = ''; return; }
        cronData = data.turmas;
        renderCronograma(cronData);
        if (exportBtns) exportBtns.style.display = cronData.length ? 'flex' : 'none';
      })
      .catch(() => showAlert('Falha na comunicação.', 'danger'));
  }

  function renderCronograma(turmas) {
    if (!turmas.length) {
      cronContainer.innerHTML = '<div style="text-align:center;padding:var(--space-12);color:var(--color-text-muted)">' +
        '<svg style="width:40px;height:40px;fill:currentColor;display:block;margin:0 auto var(--space-3);opacity:.3" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>' +
        'Nenhuma aula encontrada para os filtros selecionados.</div>';
      return;
    }

    const chevronSvg = `<svg class="cron-trim-chevron" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>`;

    let html = '';
    turmas.forEach(turma => {
      const aulas = turma.aulas;
      const porTrim = {};
      aulas.forEach(a => {
        const tk = String(a.trimestre || '1');
        if (!porTrim[tk]) porTrim[tk] = [];
        porTrim[tk].push(a);
      });

      html += `<div class="cron-turma-block">
        <div class="cron-turma-header">
          <svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
          <span>${esc(turma.nome_turma)}</span>
          <span class="badge" style="background:rgba(255,255,255,.25);color:inherit;margin-left:auto">${aulas.length} aula${aulas.length !== 1 ? 's' : ''}</span>
        </div>`;

      Object.keys(porTrim).sort().forEach(tk => {
        const trim = TRIM_INFO[tk] || TRIM_INFO['1'];
        const list = porTrim[tk];

        html += `<div class="cron-trim-section" data-trim="${tk}">
          <div class="cron-trim-header" onclick="this.closest('.cron-trim-section').classList.toggle('collapsed')" style="color:${trim.cor}">
            <span class="cron-trim-dot" style="background:${trim.cor}"></span>
            <span>${trim.label}</span>
            <span class="badge" style="background:${trim.bg};color:${trim.cor};border:1px solid ${trim.borda};margin-left:var(--space-2)">${list.length} aula${list.length !== 1 ? 's' : ''}</span>
            ${chevronSvg}
          </div>
          <div class="cron-trim-body">
            <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width:110px">Data</th>
                    <th>Aula</th>
                    <th>Tema</th>
                    <th>Professor</th>
                  </tr>
                </thead>
                <tbody>`;

        list.forEach(a => {
          const dataDia = a.data_aula
            ? `<div style="font-weight:600;font-size:var(--text-sm)">${fmtData(a.data_aula)}</div>
               <div style="font-size:var(--text-xs);color:var(--color-text-muted)">${fmtDiaSemana(a.data_aula)}</div>`
            : '<span style="color:var(--color-text-muted)">—</span>';

          html += `<tr>
            <td>${dataDia}</td>
            <td>
              <div style="font-weight:500">${esc(a.aula_titulo)}</div>
              ${a.descricao ? `<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(a.descricao)}</div>` : ''}
            </td>
            <td>
              <a href="index.php?pagina=tema-detalhe&id=${a.tema_id}" style="color:var(--color-primary);font-size:var(--text-sm)">${esc(a.tema_titulo)}</a>
            </td>
            <td>${a.professor ? `<span style="font-size:var(--text-sm)">${esc(a.professor)}</span>` : '<span style="color:var(--color-text-muted)">—</span>'}</td>
          </tr>`;
        });

        html += `</tbody></table></div></div></div>`;
      });

      html += `</div>`;
    });

    cronContainer.innerHTML = html;
  }

  // ── Dados tabulares para export ─────────────────
  function buildRows() {
    const rows = [];
    cronData.forEach(turma => {
      const porTrim = {};
      turma.aulas.forEach(a => {
        const tk = String(a.trimestre || '1');
        if (!porTrim[tk]) porTrim[tk] = [];
        porTrim[tk].push(a);
      });
      Object.keys(porTrim).sort().forEach(tk => {
        const trim = TRIM_INFO[tk] || TRIM_INFO['1'];
        porTrim[tk].forEach(a => {
          rows.push({
            turma    : turma.nome_turma,
            trimestre: trim.label,
            data     : fmtData(a.data_aula),
            semana   : fmtDiaSemana(a.data_aula),
            aula     : a.aula_titulo || '',
            tema     : a.tema_titulo || '',
            professor: a.professor   || '',
          });
        });
      });
    });
    return rows;
  }

  function nomeArquivo() {
    const ano     = document.getElementById('cron-ano').value;
    const trimSel = document.getElementById('cron-trimestre').value;
    return 'cronograma_' + ano + (trimSel > 0 ? '_' + trimSel + 'trim' : '');
  }

  // ── Exportar XLS (SpreadsheetML) ──────────────────
  function exportarXls() {
    if (!cronData.length) return;
    const rows = buildRows();
    const cols  = ['Turma','Trimestre','Data','Dia','Aula','Tema','Professor'];
    const keys  = ['turma','trimestre','data','semana','aula','tema','professor'];

    const trimBg = { '1º Trimestre':'#DBEAFE','2º Trimestre':'#DCFCE7','3º Trimestre':'#FEF9C3','4º Trimestre':'#F3E8FF' };
    const trimFg = { '1º Trimestre':'#1D4ED8','2º Trimestre':'#166534','3º Trimestre':'#92400E','4º Trimestre':'#6B21A8' };

    const headerStyle = `<Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/><Interior ss:Color="#1E40AF" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1E3A8A"/></Borders></Style>`;
    const styleBlocks = headerStyle;

    const trimStyles = Object.entries(trimBg).map(([lbl, bg]) => {
      const fg = trimFg[lbl] || '#000';
      const id = 'T' + lbl.charAt(0);
      return `<Style ss:ID="${id}"><Font ss:Color="${fg}" ss:Size="10"/><Interior ss:Color="${bg}" ss:Pattern="Solid"/><Alignment ss:Vertical="Center" ss:WrapText="1"/></Style>`;
    }).join('');

    const cell = (val, styleId) => {
      const safe = String(val).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      return `<Cell${styleId ? ` ss:StyleID="${styleId}"` : ''}><Data ss:Type="String">${safe}</Data></Cell>`;
    };

    let tableRows = `<Row ss:Height="22">${cols.map(c => cell(c, 'hdr')).join('')}</Row>\n`;
    rows.forEach(r => {
      const sid = 'T' + r.trimestre.charAt(0);
      tableRows += `<Row ss:Height="18">${keys.map(k => cell(r[k], sid)).join('')}</Row>\n`;
    });

    const xml = '<' + '?xml version="1.0" encoding="UTF-8"?>\n' +
      '<' + '?mso-application progid="Excel.Sheet"?>\n' +
      `<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"\n` +
      ` xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n` +
      `<Styles>${styleBlocks}${trimStyles}` +
      `<Style ss:ID="base"><Alignment ss:Vertical="Center" ss:WrapText="1"/><Font ss:Size="10"/></Style>` +
      `</Styles>\n` +
      `<Worksheet ss:Name="Cronograma">\n<Table ss:DefaultColumnWidth="110">\n` +
      `<Column ss:Width="120"/><Column ss:Width="100"/><Column ss:Width="80"/>` +
      `<Column ss:Width="50"/><Column ss:Width="180"/><Column ss:Width="180"/><Column ss:Width="130"/>\n` +
      tableRows +
      `</Table>\n</Worksheet>\n</Workbook>`;

    const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = nomeArquivo() + '.xls'; a.click();
    URL.revokeObjectURL(url);
  }

  // ── Exportar PDF (janela de impressão estilizada) ─
  function exportarPdf() {
    if (!cronData.length) return;
    const ano        = document.getElementById('cron-ano').value;
    const trimEl     = document.getElementById('cron-trimestre');
    const turmaEl    = document.getElementById('cron-turma');
    const trimLabel  = trimEl.options[trimEl.selectedIndex]?.text  || 'Todos';
    const turmaLabel = turmaEl.options[turmaEl.selectedIndex]?.text || 'Todas as turmas';

    const s = (v) => String(v||'').replace(/&/g,'&amp;').replace(/</g,'&lt;');

    let bodyHtml = '';
    cronData.forEach(turma => {
      const porTrim = {};
      turma.aulas.forEach(a => {
        const tk = String(a.trimestre || '1');
        if (!porTrim[tk]) porTrim[tk] = [];
        porTrim[tk].push(a);
      });

      bodyHtml += `<div class="tb"><div class="th">${s(turma.nome_turma)}<span class="tc">${turma.aulas.length} aula${turma.aulas.length!==1?'s':''}</span></div>`;

      Object.keys(porTrim).sort().forEach(tk => {
        const tinfo = TRIM_INFO[tk] || TRIM_INFO['1'];
        bodyHtml += `<div class="trh t${tk}">${s(tinfo.label)}<span class="trc">${porTrim[tk].length} aula${porTrim[tk].length!==1?'s':''}</span></div>` +
          `<table class="t${tk}"><thead><tr><th>Data</th><th>Dia</th><th>Aula</th><th>Tema</th><th>Professor</th></tr></thead><tbody>`;
        porTrim[tk].forEach((a, i) => {
          bodyHtml += `<tr class="${i%2?'alt':''}"><td class="nw">${fmtData(a.data_aula)}</td><td class="dim">${fmtDiaSemana(a.data_aula)}</td><td class="bold">${s(a.aula_titulo)}</td><td class="dim2">${s(a.tema_titulo)}</td><td>${s(a.professor)||'—'}</td></tr>`;
        });
        bodyHtml += `</tbody></table>`;
      });
      bodyHtml += `</div>`;
    });

    const css = `
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:Arial,sans-serif; font-size:11px; color:#111; background:#f1f5f9; padding:18px; }
.pw { max-width:860px; margin:0 auto; background:#fff; padding:22px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.1); }
.dh { display:flex; align-items:flex-start; justify-content:space-between; border-bottom:3px solid #1E40AF; padding-bottom:11px; margin-bottom:18px; }
.dt { font-size:17px; font-weight:700; color:#1E3A8A; }
.dm { font-size:10px; color:#374151; margin-top:3px; line-height:1.6; }
.dl { font-size:20px; background:#1E40AF; color:#fff; border-radius:7px; padding:5px 11px; font-weight:700; }
.tb { margin-bottom:24px; border:1px solid #cbd5e1; border-radius:7px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); page-break-inside:avoid; }
.th { background:linear-gradient(135deg,#1E40AF,#2563EB); color:#fff; font-weight:700; font-size:13px; padding:8px 13px; display:flex; align-items:center; gap:7px; }
.tc { background:rgba(255,255,255,.2); border-radius:999px; padding:1px 9px; font-size:10px; margin-left:auto; }
.trh { font-weight:700; font-size:11px; padding:5px 11px; display:flex; align-items:center; gap:7px; border-top:1px solid rgba(0,0,0,.06); border-left:5px solid; }
.trc { border-radius:999px; padding:1px 8px; font-size:10px; font-weight:600; color:#fff; }
.t1.trh { background:#DBEAFE; color:#1D4ED8; border-left-color:#1D4ED8; }
.t1.trc,.t1 .trc { background:#1D4ED8; }
.t1 thead { background:#1D4ED8; }
.t1 tbody tr.alt td { background:#EFF6FF; }
.t2.trh { background:#DCFCE7; color:#166534; border-left-color:#166534; }
.t2.trc,.t2 .trc { background:#166534; }
.t2 thead { background:#166534; }
.t2 tbody tr.alt td { background:#F0FDF4; }
.t3.trh { background:#FEF9C3; color:#92400E; border-left-color:#92400E; }
.t3.trc,.t3 .trc { background:#92400E; }
.t3 thead { background:#92400E; }
.t3 tbody tr.alt td { background:#FFFDE7; }
.t4.trh { background:#F3E8FF; color:#6B21A8; border-left-color:#6B21A8; }
.t4.trc,.t4 .trc { background:#6B21A8; }
.t4 thead { background:#6B21A8; }
.t4 tbody tr.alt td { background:#FAF5FF; }
table { width:100%; border-collapse:collapse; }
th { color:#fff; font-size:10px; font-weight:700; text-align:left; padding:5px 9px; border-right:1px solid rgba(255,255,255,.18); -webkit-print-color-adjust:exact; print-color-adjust:exact; }
th:last-child { border-right:none; }
th:first-child { width:78px; }
th:nth-child(2) { width:36px; }
th:last-child { width:108px; }
td { padding:5px 9px; border-bottom:1px solid #e9eef4; border-right:1px solid #e9eef4; font-size:10.5px; vertical-align:middle; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
td:last-child { border-right:none; }
tr:last-child td { border-bottom:none; }
.nw { white-space:nowrap; }
.dim { color:#444; }
.dim2 { color:#222; }
.bold { font-weight:500; }
.pb { display:flex; gap:9px; margin-bottom:16px; }
.pb button { cursor:pointer; border:none; padding:7px 18px; border-radius:6px; font-size:12px; font-weight:600; }
.bp { background:#1E40AF; color:#fff; }
.bx { background:#f1f5f9; color:#475569; }
@media print { body{background:#fff;padding:0} .pw{box-shadow:none;border-radius:0;padding:8px} .no-p{display:none!important} .tb{break-inside:avoid} }`;

    const w = window.open('', '_blank', 'width=960,height=750');
    w.document.write(`<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Cronograma ${ano}</title><style>${css}</style></head><body>
<div class="pw">
  <div class="pb no-p">
    <button class="bp" onclick="window.print()">🖨️ Imprimir / Salvar como PDF</button>
    <button class="bx" onclick="window.close()">✕ Fechar</button>
  </div>
  <div class="dh">
    <div><div class="dt">Cronograma de Aulas — ${ano}</div>
    <div class="dm">Trimestre: <strong>${trimLabel}</strong> &nbsp;·&nbsp; Turma: <strong>${turmaLabel}</strong><br>Gerado em ${new Date().toLocaleDateString('pt-BR',{day:'2-digit',month:'long',year:'numeric'})}</div></div>
    <div class="dl">EB</div>
  </div>
  ${bodyHtml}
</div></body></html>`);
    w.document.close();
  }

  document.getElementById('btnFiltrarCron').addEventListener('click', carregarCronograma);
  var _timerCron;
  document.getElementById('cron-ano').addEventListener('input', function() {
    clearTimeout(_timerCron);
    _timerCron = setTimeout(carregarCronograma, 500);
  });
  document.getElementById('cron-ano').addEventListener('keydown', function(e) { if (e.key === 'Enter') { clearTimeout(_timerCron); carregarCronograma(); } });
  document.getElementById('cron-trimestre').addEventListener('change', carregarCronograma);
  document.getElementById('cron-turma').addEventListener('change', carregarCronograma);
  document.getElementById('btnExportarXls').addEventListener('click', exportarXls);
  document.getElementById('btnExportarPdf').addEventListener('click', exportarPdf);

  carregarCronograma();
})();
