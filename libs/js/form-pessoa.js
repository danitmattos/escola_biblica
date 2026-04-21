// ══════════════════════════════════════════════════
//  FORMULÁRIO ALUNO / PROFESSOR (criar / editar)
// ══════════════════════════════════════════════════
(function() {
  const form = document.getElementById('formAluno');
  if (!form) return;

  const modo = form.dataset.modo || 'criar';
  const alunoId = parseInt(form.dataset.id || '0', 10);

  // ── Pré-preenche data de matrícula com hoje (só no criar) ──
  const dtMatricula = document.getElementById('data_matricula');
  if (modo === 'criar' && dtMatricula) dtMatricula.value = new Date().toISOString().split('T')[0];

  // ── Carrega dados se for editar ──────────────────
  if (modo === 'editar' && alunoId) {
    fetch('alunos_crud.php?id=' + alunoId)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          showAlert(data.msg || 'Aluno não encontrado.', 'danger');
          return;
        }
        const a = data.aluno;
        setValue('nome', a.nome);
        setValue('sexo', a.sexo);
        setValue('estado_civil', a.estado_civil);
        setValue('data_nascimento', a.data_nascimento);
        // setValue('profissao', a.profissao); // campo removido
        const cpfFmt = a.cpf ? fmtCpf(String(a.cpf).padStart(11, '0')) : '';
        setValue('cpf', cpfFmt);
        const telFmt = a.telefone ? fmtTelForm(String(a.telefone)) : '';
        setValue('telefone', telFmt);
        setValue('email', a.usuario_email);
        const cepFmt = a.cep ? fmtCepForm(String(a.cep).padStart(8, '0')) : '';
        setValue('cep', cepFmt);
        setValue('logradouro', a.logradouro);
        setValue('numero', a.numero_endereco);
        setValue('complemento', a.complemento_endereco);
        setValue('bairro', a.bairro);
        setValue('cidade', a.cidade);
        setValue('estado', a.UF);
        setValue('turma', a.turma);
        setValue('data_matricula', a.data_matricula);
        setValue('status', a.status);
        setValue('docente', a.docente || 'N');
        setValue('observacoes', a.observacoes);
        const obsEl = document.getElementById('obs-count');
        if (obsEl) obsEl.textContent = (a.observacoes || '').length;
        if (a.foto) mostrarFoto(a.foto);
      })
      .catch(() => showAlert('Erro ao carregar dados do aluno.', 'danger'));
  }

  function setValue(id, val) {
    const el = document.getElementById(id);
    if (!el || val === null || val === undefined) return;
    el.value = val;
  }

  function fmtCpf(s) {
    s = s.replace(/\D/g, '').slice(0, 11);
    if (s.length === 11) return s.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    return s;
  }

  function fmtTelForm(s) {
    s = s.replace(/\D/g, '');
    if (s.length === 11) return '(' + s.slice(0, 2) + ') ' + s.slice(2, 7) + '-' + s.slice(7);
    if (s.length === 10) return '(' + s.slice(0, 2) + ') ' + s.slice(2, 6) + '-' + s.slice(6);
    return s;
  }

  function fmtCepForm(s) {
    s = s.replace(/\D/g, '').slice(0, 8);
    if (s.length === 8) return s.slice(0, 5) + '-' + s.slice(5);
    return s;
  }

  // ── Máscaras ─────────────────────────────────────
  const cpfEl = document.getElementById('cpf');
  if (cpfEl) cpfEl.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    else if (v.length > 3) v = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    this.value = v;
  });

  function maskPhone(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function() {
      let v = this.value.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10) v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
      else if (v.length > 6) v = v.replace(/(\d{2})(\d{4,5})(\d{0,4})/, '($1) $2-$3');
      else if (v.length > 2) v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
      this.value = v;
    });
  }
  maskPhone('telefone');
  maskPhone('resp_telefone');

  const cepInput = document.getElementById('cep');
  const cepSpinner = document.getElementById('cep-spinner');
  const cepError = document.getElementById('cep-error');
  if (cepInput) {
    cepInput.addEventListener('input', function() {
      let v = this.value.replace(/\D/g, '').slice(0, 8);
      if (v.length > 5) v = v.replace(/(\d{5})(\d{0,3})/, '$1-$2');
      this.value = v;
      if (v.replace('-', '').length === 8) buscarCep(v.replace('-', ''));
    });
  }

  function buscarCep(cep) {
    if (cepSpinner) cepSpinner.style.display = 'inline';
    if (cepError) cepError.textContent = '';
    fetch('https://viacep.com.br/ws/' + encodeURIComponent(cep) + '/json/')
      .then(r => {
        if (!r.ok) throw new Error();
        return r.json();
      })
      .then(data => {
        if (data.erro) {
          if (cepError) cepError.textContent = 'CEP não encontrado.';
          return;
        }
        setValue('logradouro', data.logradouro || '');
        setValue('bairro', data.bairro || '');
        setValue('cidade', data.localidade || '');
        setValue('estado', data.uf || '');
        const numEl = document.getElementById('numero');
        if (numEl) numEl.focus();
      })
      .catch(() => {
        if (cepError) cepError.textContent = 'Não foi possível consultar o CEP.';
      })
      .finally(() => {
        if (cepSpinner) cepSpinner.style.display = 'none';
      });
  }

  // Contador observações
  const obsTextarea = document.getElementById('observacoes');
  const obsCount = document.getElementById('obs-count');
  if (obsTextarea && obsCount) {
    obsTextarea.addEventListener('input', function() {
      obsCount.textContent = this.value.length;
    });
  }

  // ── Validação em tempo real ───────────────────────
  vfBind('nome',           'nome-error',      vfNomeCompleto,          {onInput:true});
  vfBind('sexo',           'sexo-error',      vfSelect('o sexo'));
  vfBind('cpf',            'cpf-error',       vfCpf,                   {onInput:true});
  vfBind('telefone',       'telefone-error',  vfTelefone,              {onInput:true});
  vfBind('email',          'email-error',     vfEmail,                 {onInput:true});
  vfBind('turma',          'turma-error',     vfSelect('uma turma'));
  vfBind('data_matricula', 'matricula-error', vfRequired('Data de matrícula'));

  // ── Helpers alert/error ───────────────────────────
  function showAlert(msg, tipo) {
    const el = document.getElementById('aluno-alert');
    if (!el) return;
    el.innerHTML = '<div class="alert alert-' + tipo + '"><svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg><span>' + msg + '</span></div>';
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function clearErrors() {
    form.querySelectorAll('.form-error').forEach(e => { e.textContent = ''; e.classList.remove('form-success'); });
    form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
    form.querySelectorAll('.is-valid').forEach(e => e.classList.remove('is-valid'));
  }

  function setError(inputId, errorId, msg) {
    const input = document.getElementById(inputId);
    const err = document.getElementById(errorId);
    if (input) input.classList.add('is-invalid');
    if (err) err.textContent = msg;
  }

  // ── Validação client-side ─────────────────────────
  function validar() {
    let ok = true;

    const nome = (document.getElementById('nome')?.value || '').trim();
    if (!nome) {
      setError('nome', 'nome-error', 'O nome é obrigatório.');
      ok = false;
    } else if (nome.trim().split(/\s+/).length < 2) {
      setError('nome', 'nome-error', 'Informe o nome completo.');
      ok = false;
    }

    const sexo = document.getElementById('sexo')?.value;
    if (!sexo) {
      setError('sexo', 'sexo-error', 'Selecione o sexo.');
      ok = false;
    }

    const cpf = (document.getElementById('cpf')?.value || '').replace(/\D/g, '');
    if (cpf && cpf.length !== 11) {
      setError('cpf', 'cpf-error', 'CPF inválido.');
      ok = false;
    }

    const tel = (document.getElementById('telefone')?.value || '').replace(/\D/g, '');
    if (!tel) {
      setError('telefone', 'telefone-error', 'O telefone é obrigatório.');
      ok = false;
    } else if (tel.length < 10) {
      setError('telefone', 'telefone-error', 'Telefone incompleto.');
      ok = false;
    }

    const email = (document.getElementById('email')?.value || '').trim();
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setError('email', 'email-error', 'E-mail inválido.');
      ok = false;
    }

    const turma = document.getElementById('turma')?.value;
    if (!turma) {
      setError('turma', 'turma-error', 'Selecione uma turma.');
      ok = false;
    }

    const dtMatr = document.getElementById('data_matricula')?.value;
    if (!dtMatr) {
      setError('data_matricula', 'matricula-error', 'A data de matrícula é obrigatória.');
      ok = false;
    }

    return ok;
  }

  // ── Submit ────────────────────────────────────────
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    clearErrors();
    document.getElementById('aluno-alert').style.display = 'none';

    if (!validar()) {
      showAlert('Corrija os erros destacados antes de salvar.', 'danger');
      return;
    }

    const fd = new FormData();
    fd.append('id',            alunoId || '');
    fd.append('nome',          document.getElementById('nome')?.value.trim() || '');
    fd.append('sexo',          document.getElementById('sexo')?.value || '');
    fd.append('cpf',           document.getElementById('cpf')?.value || '');
    fd.append('estado_civil',  document.getElementById('estado_civil')?.value || '');
    fd.append('data_nascimento', document.getElementById('data_nascimento')?.value || '');
    // fd.append('profissao',     document.getElementById('profissao')?.value.trim() || ''); // campo removido
    fd.append('telefone',      document.getElementById('telefone')?.value || '');
    fd.append('email',         document.getElementById('email')?.value.trim() || '');
    fd.append('cep',           document.getElementById('cep')?.value || '');
    fd.append('logradouro',    document.getElementById('logradouro')?.value.trim() || '');
    fd.append('numero',        document.getElementById('numero')?.value || '');
    fd.append('complemento',   document.getElementById('complemento')?.value.trim() || '');
    fd.append('bairro',        document.getElementById('bairro')?.value.trim() || '');
    fd.append('cidade',        document.getElementById('cidade')?.value.trim() || '');
    fd.append('estado',        document.getElementById('estado')?.value || '');
    fd.append('data_matricula',document.getElementById('data_matricula')?.value || '');
    fd.append('turma',         document.getElementById('turma')?.value || '');
    fd.append('observacoes',   document.getElementById('observacoes')?.value || '');
    fd.append('status',        document.getElementById('status')?.value || 'ativo');
    fd.append('docente',       document.getElementById('docente')?.value || 'N');
    const fotoFile = document.getElementById('fotoInput')?.files[0];
    if (fotoFile) fd.append('foto', fotoFile);
    fd.append('foto_remover', document.getElementById('fotoRemover')?.value || '0');

    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    btn.innerHTML = '<svg class="icon" style="animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg> Salvando…';

    const url = modo === 'editar' ? 'alunos_crud.php?_method=PUT' : 'alunos_crud.php';
    fetch(url, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          if (data.erros) {
            Object.entries(data.erros).forEach(([campo, msg]) => {
              const mapId = { nome:'nome', sexo:'sexo', cpf:'cpf', telefone:'telefone', email:'email', turma:'turma', data_matricula:'data_matricula' };
              const mapErr = { nome:'nome-error', sexo:'sexo-error', cpf:'cpf-error', telefone:'telefone-error', email:'email-error', turma:'turma-error', data_matricula:'matricula-error' };
              if (mapId[campo]) setError(mapId[campo], mapErr[campo], msg);
            });
          }
          showAlert(data.msg || 'Erro ao salvar. Verifique os campos.', 'danger');
        } else {
          showAlert(data.msg || 'Salvo com sucesso!', 'success');
          setTimeout(() => {
            window.location.href = 'index.php?pagina=' + (form.dataset.retorno || 'alunos');
          }, 1500);
        }
      })
      .catch(() => showAlert('Falha na comunicação com o servidor.', 'danger'))
      .finally(() => {
        btn.disabled = false;
        const label = modo === 'editar' ? 'Salvar Alterações' : 'Salvar Aluno';
        btn.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> ' + label;
      });
  });

  // Limpar (só no form de novo cadastro)
  const btnLimpar = document.getElementById('btnLimpar');
  if (btnLimpar) {
    btnLimpar.addEventListener('click', function() {
      setTimeout(() => {
        clearErrors();
        document.getElementById('aluno-alert').style.display = 'none';
        if (obsCount) obsCount.textContent = '0';
        window.removerFotoPreview && window.removerFotoPreview();
        if (dtMatricula) dtMatricula.value = new Date().toISOString().split('T')[0];
      }, 0);
    });
  }

  // ── Foto: preview, trocar e remover ────────────────
  const fotoInput   = document.getElementById('fotoInput');
  const fotoImg     = document.getElementById('fotoImg');
  const fotoIcon    = document.getElementById('fotoIcon');
  const fotoOverlay = document.getElementById('fotoOverlay');
  const btnRemover  = document.getElementById('btnRemoverFoto');
  const fotoRemoverFlag = document.getElementById('fotoRemover');

  function mostrarFoto(src) {
    if (fotoImg)  { fotoImg.src = src; fotoImg.style.display = 'block'; }
    if (fotoIcon) fotoIcon.style.display = 'none';
    if (btnRemover) btnRemover.style.display = '';
    if (fotoRemoverFlag) fotoRemoverFlag.value = '0';
  }

  window.removerFotoPreview = function() {
    if (fotoImg)  { fotoImg.style.display = 'none'; fotoImg.src = ''; }
    if (fotoIcon) fotoIcon.style.display = 'block';
    if (btnRemover) btnRemover.style.display = 'none';
    if (fotoInput) fotoInput.value = '';
    if (fotoRemoverFlag) fotoRemoverFlag.value = '1';
  };

  if (fotoInput) {
    fotoInput.addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024) {
        showAlert('A foto deve ter no máximo 2 MB.', 'danger');
        this.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = ev => mostrarFoto(ev.target.result);
      reader.readAsDataURL(file);
    });

    const fp = document.getElementById('fotoPreview');
    if (fp && fotoOverlay) {
      fp.addEventListener('mouseenter', () => {
        fp.style.borderColor = 'var(--color-primary)';
        if (fotoImg && fotoImg.style.display !== 'none') fotoOverlay.style.display = 'flex';
      });
      fp.addEventListener('mouseleave', () => {
        fp.style.borderColor = 'var(--color-border)';
        fotoOverlay.style.display = 'none';
      });
    }
  }
})();

