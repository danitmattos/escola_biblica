// ══════════════════════════════════════════════════
//  VENDAS DE REVISTAS
// ══════════════════════════════════════════════════
(function(){
  // Carregar Chart.js
  var chartScript = document.createElement('script');
  chartScript.src = 'libs/chart.min.js';
  document.head.appendChild(chartScript);
  if(!document.getElementById('vd-container')) return;

  const API = 'vendas_crud.php';
  let abaAtual = 'historico';
  let quitarVendaId = null;

  /* ── Helpers ── */
  function showAlert(elId, msg, tipo){
    var el = document.getElementById(elId);
    if(!el) return;
    if(!msg){ el.style.display='none'; return; }
    el.className = 'alert alert-' + tipo;
    el.textContent = msg;
    el.style.display = '';
  }

  function fmtRS(v){ return 'R$ ' + parseFloat(v).toFixed(2).replace('.', ','); }

  var formaLabels = {dinheiro:'Dinheiro', pix:'Pix', cartao:'Cartão', transferencia:'Transferência'};

  function fmtDataHora(s){
    if(!s) return '—';
    var d = new Date(s.replace(' ','T'));
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
  }

  /* ── Carregar selects ── */
  fetch(API+'?recurso=anos').then(function(r){return r.json();}).then(function(data){
    var sel = document.getElementById('vd-ano');
    sel.innerHTML = '';
    (data.anos||[]).forEach(function(a){
      var o = document.createElement('option');
      o.value = a; o.textContent = a; sel.appendChild(o);
    });
  });

  function carregarPessoas(){
    fetch(API+'?recurso=pessoas').then(function(r){return r.json();}).then(function(data){
      var sel = document.getElementById('vd-pessoa');
      sel.innerHTML = '<option value="">— Selecionar —</option>';
      (data.pessoas||[]).forEach(function(p){
        var o = document.createElement('option');
        o.value = p.id;
        o.textContent = p.nome + (p.docente === 'S' ? ' (Prof.)' : '');
        o.dataset.docente = p.docente;
        sel.appendChild(o);
      });
    });
  }
  carregarPessoas();

  /* Auto-select tipo de revista baseado no selecionado */
  document.getElementById('vd-pessoa').addEventListener('change', function(){
    var opt = this.options[this.selectedIndex];
    if(opt && opt.dataset.docente === 'S'){
      document.getElementById('vd-tipo-revista').value = 'professor';
    } else {
      document.getElementById('vd-tipo-revista').value = 'aluno';
    }
  });

  /* Preencher ano no modal */
  function preencherModalAno(){
    var sel = document.getElementById('vd-modal-ano');
    var anoAtual = new Date().getFullYear();
    sel.innerHTML = '';
    for(var y = anoAtual; y >= anoAtual - 2; y--){
      var o = document.createElement('option');
      o.value = y; o.textContent = y; sel.appendChild(o);
    }
  }
  preencherModalAno();

  /* Pré-selecionar trimestre atual */
  var triAtual = Math.min(4, Math.ceil((new Date().getMonth()+1)/3));
  document.getElementById('vd-modal-tri').value = triAtual;

  /* Desabilitar forma pgto quando fiado */
  document.getElementById('vd-fiado').addEventListener('change', function(){
    document.getElementById('vd-forma').disabled = this.checked;
  });

  /* ── Abas ── */
  document.querySelectorAll('[data-vd-tab]').forEach(function(btn){
    btn.addEventListener('click', function(){
      abaAtual = this.dataset.vdTab;
      document.querySelectorAll('.vd-tab').forEach(function(t){ t.classList.remove('active'); });
      this.classList.add('active');
      document.getElementById('vd-filtros-card').style.display = abaAtual === 'historico' ? '' : 'none';
      carregar();
    });
  });

  /* ── Carregar dados ── */
  function carregar(){
    carregarResumo();
    if(abaAtual === 'historico') carregarHistorico();
    else carregarDevedores();
  }

  var vdPizzaChart = null;
  function carregarResumo(){
    var ano = document.getElementById('vd-ano').value || new Date().getFullYear();
    var tri = document.getElementById('vd-trimestre').value;
    var url = API+'?recurso=resumo&ano='+ano;
    if(tri) url += '&trimestre='+tri;

    fetch(url).then(function(r){return r.json();}).then(function(data){
      if(!data.ok) return;
      var r = data.resumo;
      document.getElementById('vd-total-vendas').textContent = fmtRS(r.total_valor);
      document.getElementById('vd-total-pago').textContent = fmtRS(r.total_pago);
      document.getElementById('vd-total-debito').textContent = fmtRS(r.total_debito);
      document.getElementById('vd-qtd-revistas').textContent = r.total_vendas;

      // Gráfico de pizza
      function renderPizza(){
        var ctx = document.getElementById('vd-pizza');
        if(!ctx || typeof window.Chart === 'undefined') return setTimeout(renderPizza, 100);
        var dados = [parseFloat(r.total_pago), parseFloat(r.total_debito)];
        var labels = ['Pagas', 'Em débito'];
        var cores = ['#22c55e', '#f59e42'];
        if(vdPizzaChart) vdPizzaChart.destroy();
        vdPizzaChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: labels,
            datasets: [{
              data: dados,
              backgroundColor: cores,
              borderWidth: 1
            }]
          },
          options: {
            plugins: {
              legend: { display: true, position: 'bottom' },
              tooltip: { enabled: true }
            }
          }
        });
      }
      renderPizza();
    });
  }

  function carregarHistorico(){
    var ano    = document.getElementById('vd-ano').value || new Date().getFullYear();
    var tri    = document.getElementById('vd-trimestre').value;
    var status = document.getElementById('vd-status').value;

    var url = API+'?recurso=historico&ano='+ano;
    if(tri) url += '&trimestre='+tri;
    if(status) url += '&status='+status;

    document.getElementById('vd-loading').style.display = '';
    document.getElementById('vd-container').innerHTML = '';
    showAlert('vd-alert','',null);

    fetch(url).then(function(r){return r.json();}).then(function(data){
      document.getElementById('vd-loading').style.display = 'none';
      if(!data.ok){ showAlert('vd-alert', data.msg||'Erro.','danger'); return; }
      renderHistorico(data.vendas||[]);
    }).catch(function(){
      document.getElementById('vd-loading').style.display = 'none';
      showAlert('vd-alert','Falha na comunicação.','danger');
    });
  }

  function carregarDevedores(){
    document.getElementById('vd-loading').style.display = '';
    document.getElementById('vd-container').innerHTML = '';
    showAlert('vd-alert','',null);

    fetch(API+'?recurso=devedores').then(function(r){return r.json();}).then(function(data){
      document.getElementById('vd-loading').style.display = 'none';
      if(!data.ok){ showAlert('vd-alert', data.msg||'Erro.','danger'); return; }
      renderDevedores(data.devedores||[]);
    }).catch(function(){
      document.getElementById('vd-loading').style.display = 'none';
      showAlert('vd-alert','Falha na comunicação.','danger');
    });
  }

  /* ── Render Histórico ── */
  function renderHistorico(vendas){
    if(!vendas.length){
      document.getElementById('vd-container').innerHTML =
        '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
        'Nenhuma venda encontrada para os filtros selecionados.</div></div>';
      return;
    }

    var html = '<div class="card"><div style="overflow-x:auto"><table class="vd-table">';
    html += '<thead><tr><th>#</th><th>Pessoa</th><th>Tipo</th><th>Trimestre</th><th>Valor</th><th>Pagamento</th><th>Status</th><th>Data</th><th style="text-align:center">Ações</th></tr></thead><tbody>';

    vendas.forEach(function(v, i){
      var tipoLabel = v.tipo_revista === 'professor' ? 'Professor' : 'Aluno';
      var triLabel = v.trimestre + 'ºT/' + v.ano;
      var statusBadge = '<span class="vd-badge vd-badge--' + v.status_pgto + '">' + (v.status_pgto === 'pago' ? 'Pago' : 'Fiado') + '</span>';
      var formaLabel = v.status_pgto === 'pago' ? '<span class="vd-forma">' + esc(formaLabels[v.forma_pagamento]||v.forma_pagamento) + '</span>' : '—';

      html += '<tr>';
      html += '<td>' + (i+1) + '</td>';
      html += '<td>' + esc(v.aluno_nome) + (v.docente === 'S' ? ' <span style="color:var(--color-text-muted);font-size:var(--text-xs)">(Prof.)</span>' : '') + '</td>';
      html += '<td>' + tipoLabel + '</td>';
      html += '<td>' + triLabel + '</td>';
      html += '<td style="font-weight:600">' + fmtRS(v.valor) + '</td>';
      html += '<td>' + formaLabel + '</td>';
      html += '<td>' + statusBadge + '</td>';
      html += '<td style="font-size:var(--text-xs);color:var(--color-text-muted)">' + fmtDataHora(v.criado_em) + '</td>';
      html += '<td style="text-align:center;white-space:nowrap">';
      if(v.status_pgto === 'fiado'){
        html += '<button class="btn btn-primary" style="padding:3px 10px;font-size:var(--text-xs)" data-quitar="'+v.id+'" data-nome="'+esc(v.aluno_nome)+'" data-valor="'+v.valor+'">Quitar</button> ';
      }
      html += '<button class="btn btn-ghost btn-sm" style="color:var(--color-danger);padding:3px 6px" data-excluir="'+v.id+'" title="Excluir">' +
        '<svg style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>' +
        '</button>';
      html += '</td>';
      html += '</tr>';
    });

    html += '</tbody></table></div></div>';
    document.getElementById('vd-container').innerHTML = html;

    /* Event delegation para botões */
    document.getElementById('vd-container').addEventListener('click', function(e){
      var btnQ = e.target.closest('[data-quitar]');
      if(btnQ){
        quitarVendaId = parseInt(btnQ.dataset.quitar);
        document.getElementById('vd-quitar-info').innerHTML =
          'Confirmar pagamento de <strong>' + fmtRS(btnQ.dataset.valor) + '</strong> de <strong>' + esc(btnQ.dataset.nome) + '</strong>?';
        document.getElementById('vd-modal-quitar').style.display = '';
        return;
      }
      var btnE = e.target.closest('[data-excluir]');
      if(btnE){
        if(!confirm('Tem certeza que deseja excluir esta venda?')) return;
        var vid = btnE.dataset.excluir;
        fetch(API+'?recurso=venda&id='+vid, {method:'DELETE'})
          .then(function(r){return r.json();})
          .then(function(data){
            if(!data.ok){ showAlert('vd-alert', data.msg, 'danger'); return; }
            showAlert('vd-alert', data.msg, 'success');
            carregar();
          });
      }
    });
  }

  /* ── Render Devedores ── */
  function renderDevedores(devedores){
    if(!devedores.length){
      document.getElementById('vd-container').innerHTML =
        '<div class="card"><div class="card-body" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
        '<svg style="width:40px;height:40px;fill:currentColor;margin:0 auto var(--space-3);display:block;opacity:.3" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' +
        '<p style="margin:0">Nenhum aluno em débito no momento. Todos os pagamentos estão em dia!</p></div></div>';
      return;
    }

    var html = '<div class="card"><div class="card-header"><span class="card-title">Alunos em Débito (' + devedores.length + ')</span></div>';
    html += '<div style="overflow-x:auto"><table class="vd-table">';
    html += '<thead><tr><th>#</th><th>Pessoa</th><th>Revistas Pendentes</th><th>Períodos</th><th style="text-align:right">Total em Débito</th></tr></thead><tbody>';

    devedores.forEach(function(d, i){
      html += '<tr>';
      html += '<td>' + (i+1) + '</td>';
      html += '<td>' + esc(d.aluno_nome) + (d.docente === 'S' ? ' <span style="color:var(--color-text-muted);font-size:var(--text-xs)">(Prof.)</span>' : '') + '</td>';
      html += '<td>' + d.qtd_fiado + '</td>';
      html += '<td style="font-size:var(--text-xs);color:var(--color-text-muted)">' + esc(d.periodos) + '</td>';
      html += '<td style="text-align:right;font-weight:700;color:var(--color-danger)">' + fmtRS(d.total_divida) + '</td>';
      html += '</tr>';
    });

    html += '</tbody></table></div></div>';
    document.getElementById('vd-container').innerHTML = html;
  }

  /* ── Modal: Nova Venda ── */
  document.getElementById('btnNovaVenda').addEventListener('click', function(){
    showAlert('vd-modal-alert','',null);
    document.getElementById('vd-fiado').checked = false;
    document.getElementById('vd-forma').disabled = false;
    document.getElementById('vd-obs').value = '';
    document.getElementById('vd-pessoa').value = '';
    document.getElementById('vd-modal').style.display = '';
  });

  function fecharModal(){ document.getElementById('vd-modal').style.display = 'none'; }
  document.getElementById('btnVdFecharModal').addEventListener('click', fecharModal);
  document.getElementById('btnVdCancelar').addEventListener('click', fecharModal);
  document.getElementById('vd-modal').addEventListener('click', function(e){
    if(e.target === this) fecharModal();
  });

  /* Salvar venda */
  document.getElementById('btnVdSalvar').addEventListener('click', function(){
    var aluno_id = document.getElementById('vd-pessoa').value;
    var tipo     = document.getElementById('vd-tipo-revista').value;
    var tri      = document.getElementById('vd-modal-tri').value;
    var ano      = document.getElementById('vd-modal-ano').value;
    var forma    = document.getElementById('vd-forma').value;
    var fiado    = document.getElementById('vd-fiado').checked;
    var obs      = document.getElementById('vd-obs').value.trim();

    if(!aluno_id){ showAlert('vd-modal-alert','Selecione uma pessoa.','warning'); return; }

    var btn = this;
    btn.disabled = true;
    showAlert('vd-modal-alert','',null);

    fetch(API+'?recurso=venda', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({
        aluno_id: parseInt(aluno_id),
        tipo_revista: tipo,
        trimestre: parseInt(tri),
        ano: parseInt(ano),
        forma_pagamento: forma,
        fiado: fiado,
        observacao: obs
      })
    })
    .then(function(r){return r.json();})
    .then(function(data){
      btn.disabled = false;
      if(!data.ok){ showAlert('vd-modal-alert', data.msg, 'danger'); return; }
      fecharModal();
      showAlert('vd-alert', data.msg, 'success');
      carregar();
    })
    .catch(function(){
      btn.disabled = false;
      showAlert('vd-modal-alert','Erro de conexão.','danger');
    });
  });

  /* ── Modal: Quitar ── */
  function fecharQuitar(){ document.getElementById('vd-modal-quitar').style.display = 'none'; quitarVendaId = null; }
  document.getElementById('btnVdFecharQuitar').addEventListener('click', fecharQuitar);
  document.getElementById('btnVdCancelarQuitar').addEventListener('click', fecharQuitar);
  document.getElementById('vd-modal-quitar').addEventListener('click', function(e){
    if(e.target === this) fecharQuitar();
  });

  document.getElementById('btnVdConfirmarQuitar').addEventListener('click', function(){
    if(!quitarVendaId) return;
    var btn = this;
    var forma = document.getElementById('vd-quitar-forma').value;
    btn.disabled = true;

    fetch(API+'?recurso=quitar', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({ venda_id: quitarVendaId, forma_pagamento: forma })
    })
    .then(function(r){return r.json();})
    .then(function(data){
      btn.disabled = false;
      if(!data.ok){ alert(data.msg); return; }
      fecharQuitar();
      showAlert('vd-alert', data.msg, 'success');
      carregar();
    })
    .catch(function(){ btn.disabled = false; alert('Erro de conexão.'); });
  });

  /* ── Filtrar ── */
  document.getElementById('btnVdFiltrar').addEventListener('click', carregar);

  /* ── Init ── */
  carregar();

})();
