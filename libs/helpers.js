/**
 * libs/helpers.js — Funções utilitárias globais compartilhadas entre todas as páginas.
 * Carregado uma única vez no <head> do index.php.
 */

/* ── Escape HTML ──────────────────────────────────────── */
function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
// Aliases usados em diferentes módulos
var escHtml  = esc;
var escH     = esc;
var escHtmlT = esc;

/* ── Formatação de datas ──────────────────────────────── */
function fmtData(s) {
  if (!s) return '—';
  var parts = s.split('-');
  return parts[2] + '/' + parts[1] + '/' + parts[0];
}
var fmtDataBR = fmtData;

function fmtHora(t) {
  if (!t) return '';
  if (t.length > 8) return t.substring(11, 16); // datetime
  var parts = t.split(':');
  return parts[0] + ':' + parts[1];
}

/* ── Formatação de telefone ───────────────────────────── */
function fmtTel(n) {
  if (!n) return '—';
  var s = String(n).replace(/\D/g, '');
  if (s.length === 11) return '(' + s.slice(0,2) + ') ' + s.slice(2,7) + '-' + s.slice(7);
  if (s.length === 10) return '(' + s.slice(0,2) + ') ' + s.slice(2,6) + '-' + s.slice(6);
  return n;
}

/* ── Badges ───────────────────────────────────────────── */
function badgeStatus(s) {
  var map   = { ativo: 'success', pendente: 'warning', inativo: 'danger' };
  var label = { ativo: 'Ativo',   pendente: 'Pendente', inativo: 'Inativo' };
  return '<span class="badge badge-' + (map[s] || 'primary') + '">' + (label[s] || s) + '</span>';
}

/* ── Cores de percentual (frequência) ─────────────────── */
function corPct(p) {
  if (p === null || p === undefined) return 'var(--color-text-muted)';
  if (p >= 75) return 'var(--color-success,#16a34a)';
  if (p >= 50) return 'var(--color-warning,#d97706)';
  return 'var(--color-danger,#dc2626)';
}

/* ── Busca instantânea (debounce) ─────────────────────── */
function debounce(fn, ms) {
  var timer;
  return function() {
    var ctx = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function() { fn.apply(ctx, args); }, ms);
  };
}

/* ── Skeleton Loading Helpers ─────────────────────────── */

/**
 * Gera linhas de skeleton para tabelas.
 * @param {number} cols     - número de colunas da tabela
 * @param {number} [rows]   - número de linhas skeleton (padrão 5)
 * @param {object} [opts]   - { avatar: col index (0-based) para mostrar círculo }
 */
function skeletonTable(cols, rows, opts) {
  rows = rows || 5;
  opts = opts || {};
  var html = '';
  var widths = ['60%','45%','70%','35%','50%','55%','40%'];
  for (var r = 0; r < rows; r++) {
    html += '<tr>';
    for (var c = 0; c < cols; c++) {
      if (opts.avatar !== undefined && c === opts.avatar) {
        html += '<td><div class="sk sk-circle"></div></td>';
      } else {
        var w = widths[(r + c) % widths.length];
        html += '<td><div class="sk sk-h-4" style="width:' + w + '"></div></td>';
      }
    }
    html += '</tr>';
  }
  return html;
}

/**
 * Gera skeleton para cards do dashboard.
 * @param {number} [count] - número de cards/blocos (padrão 3)
 */
function skeletonCards(count) {
  count = count || 3;
  var html = '';
  for (var i = 0; i < count; i++) {
    html += '<div class="sk-card">' +
      '<div class="sk sk-h-5 sk-w-1\/3" style="margin-bottom:8px"></div>' +
      '<div class="sk sk-h-4 sk-w-3\/4"></div>' +
      '</div>';
  }
  return html;
}

/**
 * Gera skeleton para seções (temas, cronograma).
 * @param {number} [count] - número de seções (padrão 2)
 */
function skeletonSections(count) {
  count = count || 2;
  var html = '';
  for (var i = 0; i < count; i++) {
    html += '<div class="sk-section">' +
      '<div class="sk sk-h-5 sk-w-1\/3" style="margin-bottom:12px"></div>' +
      '<div style="display:flex;flex-direction:column;gap:8px">' +
      '<div class="sk sk-h-4 sk-w-full"></div>' +
      '<div class="sk sk-h-4 sk-w-3\/4"></div>' +
      '<div class="sk sk-h-4 sk-w-1\/2"></div>' +
      '</div></div>';
  }
  return html;
}

