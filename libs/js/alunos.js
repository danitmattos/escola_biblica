// ════════════════════════════════════════════════
//  LISTAGEM DE ALUNOS
// ════════════════════════════════════════════════
(function() {
  if (!document.getElementById('tabela-alunos')) return;

  const tbody = document.getElementById('tbody-alunos');
  const totalEl = document.getElementById('total-alunos');
  const listaAlert = document.getElementById('lista-alert');
  const pagEl = document.getElementById('pag-alunos');
  let excluirId = null;
  let paginaAtual = 1;
  const POR_PAGINA = 25;

  function showListAlert(msg, tipo) {
    listaAlert.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + msg + '</span></div>';
    listaAlert.style.display = 'block';
    setTimeout(() => {
      listaAlert.style.display = 'none';
    }, 4000);
  }

  function renderTabela(alunos) {
    if (!alunos.length) {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">Nenhum aluno encontrado.</td></tr>';
      return;
    }
    tbody.innerHTML = alunos.map(a => {
      const initials = escHtml(a.nome).trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase();
      const avatarHtml = a.foto
        ? `<img src="${escHtml(a.foto)}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;display:block">`
        : `<div style="width:36px;height:36px;border-radius:50%;background:var(--color-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:600;flex-shrink:0">${initials}</div>`;
      return `
    <tr>
      <td style="color:var(--color-gray-400);font-size:var(--text-xs)">${a.id}</td>
      <td>${avatarHtml}</td>
      <td><strong>${escHtml(a.nome)}</strong><br><small class="text-muted">${escHtml(a.usuario_email || '')}</small></td>
      <td>${escHtml(a.turma || '—')}</td>
      <td>${fmtTel(a.telefone)}</td>
      <td>${fmtData(a.data_matricula)}</td>
      <td>${badgeStatus(a.status)}</td>
      <td style="text-align:right;white-space:nowrap">
        <a href="index.php?pagina=aluno-editar&id=${a.id}" class="btn btn-ghost btn-sm" title="Editar">
          <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
        </a>
        <button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" title="Excluir" onclick="abrirModalExcluir(${a.id}, '${escHtml(a.nome).replace(/'/g,"\\'")}')">
          <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        </button>
      </td>
    </tr>`;
    }).join('');
  }

  function carregarAlunos(page) {
    paginaAtual = page || 1;
    const busca = document.getElementById('filtro-busca').value.trim();
    const status = document.getElementById('filtro-status').value;
    const turma = document.getElementById('filtro-turma').value;

    tbody.innerHTML = skeletonTable(8, 5, {avatar: 1});

    const params = new URLSearchParams({
      busca,
      status,
      turma,
      page: paginaAtual,
      limit: POR_PAGINA
    });
    fetch('alunos_crud.php?' + params)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          showListAlert(data.msg || 'Erro ao carregar.', 'danger');
          return;
        }
        totalEl.textContent = data.total + ' aluno' + (data.total !== 1 ? 's' : '');
        renderTabela(data.alunos);
        renderPaginacao(pagEl, data.page, data.total_pages, carregarAlunos);
      })
      .catch(() => showListAlert('Falha na comunicação com o servidor.', 'danger'));
  }

  // Modal exclusão
  window.abrirModalExcluir = function(id, nome) {
    excluirId = id;
    document.getElementById('modal-nome-aluno').textContent = nome;
    document.getElementById('modalExcluir').style.display = 'flex';
  };

  document.getElementById('btnConfirmarExcluir').addEventListener('click', function() {
    if (!excluirId) return;
    this.disabled = true;
    this.textContent = 'Excluindo…';
    fetch('alunos_crud.php?id=' + excluirId, {
        method: 'DELETE'
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalExcluir').style.display = 'none';
        showListAlert(data.msg || (data.ok ? 'Excluído.' : 'Erro.'), data.ok ? 'success' : 'danger');
        if (data.ok) carregarAlunos();
      })
      .catch(() => showListAlert('Falha ao excluir.', 'danger'))
      .finally(() => {
        this.disabled = false;
        this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> Excluir';
        excluirId = null;
      });
  });

  document.getElementById('btnFiltrar').addEventListener('click', function() { carregarAlunos(1); });
  var _timerAlunos;
  document.getElementById('filtro-busca').addEventListener('input', function() {
    clearTimeout(_timerAlunos);
    _timerAlunos = setTimeout(function() { carregarAlunos(1); }, 350);
  });
  document.getElementById('filtro-busca').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); clearTimeout(_timerAlunos); carregarAlunos(1); }
  });
  document.getElementById('filtro-status').addEventListener('change', function() { carregarAlunos(1); });
  document.getElementById('filtro-turma').addEventListener('change', function() { carregarAlunos(1); });
  // Carga inicial
  carregarAlunos();
})();
