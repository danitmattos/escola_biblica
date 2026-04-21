// ════════════════════════════════════════════════
//  LISTAGEM DE PROFESSORES
// ════════════════════════════════════════════════
(function() {
  if (!document.getElementById('tabela-professores')) return;

  const tbody    = document.getElementById('tbody-professores');
  const totalEl  = document.getElementById('total-professores');
  const alertEl  = document.getElementById('lista-prof-alert');
  let excluirId  = null;

  function showAlert(msg, tipo) {
    alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + msg + '</span></div>';
    alertEl.style.display = 'block';
    setTimeout(() => alertEl.style.display = 'none', 4000);
  }

  function renderTabela(professores) {
    if (!professores.length) {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">Nenhum professor encontrado.</td></tr>';
      return;
    }
    tbody.innerHTML = professores.map(a => {
      const initials = escH(a.nome).trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase();
      const avatar = a.foto
        ? `<img src="${escH(a.foto)}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;display:block">`
        : `<div style="width:36px;height:36px;border-radius:50%;background:var(--color-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:600">${initials}</div>`;
      return `<tr>
        <td style="color:var(--color-gray-400);font-size:var(--text-xs)">${a.id}</td>
        <td>${avatar}</td>
        <td><strong>${escH(a.nome)}</strong><br><small class="text-muted">${escH(a.usuario_email || '')}</small></td>
        <td>${escH(a.turma || '—')}</td>
        <td>${fmtTel(a.telefone)}</td>
        <td>${fmtData(a.data_matricula)}</td>
        <td>${badgeStatus(a.status)}</td>
        <td style="text-align:right;white-space:nowrap">
          <a href="index.php?pagina=professor-editar&id=${a.id}" class="btn btn-ghost btn-sm" title="Editar">
            <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
          </a>
          <button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" title="Excluir"
            onclick="abrirModalExcluirProf(${a.id}, '${escH(a.nome).replace(/'/g,"\\'")}')">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          </button>
        </td>
      </tr>`;
    }).join('');
  }

  function carregarProfessores() {
    const busca  = document.getElementById('filtro-prof-busca').value.trim();
    const status = document.getElementById('filtro-prof-status').value;
    const turma  = document.getElementById('filtro-prof-turma').value;
    tbody.innerHTML = skeletonTable(8, 5, {avatar: 1});
    fetch('alunos_crud.php?' + new URLSearchParams({ busca, status, turma, docente: 'S' }))
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.msg || 'Erro ao carregar.', 'danger'); return; }
        totalEl.textContent = data.total + ' professor' + (data.total !== 1 ? 'es' : '');
        renderTabela(data.alunos);
      })
      .catch(() => showAlert('Falha na comunicação com o servidor.', 'danger'));
  }

  window.abrirModalExcluirProf = function(id, nome) {
    excluirId = id;
    document.getElementById('modal-nome-prof').textContent = nome;
    document.getElementById('modalExcluirProf').style.display = 'flex';
  };

  document.getElementById('btnConfirmarExcluirProf').addEventListener('click', function() {
    if (!excluirId) return;
    this.disabled = true;
    this.textContent = 'Excluindo…';
    fetch('alunos_crud.php?id=' + excluirId, { method: 'DELETE' })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalExcluirProf').style.display = 'none';
        showAlert(data.msg || (data.ok ? 'Excluído.' : 'Erro.'), data.ok ? 'success' : 'danger');
        if (data.ok) carregarProfessores();
      })
      .catch(() => showAlert('Falha ao excluir.', 'danger'))
      .finally(() => {
        this.disabled = false;
        this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> Excluir';
        excluirId = null;
      });
  });

  document.getElementById('btnFiltrarProf').addEventListener('click', carregarProfessores);
  var _timerProf;
  document.getElementById('filtro-prof-busca').addEventListener('input', function() {
    clearTimeout(_timerProf);
    _timerProf = setTimeout(carregarProfessores, 350);
  });
  document.getElementById('filtro-prof-busca').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); clearTimeout(_timerProf); carregarProfessores(); }
  });
  document.getElementById('filtro-prof-status').addEventListener('change', carregarProfessores);
  document.getElementById('filtro-prof-turma').addEventListener('change', carregarProfessores);

  carregarProfessores();
})();