/**
 * Gera skeleton para listas de barras de progresso (frequência dashboard).
 * @param {number} [count] - número de barras (padrão 4)
 */
function skeletonBars(count) {
  count = count || 4;
  var html = '';
  for (var i = 0; i < count; i++) {
    html += '<div style="margin-bottom:var(--space-4)">' +
      '<div style="display:flex;justify-content:space-between;margin-bottom:4px">' +
      '<div class="sk sk-h-4" style="width:120px"></div>' +
      '<div class="sk sk-h-4" style="width:40px"></div>' +
      '</div>' +
      '<div class="sk sk-h-4 sk-w-full" style="border-radius:var(--radius-full)"></div>' +
      '</div>';
  }
  return html;
}

/* ── Validação em tempo real ──────────────────────────── */

/**
 * Marca um campo como válido, inválido ou neutro.
 * @param {string} inputId   - id do <input/select/textarea>
 * @param {string} errorId   - id do <span class="form-error"> associado
 * @param {string} msg       - mensagem de erro ('' = limpa)
 * @param {boolean} [valid]  - true = borda verde + mensagem sucesso
 */
function vf(inputId, errorId, msg, valid) {
  var el  = document.getElementById(inputId);
  var err = document.getElementById(errorId);
  if (!el) return;
  el.classList.remove('is-invalid', 'is-valid');
  if (err) { err.textContent = ''; err.classList.remove('form-success'); }
  if (msg) {
    el.classList.add('is-invalid');
    if (err) err.textContent = msg;
  } else if (valid) {
    el.classList.add('is-valid');
    if (err) { err.textContent = '✓'; err.classList.add('form-success'); }
  }
}

/**
 * Atalho: liga validação em tempo real (blur + input) a um campo.
 * @param {string} inputId
 * @param {string} errorId
 * @param {function} validatorFn - recebe value, retorna string de erro ou '' se ok
 * @param {object}  [opts]       - {onInput:bool} se true valida a cada keystroke
 */
function vfBind(inputId, errorId, validatorFn, opts) {
  var el = document.getElementById(inputId);
  if (!el) return;
  var touched = false;
  el.addEventListener('blur', function() {
    touched = true;
    var msg = validatorFn(el.value);
    vf(inputId, errorId, msg, !msg && !!el.value);
  });
  if (opts && opts.onInput) {
    el.addEventListener('input', function() {
      if (!touched) return;
      var msg = validatorFn(el.value);
      vf(inputId, errorId, msg, !msg && !!el.value);
    });
  }
  el.addEventListener('change', function() {
    touched = true;
    var msg = validatorFn(el.value);
    vf(inputId, errorId, msg, !msg && !!el.value);
  });
}

/* Validadores reutilizáveis */
function vfRequired(label) {
  return function(v) { return v.trim() ? '' : (label || 'Campo') + ' é obrigatório.'; };
}
function vfNomeCompleto(v) {
  v = v.trim();
  if (!v) return 'O nome é obrigatório.';
  if (v.split(/\s+/).length < 2) return 'Informe o nome completo.';
  return '';
}
function vfEmail(v) {
  v = v.trim();
  if (!v) return '';
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'E-mail inválido.';
}
function vfTelefone(v) {
  var d = v.replace(/\D/g, '');
  if (!d) return 'O telefone é obrigatório.';
  if (d.length < 10) return 'Telefone incompleto.';
  return '';
}
function vfCpf(v) {
  var d = v.replace(/\D/g, '');
  if (!d) return '';
  if (d.length !== 11) return 'CPF incompleto (11 dígitos).';
  if (/^(\d)\1{10}$/.test(d)) return 'CPF inválido.';
  for (var t = 9; t < 11; t++) {
    var sum = 0;
    for (var i = 0; i < t; i++) sum += parseInt(d.charAt(i)) * (t + 1 - i);
    var r = (sum * 10) % 11;
    if (r === 10) r = 0;
    if (parseInt(d.charAt(t)) !== r) return 'CPF inválido.';
  }
  return '';
}
function vfSelect(label) {
  return function(v) { return v ? '' : 'Selecione ' + (label || 'uma opção') + '.'; };
}
function vfMinLen(min, label) {
  return function(v) {
    if (!v.trim()) return (label || 'Campo') + ' é obrigatório.';
    if (v.trim().length < min) return 'Mínimo de ' + min + ' caracteres.';
    return '';
  };
}

