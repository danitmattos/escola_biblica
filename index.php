<?php
session_start();

// Protege a página — redireciona para login se não autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$usuario = htmlspecialchars($_SESSION['usuario'] ?? 'Administrador', ENT_QUOTES, 'UTF-8');
$usuario_email = htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8');
$pagina  = isset($_GET['pagina']) ? htmlspecialchars($_GET['pagina'], ENT_QUOTES, 'UTF-8') : 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Escola Bíblica — Sistema de Gestão</title>
  <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>" />
</head>
<body>

<!-- ╔══════════════════════════════════════════════╗ -->
<!-- ║  SIDEBAR                                     ║ -->
<!-- ╚══════════════════════════════════════════════╝ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">

  <!-- Logo -->
  <div class="sidebar__logo">
    <svg style="width:28px;height:28px;fill:white;flex-shrink:0" viewBox="0 0 24 24">
      <path d="M12 2C9.5 2 7.5 3 6 4.5 4.5 3 2.5 2 0 2v18c2.5 0 4.5 1 6 2.5C7.5 21 9.5 20 12 20c2.5 0 4.5 1 6 2.5C19.5 21 21.5 20 24 20V2c-2.5 0-4.5 1-6 2.5C16.5 3 14.5 2 12 2zm-1 15.5c-1.2-.8-2.7-1.3-5-1.5V5c2.3.2 3.8.7 5 1.5v11zm8 0c-2.3.2-3.8.7-5 1.5V6.5c1.2-.8 2.7-1.3 5-1.5v12.5z"/>
    </svg>
    Escola Bíblica
  </div>

  <!-- Navegação -->
  <nav class="sidebar__nav">

    <!-- Dashboard -->
    <div class="sidebar__section-title">Principal</div>
    <a href="index.php?pagina=dashboard" class="sidebar__link <?= $pagina === 'dashboard' ? 'active' : '' ?>">
      <svg class="icon" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
      Dashboard
    </a>

    <!-- ── ALUNOS ── -->
    <div class="sidebar__section-title">Acadêmico</div>

    <div class="sidebar__group <?= in_array($pagina, ['alunos','aluno-novo','turmas','turma-nova']) ? 'open' : '' ?>">
      <button class="sidebar__group-btn" data-group>
        <span class="btn-left">
          <svg class="icon" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
          Alunos
        </span>
        <svg class="chevron" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      </button>
      <div class="sidebar__submenu">
        <div class="sidebar__submenu-inner">
          <a href="index.php?pagina=alunos" class="sidebar__submenu-link <?= $pagina === 'alunos' ? 'active' : '' ?>">Listar Alunos</a>
          <a href="index.php?pagina=aluno-novo" class="sidebar__submenu-link <?= $pagina === 'aluno-novo' ? 'active' : '' ?>">Cadastrar Aluno</a>
          <a href="index.php?pagina=turmas" class="sidebar__submenu-link <?= $pagina === 'turmas' ? 'active' : '' ?>">Turmas</a>
          <a href="index.php?pagina=turma-nova" class="sidebar__submenu-link <?= $pagina === 'turma-nova' ? 'active' : '' ?>">Nova Turma</a>
        </div>
      </div>
    </div>

    <!-- ── PROFESSORES ── -->
    <div class="sidebar__group <?= in_array($pagina, ['professores','professor-novo']) ? 'open' : '' ?>">
      <button class="sidebar__group-btn" data-group>
        <span class="btn-left">
          <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
          Professores
        </span>
        <svg class="chevron" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      </button>
      <div class="sidebar__submenu">
        <div class="sidebar__submenu-inner">
          <a href="index.php?pagina=professores" class="sidebar__submenu-link <?= $pagina === 'professores' ? 'active' : '' ?>">Listar Professores</a>
          <a href="index.php?pagina=professor-novo" class="sidebar__submenu-link <?= $pagina === 'professor-novo' ? 'active' : '' ?>">Cadastrar Professor</a>
        </div>
      </div>
    </div>

    <!-- ── AULAS ── -->
    <div class="sidebar__group <?= in_array($pagina, ['aulas','aula-nova','frequencia','calendario']) ? 'open' : '' ?>">
      <button class="sidebar__group-btn" data-group>
        <span class="btn-left">
          <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
          Aulas
        </span>
        <svg class="chevron" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      </button>
      <div class="sidebar__submenu">
        <div class="sidebar__submenu-inner">
          <a href="index.php?pagina=calendario" class="sidebar__submenu-link <?= $pagina === 'calendario' ? 'active' : '' ?>">Calendário</a>
          <a href="index.php?pagina=aulas" class="sidebar__submenu-link <?= $pagina === 'aulas' ? 'active' : '' ?>">Listar Aulas</a>
          <a href="index.php?pagina=aula-nova" class="sidebar__submenu-link <?= $pagina === 'aula-nova' ? 'active' : '' ?>">Registrar Aula</a>
          <a href="index.php?pagina=frequencia" class="sidebar__submenu-link <?= $pagina === 'frequencia' ? 'active' : '' ?>">Frequência</a>
        </div>
      </div>
    </div>

    <!-- ── RELATÓRIOS ── -->
    <div class="sidebar__section-title">Análise</div>

    <div class="sidebar__group <?= in_array($pagina, ['rel-geral','rel-turma','rel-aluno']) ? 'open' : '' ?>">
      <button class="sidebar__group-btn" data-group>
        <span class="btn-left">
          <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"/></svg>
          Relatórios
        </span>
        <svg class="chevron" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      </button>
      <div class="sidebar__submenu">
        <div class="sidebar__submenu-inner">
          <a href="index.php?pagina=rel-geral" class="sidebar__submenu-link <?= $pagina === 'rel-geral' ? 'active' : '' ?>">Frequência Geral</a>
          <a href="index.php?pagina=rel-turma" class="sidebar__submenu-link <?= $pagina === 'rel-turma' ? 'active' : '' ?>">Por Turma</a>
          <a href="index.php?pagina=rel-aluno" class="sidebar__submenu-link <?= $pagina === 'rel-aluno' ? 'active' : '' ?>">Por Aluno</a>
        </div>
      </div>
    </div>

    <!-- ── CONFIGURAÇÕES ── -->
    <div class="sidebar__section-title">Sistema</div>
    <a href="index.php?pagina=configuracoes" class="sidebar__link <?= $pagina === 'configuracoes' ? 'active' : '' ?>">
      <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
      Configurações
    </a>

  </nav>

  <!-- Rodapé com usuário logado -->
  <div class="sidebar__footer">
    <div class="avatar"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
    <div>
      <div class="avatar-name"><?= $usuario ?></div>
      <div class="avatar-role">Administrador</div>
    </div>
    <a href="logout.php" class="sidebar__logout" title="Sair">
      <svg style="width:18px;height:18px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h6a1 1 0 100-2H4V5h5a1 1 0 000-2H3zm10.293 3.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L14.586 11H8a1 1 0 110-2h6.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
    </a>
  </div>