// ══════════════════════════════════════════════════
//  FICHA DE CADASTRO DE ALUNO (PDF)
// ══════════════════════════════════════════════════
function abrirFichaCadastro() {
  const css = `
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#111; background:#f1f5f9; padding:20px; }
.pw { max-width:760px; margin:0 auto; background:#fff; padding:28px 32px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.1); }
.doc-head { display:flex; align-items:center; gap:16px; border-bottom:3px solid #1E40AF; padding-bottom:14px; margin-bottom:20px; }
.doc-logo { background:#1E40AF; color:#fff; font-size:20px; font-weight:700; padding:8px 14px; border-radius:7px; flex-shrink:0; }
.doc-title { font-size:17px; font-weight:700; color:#1E3A8A; }
.doc-sub { font-size:10px; color:#64748b; margin-top:3px; }
.doc-photo { margin-left:auto; width:80px; height:96px; border:1.5px solid #94a3b8; border-radius:5px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; color:#94a3b8; font-size:9px; flex-shrink:0; }
.doc-photo svg { width:28px; height:28px; fill:#cbd5e1; }
.sec { margin-bottom:18px; }
.sec-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#fff; background:#1E40AF; padding:4px 10px; border-radius:4px 4px 0 0; display:flex; align-items:center; gap:6px; }
.sec-body { border:1px solid #cbd5e1; border-top:none; border-radius:0 0 5px 5px; padding:12px 14px; }
.fg { display:grid; gap:10px 14px; }
.fg-2 { grid-template-columns:1fr 1fr; }
.fg-3 { grid-template-columns:1fr 1fr 1fr; }
.fg-4 { grid-template-columns:2fr 3fr 1fr 2fr; }
.fg-addr { grid-template-columns:1fr 1fr 80px; }
.col-full { grid-column:1/-1; }
.f { display:flex; flex-direction:column; gap:3px; }
.f label { font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.4px; }
.f .line { border:none; border-bottom:1.5px solid #94a3b8; height:22px; width:100%; }
.f .req { color:#dc2626; }
.checks { display:flex; flex-wrap:wrap; gap:8px 16px; margin-top:4px; }
.chk { display:flex; align-items:center; gap:5px; font-size:10px; cursor:default; color:#111; }
.chk-box { width:13px; height:13px; border:1.5px solid #94a3b8; border-radius:2px; flex-shrink:0; }
.sign-row { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-top:10px; }
.sign-field { display:flex; flex-direction:column; gap:3px; }
.sign-field label { font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.4px; }
.sign-field .line { border:none; border-bottom:1.5px solid #94a3b8; height:28px; width:100%; }
.doc-footer { margin-top:18px; padding-top:10px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:9px; color:#94a3b8; }
.pb { display:flex; gap:9px; margin-bottom:18px; }
.pb button { cursor:pointer; border:none; padding:8px 20px; border-radius:6px; font-size:12px; font-weight:600; }
.bp { background:#1E40AF; color:#fff; }
.bx { background:#f1f5f9; color:#475569; }
@media print {
  body { background:#fff; padding:0; }
  .pw { box-shadow:none; border-radius:0; padding:16px 20px; }
  .pb { display:none !important; }
  @page { size:A4; margin:12mm; }
}
`;

  const hoje = new Date().toLocaleDateString('pt-BR', {day:'2-digit',month:'long',year:'numeric'});

  const html = `<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Ficha de Cadastro de Aluno</title><style>${css}</style></head><body>
<div class="pw">
  <div class="pb">
    <button class="bp" onclick="window.print()">🖨️ Imprimir / Salvar como PDF</button>
    <button class="bx" onclick="window.close()">✕ Fechar</button>
  </div>
  <div class="doc-head">
    <div class="doc-logo">EBD</div>
    <div>
      <div class="doc-title">Escola Bíblica — Ficha de Cadastro de Aluno</div>
      <div class="doc-sub">Preencha todos os campos com letra de forma. Campos com <span style="color:#dc2626">*</span> são obrigatórios.</div>
    </div>
    <div class="doc-photo">
      <svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
      Foto 3x4
    </div>
  </div>
  <div class="sec">
    <div class="sec-title">👤 &nbsp;1. Dados Pessoais</div>
    <div class="sec-body">
      <div class="fg fg-2" style="margin-bottom:10px">
        <div class="f col-full"><label>Nome completo <span class="req">*</span></label><div class="line"></div></div>
      </div>
      <div class="fg fg-3" style="margin-bottom:10px">
        <div class="f"><label>Data de Nascimento <span class="req">*</span></label><div class="line"></div></div>
        <div class="f"><label>CPF</label><div class="line"></div></div>
        <!-- Profissão removida -->
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px 14px">
        <div class="f">
          <label>Sexo <span class="req">*</span></label>
          <div class="checks">
            <span class="chk"><span class="chk-box"></span> Masculino</span>
            <span class="chk"><span class="chk-box"></span> Feminino</span>
          </div>
        </div>
        <div class="f">
          <label>Estado Civil</label>
          <div class="checks">
            <span class="chk"><span class="chk-box"></span> Solteiro(a)</span>
            <span class="chk"><span class="chk-box"></span> Casado(a)</span>
            <span class="chk"><span class="chk-box"></span> Divorciado(a)</span>
            <span class="chk"><span class="chk-box"></span> Viúvo(a)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="sec">
    <div class="sec-title" style="background:#166534">📞 &nbsp;2. Contato</div>
    <div class="sec-body">
      <div class="fg fg-3">
        <div class="f"><label>Telefone / WhatsApp <span class="req">*</span></label><div class="line"></div></div>
        <div class="f"><label>Telefone alternativo</label><div class="line"></div></div>
        <div class="f"><label>E-mail</label><div class="line"></div></div>
      </div>
    </div>
  </div>
  <div class="sec">
    <div class="sec-title" style="background:#92400E">📍 &nbsp;3. Endereço</div>
    <div class="sec-body">
      <div class="fg" style="grid-template-columns:120px 1fr 70px 1fr;margin-bottom:10px">
        <div class="f"><label>CEP</label><div class="line"></div></div>
        <div class="f"><label>Logradouro (Rua / Av.)</label><div class="line"></div></div>
        <div class="f"><label>Nº</label><div class="line"></div></div>
        <div class="f"><label>Complemento</label><div class="line"></div></div>
      </div>
      <div class="fg fg-addr">
        <div class="f"><label>Bairro</label><div class="line"></div></div>
        <div class="f"><label>Cidade</label><div class="line"></div></div>
        <div class="f"><label>UF</label><div class="line"></div></div>
      </div>
    </div>
  </div>
  <div class="sec">
    <div class="sec-title" style="background:#6B21A8">👨‍👩‍👧 &nbsp;4. Responsável <span style="font-weight:400;opacity:.85;font-size:9px">(preencher somente se o aluno for menor de 18 anos)</span></div>
    <div class="sec-body">
      <div class="fg" style="grid-template-columns:2fr 1fr;margin-bottom:10px">
        <div class="f"><label>Nome do Responsável</label><div class="line"></div></div>
        <div class="f">
          <label>Parentesco</label>
          <div class="checks">
            <span class="chk"><span class="chk-box"></span> Pai</span>
            <span class="chk"><span class="chk-box"></span> Mãe</span>
            <span class="chk"><span class="chk-box"></span> Avô/Avó</span>
            <span class="chk"><span class="chk-box"></span> Tio(a)</span>
            <span class="chk"><span class="chk-box"></span> Outro</span>
          </div>
        </div>
      </div>
      <div class="fg fg-2">
        <div class="f"><label>Telefone do Responsável</label><div class="line"></div></div>
        <div class="f"><label>E-mail do Responsável</label><div class="line"></div></div>
      </div>
    </div>
  </div>
  <div class="sec">
    <div class="sec-title">🎓 &nbsp;5. Matrícula</div>
    <div class="sec-body">
      <div class="fg fg-3" style="margin-bottom:10px">
        <div class="f"><label>Turma <span class="req">*</span></label><div class="line"></div></div>
        <div class="f"><label>Data de Matrícula <span class="req">*</span></label><div class="line"></div></div>
        <div class="f">
          <label>É docente?</label>
          <div class="checks">
            <span class="chk"><span class="chk-box"></span> Sim</span>
            <span class="chk"><span class="chk-box"></span> Não</span>
          </div>
        </div>
      </div>
      <div class="f"><label>Observações / Necessidades Especiais</label><div class="line" style="height:36px;border-bottom-color:#94a3b8"></div></div>
    </div>
  </div>
  <div class="sign-row">
    <div class="sign-field"><label>Assinatura do Aluno / Responsável</label><div class="line"></div></div>
    <div class="sign-field"><label>Data de Preenchimento</label><div class="line"></div></div>
  </div>
  <div class="doc-footer">
    <span>Escola Bíblica — Sistema de Gestão</span>
    <span>Gerado em ${hoje}</span>
  </div>
</div>
</body></html>`;

  const w = window.open('', '_blank', 'width=900,height=750');
  w.document.write(html);
  w.document.close();
}