/* ── Export: XLS (SpreadsheetML) ───────────────────────── */
function xlsCell(val, styleId, type) {
  type = type || 'String';
  var safe = String(val === null || val === undefined ? '' : val)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  return '<Cell' + (styleId ? ' ss:StyleID="' + styleId + '"' : '') + '>' +
    '<Data ss:Type="' + type + '">' + safe + '</Data></Cell>';
}

function xlsDownload(xml, filename) {
  var blob = new Blob(['\uFEFF' + xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
  var url  = URL.createObjectURL(blob);
  var a    = document.createElement('a');
  a.href = url;
  a.download = filename + '.xls';
  a.click();
  URL.revokeObjectURL(url);
}

function xlsWrap(stylesHtml, sheetName, colDefs, rows) {
  return '<' + '?xml version="1.0" encoding="UTF-8"?>\n' +
    '<' + '?mso-application progid="Excel.Sheet"?>\n' +
    '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n' +
    '<Styles>' + stylesHtml + '</Styles>\n' +
    '<Worksheet ss:Name="' + esc(sheetName) + '">\n<Table>\n' + colDefs + '\n' + rows + '</Table>\n</Worksheet>\n</Workbook>';
}

var XLS_STY = {
  hdr: '<Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="10"/><Interior ss:Color="#1E40AF" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/></Style>',
  sub: '<Style ss:ID="sub"><Font ss:Bold="1" ss:Size="10"/><Interior ss:Color="#DBEAFE" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  tot: '<Style ss:ID="tot"><Font ss:Bold="1" ss:Color="#166534" ss:Size="10"/><Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  pre: '<Style ss:ID="pre"><Font ss:Color="#166534"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  aus: '<Style ss:ID="aus"><Font ss:Color="#9CA3AF"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  ftr: '<Style ss:ID="ftr"><Font ss:Bold="1" ss:Size="10"/><Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  dng: '<Style ss:ID="dng"><Font ss:Bold="1" ss:Color="#991B1B" ss:Size="10"/><Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
  wrn: '<Style ss:ID="wrn"><Font ss:Bold="1" ss:Color="#92400E" ss:Size="10"/><Interior ss:Color="#FEF9C3" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>',
};

/* ── Export: PDF (styled print window) ────────────────── */
function pdfOpen(opts) {
  var title    = opts.title || 'Relatório';
  var subtitle = opts.subtitle || '';
  var body     = opts.body || '';
  var extraCss = opts.css || '';
  var orient   = opts.orientation || 'portrait';
  var hoje = new Date().toLocaleDateString('pt-BR', {day:'2-digit',month:'long',year:'numeric'});

  var css =
    '* { box-sizing:border-box; margin:0; padding:0; }' +
    'body { font-family:Arial,sans-serif; font-size:11px; color:#111; background:#f1f5f9; padding:18px; }' +
    '.pw { max-width:' + (orient==='landscape'?'1100px':'860px') + '; margin:0 auto; background:#fff; padding:22px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.1); }' +
    '.dh { display:flex; align-items:flex-start; justify-content:space-between; border-bottom:3px solid #1E40AF; padding-bottom:11px; margin-bottom:18px; }' +
    '.dt { font-size:17px; font-weight:700; color:#1E3A8A; }' +
    '.dm { font-size:10px; color:#374151; margin-top:3px; line-height:1.6; }' +
    '.dl { font-size:20px; background:#1E40AF; color:#fff; border-radius:7px; padding:5px 11px; font-weight:700; }' +
    'table { width:100%; border-collapse:collapse; margin-bottom:12px; }' +
    'thead th { background:#1E40AF; color:#fff; font-size:10px; font-weight:700; text-align:center; padding:6px 8px; -webkit-print-color-adjust:exact; print-color-adjust:exact; }' +
    'thead th:first-child { text-align:left; }' +
    'td { padding:5px 8px; border-bottom:1px solid #e5e7eb; font-size:10.5px; text-align:center; -webkit-print-color-adjust:exact; print-color-adjust:exact; }' +
    'td:first-child { text-align:left; }' +
    'tr:nth-child(even) td { background:#f8fafc; }' +
    '.badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:9px; font-weight:600; }' +
    '.badge-danger { background:#FEE2E2; color:#991B1B; }' +
    '.badge-warning { background:#FEF9C3; color:#92400E; }' +
    '.badge-success { background:#DCFCE7; color:#166534; }' +
    '.bar-wrap { height:6px; background:#e5e7eb; border-radius:3px; overflow:hidden; margin-top:3px; }' +
    '.bar-fill { height:100%; border-radius:3px; }' +
    '.summary-cards { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }' +
    '.s-card { flex:1; min-width:120px; border:1px solid #e5e7eb; border-radius:6px; padding:10px; text-align:center; -webkit-print-color-adjust:exact; print-color-adjust:exact; }' +
    '.s-card-val { font-size:20px; font-weight:800; }' +
    '.s-card-lbl { font-size:9px; color:#6b7280; margin-top:2px; }' +
    '.doc-footer { margin-top:18px; padding-top:10px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:9px; color:#94a3b8; }' +
    '.pb { display:flex; gap:9px; margin-bottom:18px; }' +
    '.pb button { cursor:pointer; border:none; padding:8px 20px; border-radius:6px; font-size:12px; font-weight:600; }' +
    '.bp { background:#1E40AF; color:#fff; }' +
    '.bx { background:#f1f5f9; color:#475569; }' +
    '@media print { body{background:#fff;padding:0} .pw{box-shadow:none;border-radius:0;padding:8px} .pb{display:none!important} @page{size:A4 ' + orient + ';margin:10mm} }' +
    extraCss;

  var w = window.open('', '_blank', 'width=960,height=750');
  w.document.write(
    '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>' + esc(title) + '</title><style>' + css + '</style></head><body>' +
    '<div class="pw">' +
    '<div class="pb"><button class="bp" onclick="window.print()">\ud83d\udda8\ufe0f Imprimir / Salvar como PDF</button><button class="bx" onclick="window.close()">\u2715 Fechar</button></div>' +
    '<div class="dh"><div><div class="dt">' + esc(title) + '</div>' +
    '<div class="dm">' + subtitle + '</div></div>' +
    '<div class="dl">EB</div></div>' +
    body +
    '<div class="doc-footer"><span>Escola B\u00edblica \u2014 Sistema de Gest\u00e3o</span><span>Gerado em ' + hoje + '</span></div>' +
    '</div></body></html>'
  );
  w.document.close();
}

/* ── Paginação ────────────────────────────────────────── */
function renderPaginacao(container, paginaAtual, totalPaginas, callback) {
  if (!container) return;
  if (totalPaginas <= 1) { container.style.display = 'none'; return; }
  container.style.display = 'flex';
  var html = '';
  html += '<button class="pag-btn" ' + (paginaAtual <= 1 ? 'disabled' : '') + ' data-p="' + (paginaAtual - 1) + '">&laquo;</button>';
  var start = Math.max(1, paginaAtual - 2);
  var end   = Math.min(totalPaginas, paginaAtual + 2);
  if (start > 1) html += '<button class="pag-btn" data-p="1">1</button>';
  if (start > 2) html += '<span class="pag-dots">&hellip;</span>';
  for (var i = start; i <= end; i++) {
    html += '<button class="pag-btn' + (i === paginaAtual ? ' active' : '') + '" data-p="' + i + '">' + i + '</button>';
  }
  if (end < totalPaginas - 1) html += '<span class="pag-dots">&hellip;</span>';
  if (end < totalPaginas) html += '<button class="pag-btn" data-p="' + totalPaginas + '">' + totalPaginas + '</button>';
  html += '<button class="pag-btn" ' + (paginaAtual >= totalPaginas ? 'disabled' : '') + ' data-p="' + (paginaAtual + 1) + '">&raquo;</button>';
  container.innerHTML = html;
  container.querySelectorAll('.pag-btn:not([disabled])').forEach(function(btn) {
    btn.addEventListener('click', function() { callback(parseInt(this.dataset.p)); });
  });
}