</aside>


<!-- ╔══════════════════════════════════════════════╗ -->
<!-- ║  HEADER                                      ║ -->
<!-- ╚══════════════════════════════════════════════╝ -->
<header class="header" id="mainHeader">
  <div class="flex items-center gap-4">
    <button class="btn-hamburger" id="hamburgerBtn" aria-label="Menu">
      <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
    </button>
    <span class="header__title" id="pageTitle">Dashboard</span>
  </div>
  <div class="header__actions">
    <!-- Notificações -->
    <button class="btn btn-ghost btn-sm" style="position:relative" title="Notificações">
      <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
      <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;background:var(--color-danger);border-radius:50%;"></span>
    </button>
    <!-- Avatar header -->
    <div class="avatar" style="width:32px;height:32px;font-size:var(--text-xs)"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
  </div>
</header>


<!-- ╔══════════════════════════════════════════════╗ -->
<!-- ║  CONTEÚDO PRINCIPAL                          ║ -->
<!-- ╚══════════════════════════════════════════════╝ -->
<div class="main-content">
  <div class="page">

    <?php if ($pagina === 'dashboard'): ?>
    <!-- ══════════════════════════════════════════════ -->
    <!--  DASHBOARD                                     -->
    <!-- ══════════════════════════════════════════════ -->

    <div class="page-header">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Bem-vindo, <?= $usuario ?>! Aqui está um resumo do sistema.</p>
      </div>
      <button class="btn btn-primary">
        <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
        Nova Aula
      </button>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card__icon icon-bg-blue">
          <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
        </div>
        <div>
          <div class="stat-card__value">142</div>
          <div class="stat-card__label">Total de Alunos</div>
          <span class="trend trend-up">
            <svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            +8 este mês
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card__icon icon-bg-purple">
          <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        </div>
        <div>
          <div class="stat-card__value">12</div>
          <div class="stat-card__label">Professores</div>
          <span class="trend trend-up">
            <svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            +2 este mês
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card__icon icon-bg-green">
          <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
        </div>
        <div>
          <div class="stat-card__value">8</div>
          <div class="stat-card__label">Turmas Ativas</div>
          <span class="trend trend-up">
            <svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            +1 este mês
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card__icon icon-bg-orange">
          <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
        </div>
        <div>
          <div class="stat-card__value">34</div>
          <div class="stat-card__label">Aulas este Mês</div>
          <span class="trend trend-down">
            <svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            -3 vs mês anterior
          </span>
        </div>
      </div>
    </div>

    <!-- Tabela + Painel lateral -->
    <div class="two-col">

      <!-- Últimas matrículas -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Últimas Matrículas</span>
          <a href="index.php?pagina=alunos" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none;">
          <table class="table">
            <thead>
              <tr>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Data</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Maria Silva</strong><br><small class="text-muted">maria@email.com</small></td>
                <td>Fundamentos da Fé</td>
                <td>14/03/2026</td>
                <td><span class="badge badge-success">Ativo</span></td>
              </tr>
              <tr>
                <td><strong>João Pereira</strong><br><small class="text-muted">joao@email.com</small></td>
                <td>Antigo Testamento I</td>
                <td>13/03/2026</td>
                <td><span class="badge badge-success">Ativo</span></td>
              </tr>
              <tr>
                <td><strong>Ana Costa</strong><br><small class="text-muted">ana@email.com</small></td>
                <td>Novo Testamento II</td>
                <td>12/03/2026</td>
                <td><span class="badge badge-warning">Pendente</span></td>
              </tr>
              <tr>
                <td><strong>Carlos Santos</strong><br><small class="text-muted">carlos@email.com</small></td>
                <td>Evangelismo Prático</td>
                <td>10/03/2026</td>
                <td><span class="badge badge-success">Ativo</span></td>
              </tr>
              <tr>
                <td><strong>Fernanda Lima</strong><br><small class="text-muted">fe@email.com</small></td>
                <td>Teologia Sistemática</td>
                <td>08/03/2026</td>
                <td><span class="badge badge-danger">Inativo</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Coluna direita -->
      <div style="display:flex;flex-direction:column;gap:var(--space-6);">

        <!-- Aulas de Hoje -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Aulas de Hoje</span>
            <span class="badge badge-primary">16/03</span>
          </div>
          <div class="card-body" style="padding-top:var(--space-2)">

            <div class="aula-item">
              <div class="aula-hora"><strong>08:00</strong><span>1h30</span></div>
              <div class="aula-info">
                <h4>Fundamentos da Fé</h4>
                <span>Prof. Samuel Oliveira · Sala 01</span>
              </div>
            </div>

            <div class="aula-item">
              <div class="aula-hora"><strong>10:00</strong><span>2h00</span></div>
              <div class="aula-info">
                <h4>Antigo Testamento I</h4>
                <span>Prof. Débora Alves · Sala 02</span>
              </div>
            </div>

            <div class="aula-item">
              <div class="aula-hora"><strong>14:00</strong><span>1h30</span></div>
              <div class="aula-info">
                <h4>Evangelismo Prático</h4>
                <span>Prof. Marcos Reis · Auditório</span>
              </div>
            </div>

          </div>
        </div>

        <!-- Frequência por Turma -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Frequência por Turma</span>
          </div>
          <div class="card-body">
            <?php
            $turmas = [
              ['nome' => 'Fund. da Fé',       'pct' => 92],
              ['nome' => 'A.T. I',             'pct' => 85],
              ['nome' => 'N.T. II',            'pct' => 78],
              ['nome' => 'Evangelismo',        'pct' => 95],
              ['nome' => 'Teol. Sistemática',  'pct' => 70],
            ];
            foreach ($turmas as $t): ?>
            <div style="margin-bottom:var(--space-4)">
              <div class="flex justify-between" style="font-size:var(--text-sm)">
                <span><?= $t['nome'] ?></span>
                <strong><?= $t['pct'] ?>%</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-bar__fill" style="width:<?= $t['pct'] ?>%;<?= $t['pct'] < 75 ? 'background-color:var(--color-warning)' : '' ?>"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div><!-- /col direita -->
    </div><!-- /two-col -->

    <?php else: ?>
    <!-- ══════════════════════════════════════════════ -->
    <!--  PÁGINAS GENÉRICAS (em construção)             -->
    <!-- ══════════════════════════════════════════════ -->
    <?php
    $titulos = [
      'alunos'         => 'Listar Alunos',
      'aluno-novo'     => 'Cadastrar Aluno',
      'turmas'         => 'Turmas',
      'turma-nova'     => 'Nova Turma',
      'professores'    => 'Listar Professores',
      'professor-novo' => 'Cadastrar Professor',
      'aulas'          => 'Listar Aulas',
      'aula-nova'      => 'Registrar Aula',
      'frequencia'     => 'Frequência',
      'calendario'     => 'Calendário',
      'rel-geral'      => 'Relatório — Frequência Geral',
      'rel-turma'      => 'Relatório por Turma',
      'rel-aluno'      => 'Relatório por Aluno',
      'configuracoes'  => 'Configurações',
    ];
    $titulo = $titulos[$pagina] ?? 'Página';
    ?>
    <div class="page-header">
      <div>
        <h1 class="page-title"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-subtitle">Esta seção está em desenvolvimento.</p>
      </div>
    </div>
    <div class="card">
      <div class="card-body text-center" style="padding:var(--space-16)">
        <svg style="width:56px;height:56px;fill:var(--color-gray-300);margin:0 auto var(--space-4)" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <h3 style="color:var(--color-gray-400);font-weight:500">Em construção</h3>
        <p class="text-muted" style="margin-top:var(--space-2)">O módulo <strong><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></strong> será implementado em breve.</p>
        <a href="index.php" class="btn btn-secondary" style="margin-top:var(--space-6)">← Voltar ao Dashboard</a>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /page -->
