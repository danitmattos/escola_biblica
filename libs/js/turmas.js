// ══════════════════════════════════════════════════
//  TURMAS — Listagem + Formulário (criar/editar)
// ══════════════════════════════════════════════════
(function initTurmas() {
  // ── Listagem ──────────────────────────────────
  const tbody = document.getElementById('tbody-turmas');
  if (tbody) {
    let excluirId = null;
    let pagTurma = 1;
    const POR_PAGINA_T = 25;
    const pagElT = document.getElementById('pag-turmas');

    function showTurmaAlert(msg, type) {
      const el = document.getElementById('turmas-alert');
      if (!el) return;
      el.innerHTML = `<div class="alert alert-${type}" style="padding:var(--space-3) var(--space-4)">${escHtmlT(msg)}</div>`;
      el.style.display = 'block';
      setTimeout(() => el.style.display = 'none', 4000);
    }

    function renderTurmas(turmas) {
      if (!turmas.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">Nenhuma turma encontrada.</td></tr>';
        return;
      }
      tbody.innerHTML = turmas.map(t => `
        <tr>
          <td style="color:var(--color-gray-400);font-size:var(--text-xs)">${t.id}</td>
          <td><strong>${escHtmlT(t.nome_turma)}</strong></td>
          <td><span class="badge badge-primary">${t.total_alunos} aluno${t.total_alunos !== 1 ? 's' : ''}</span></td>
          <td style="text-align:right;white-space:nowrap">
            <a href="index.php?pagina=turma-editar&id=${t.id}" class="btn btn-ghost btn-sm" title="Editar">
              <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
            </a>
            <button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" title="Excluir"
              onclick="abrirModalExcluirTurma(${t.id}, '${escHtmlT(t.nome_turma).replace(/'/g,"\\'")}')">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </button>
          </td>
        </tr>`).join('');
    }

    function carregarTurmas(page) {
      pagTurma = page || 1;
      const busca = document.getElementById('turma-busca')?.value.trim() || '';
      tbody.innerHTML = skeletonTable(4, 5);
      fetch('turmas_crud.php?' + new URLSearchParams({ busca, page: pagTurma, limit: POR_PAGINA_T }))
        .then(r => r.json())
        .then(data => {
          if (!data.ok) { showTurmaAlert(data.msg || 'Erro ao carregar.', 'danger'); return; }
          document.getElementById('total-turmas').textContent = data.total + ' turma' + (data.total !== 1 ? 's' : '');
          renderTurmas(data.turmas);
          renderPaginacao(pagElT, data.page, data.total_pages, carregarTurmas);
        })
        .catch(() => showTurmaAlert('Falha na comunicação com o servidor.', 'danger'));
    }

    carregarTurmas();

    document.getElementById('btnBuscarTurma')?.addEventListener('click', function() { carregarTurmas(1); });
    var _timerTurma;
    var elBuscaTurma = document.getElementById('turma-busca');
    if (elBuscaTurma) {
      elBuscaTurma.addEventListener('input', function() {
        clearTimeout(_timerTurma);
        _timerTurma = setTimeout(function() { carregarTurmas(1); }, 350);
      });
      elBuscaTurma.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); clearTimeout(_timerTurma); carregarTurmas(1); } });
    }

    window.abrirModalExcluirTurma = function(id, nome) {
      excluirId = id;
      document.getElementById('modal-nome-turma').textContent = nome;
      document.getElementById('modalExcluirTurma').style.display = 'flex';
    };

    document.getElementById('btnConfirmarExcluirTurma')?.addEventListener('click', function() {
      if (!excluirId) return;
      this.disabled = true;
      this.textContent = 'Excluindo…';
      fetch('turmas_crud.php?id=' + excluirId, { method: 'DELETE' })
        .then(r => r.json())
        .then(data => {
          document.getElementById('modalExcluirTurma').style.display = 'none';
          if (data.ok) {
            showTurmaAlert(data.msg, 'success');
            carregarTurmas();
          } else {
            showTurmaAlert(data.msg || 'Erro ao excluir.', 'danger');
          }
        })
        .catch(() => showTurmaAlert('Falha na comunicação.', 'danger'))
        .finally(() => { this.disabled = false; this.textContent = 'Excluir'; });
    });
  }

  // ── Formulário (criar / editar) ────────────────
  const formTurma = document.getElementById('formTurma');
  if (!formTurma) return;

  const modo     = formTurma.dataset.modo;
  const turmaId  = parseInt(formTurma.dataset.id || '0', 10);
  const btnSalvar = document.getElementById('btnSalvarTurma');

  function showTurmaFormAlert(msg, type) {
    const el = document.getElementById('turma-alert');
    if (!el) return;
    el.innerHTML = `<div class="alert alert-${type}" style="padding:var(--space-3) var(--space-4)">${msg.replace(/</g,'&lt;')}</div>`;
    el.style.display = 'block';
  }

  function clearTurmaErrors() {
    document.getElementById('nome_turma-error').textContent = '';
    document.getElementById('nome_turma').classList.remove('is-invalid');
    document.getElementById('nome_turma').classList.remove('is-valid');
  }

  // ── Validação em tempo real ────────────────────
  vfBind('nome_turma', 'nome_turma-error', vfRequired('Nome da turma'), {onInput:true});

  if (modo === 'editar' && turmaId) {
    fetch('turmas_crud.php?id=' + turmaId)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showTurmaFormAlert(data.msg || 'Turma não encontrada.', 'danger'); return; }
        document.getElementById('nome_turma').value = data.turma.nome_turma;
      })
      .catch(() => showTurmaFormAlert('Erro ao carregar dados da turma.', 'danger'));
  }

  formTurma.addEventListener('submit', function(e) {
    e.preventDefault();
    clearTurmaErrors();
    document.getElementById('turma-alert').style.display = 'none';

    const nome = document.getElementById('nome_turma').value.trim();
    if (!nome) {
      document.getElementById('nome_turma-error').textContent = 'O nome da turma é obrigatório.';
      document.getElementById('nome_turma').classList.add('is-invalid');
      return;
    }

    btnSalvar.disabled = true;
    btnSalvar.innerHTML = '<svg class="icon" style="animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg> Salvando…';

    const fd = new FormData();
    fd.append('nome_turma', nome);
    if (modo === 'editar') fd.append('id', turmaId);

    const url = modo === 'editar' ? 'turmas_crud.php?_method=PUT' : 'turmas_crud.php';
    fetch(url, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          if (data.erros?.nome_turma) {
            document.getElementById('nome_turma-error').textContent = data.erros.nome_turma;
            document.getElementById('nome_turma').classList.add('is-invalid');
          }
          showTurmaFormAlert(data.msg || 'Erro ao salvar.', 'danger');
        } else {
          showTurmaFormAlert(data.msg || 'Salvo com sucesso!', 'success');
          setTimeout(() => window.location.href = 'index.php?pagina=turmas', 1500);
        }
      })
      .catch(() => showTurmaFormAlert('Falha na comunicação com o servidor.', 'danger'))
      .finally(() => {
        btnSalvar.disabled = false;
        btnSalvar.innerHTML = modo === 'editar'
          ? '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar Alterações'
          : '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar Turma';
      });
  });
})();