</div><!-- /main-content -->


<script>
  // ── Sidebar accordion ──────────────────────────────
  document.querySelectorAll('[data-group]').forEach(btn => {
    btn.addEventListener('click', () => {
      const group = btn.closest('.sidebar__group');
      const isOpen = group.classList.contains('open');

      // Fecha todos
      document.querySelectorAll('.sidebar__group').forEach(g => g.classList.remove('open'));

      // Abre o clicado (toggle)
      if (!isOpen) group.classList.add('open');
    });
  });

  // ── Título do header dinâmico ──────────────────────
  const titulos = {
    dashboard:       'Dashboard',
    alunos:          'Listar Alunos',
    'aluno-novo':    'Cadastrar Aluno',
    turmas:          'Turmas',
    'turma-nova':    'Nova Turma',
    professores:     'Listar Professores',
    'professor-novo':'Cadastrar Professor',
    aulas:           'Listar Aulas',
    'aula-nova':     'Registrar Aula',
    frequencia:      'Frequência',
    calendario:      'Calendário',
    'rel-geral':     'Frequência Geral',
    'rel-turma':     'Relatório por Turma',
    'rel-aluno':     'Relatório por Aluno',
    configuracoes:   'Configurações',
  };

  const params  = new URLSearchParams(window.location.search);
  const current = params.get('pagina') || 'dashboard';
  const titleEl = document.getElementById('pageTitle');
  if (titleEl && titulos[current]) titleEl.textContent = titulos[current];

  // ── Mobile: hambúrguer ─────────────────────────────
  const sidebar         = document.getElementById('sidebar');
  const overlay         = document.getElementById('sidebarOverlay');
  const hamburgerBtn    = document.getElementById('hamburgerBtn');

  function openSidebar()  { sidebar.classList.add('is-open');  overlay.classList.add('visible');  }
  function closeSidebar() { sidebar.classList.remove('is-open'); overlay.classList.remove('visible'); }

  hamburgerBtn.addEventListener('click', () => {
    sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
  });
  overlay.addEventListener('click', closeSidebar);
</script>

</body>
</html>
