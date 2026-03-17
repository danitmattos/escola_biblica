<?php
session_start();

// Protege a página — redireciona para login se não autenticado
if (!isset($_SESSION['usuario'])) {
  header('Location: login.php');
  exit();
}

$usuario = htmlspecialchars($_SESSION['usuario'] ?? 'Administrador', ENT_QUOTES, 'UTF-8');
$usuario_email = htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8');
$usuario_foto  = htmlspecialchars($_SESSION['usuario_foto']  ?? '', ENT_QUOTES, 'UTF-8');
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
        <path d="M12 2C9.5 2 7.5 3 6 4.5 4.5 3 2.5 2 0 2v18c2.5 0 4.5 1 6 2.5C7.5 21 9.5 20 12 20c2.5 0 4.5 1 6 2.5C19.5 21 21.5 20 24 20V2c-2.5 0-4.5 1-6 2.5C16.5 3 14.5 2 12 2zm-1 15.5c-1.2-.8-2.7-1.3-5-1.5V5c2.3.2 3.8.7 5 1.5v11zm8 0c-2.3.2-3.8.7-5 1.5V6.5c1.2-.8 2.7-1.3 5-1.5v12.5z" />
      </svg>
      Escola Bíblica
    </div>

    <!-- Navegação -->
    <nav class="sidebar__nav">

      <!-- Dashboard -->
      <div class="sidebar__section-title">Principal</div>
      <a href="index.php?pagina=dashboard" class="sidebar__link <?= $pagina === 'dashboard' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>
        Dashboard
      </a>

      <!-- ── ALUNOS ── -->
      <div class="sidebar__section-title">Acadêmico</div>

      <div class="sidebar__group <?= in_array($pagina, ['alunos', 'aluno-novo', 'aluno-editar', 'turmas', 'turma-nova', 'turma-editar']) ? 'open' : '' ?>">
        <button class="sidebar__group-btn" data-group>
          <span class="btn-left">
            <svg class="icon" viewBox="0 0 20 20">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
            </svg>
            Alunos
          </span>
          <svg class="chevron" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
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
      <div class="sidebar__group <?= in_array($pagina, ['professores', 'professor-novo', 'professor-editar']) ? 'open' : '' ?>">
        <button class="sidebar__group-btn" data-group>
          <span class="btn-left">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            Professores
          </span>
          <svg class="chevron" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
        <div class="sidebar__submenu">
          <div class="sidebar__submenu-inner">
            <a href="index.php?pagina=professores" class="sidebar__submenu-link <?= $pagina === 'professores' ? 'active' : '' ?>">Listar Professores</a>
            <a href="index.php?pagina=professor-novo" class="sidebar__submenu-link <?= $pagina === 'professor-novo' ? 'active' : '' ?>">Cadastrar Professor</a>
            <a href="index.php?pagina=professor-editar" class="sidebar__submenu-link <?= $pagina === 'professor-editar' ? 'active' : '' ?>" style="display:none">Editar Professor</a>
          </div>
        </div>
      </div>

      <!-- ── AULAS ── -->
      <div class="sidebar__group <?= in_array($pagina, ['aulas', 'aula-nova', 'frequencia', 'calendario']) ? 'open' : '' ?>">
        <button class="sidebar__group-btn" data-group>
          <span class="btn-left">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            Aulas
          </span>
          <svg class="chevron" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
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

      <div class="sidebar__group <?= in_array($pagina, ['rel-geral', 'rel-turma', 'rel-aluno']) ? 'open' : '' ?>">
        <button class="sidebar__group-btn" data-group>
          <span class="btn-left">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
            </svg>
            Relatórios
          </span>
          <svg class="chevron" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
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
        <svg class="icon" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
        </svg>
        Configurações
      </a>

    </nav>

    <!-- Rodapé com usuário logado -->
    <div class="sidebar__footer">
      <?php if ($usuario_foto): ?>
        <img src="<?= $usuario_foto ?>" alt="" style="width:36px;height:36px;border-radius:var(--radius-full);object-fit:cover;flex-shrink:0">
      <?php else: ?>
        <div class="avatar"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
      <?php endif; ?>
      <div>
        <div class="avatar-name"><?= $usuario ?></div>
        <div class="avatar-role">Administrador</div>
      </div>
      <a href="logout.php" class="sidebar__logout" title="Sair">
        <svg style="width:18px;height:18px;fill:currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h6a1 1 0 100-2H4V5h5a1 1 0 000-2H3zm10.293 3.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L14.586 11H8a1 1 0 110-2h6.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>

  </aside>


  <!-- ╔══════════════════════════════════════════════╗ -->
  <!-- ║  HEADER                                      ║ -->
  <!-- ╚══════════════════════════════════════════════╝ -->
  <header class="header" id="mainHeader">
    <div class="flex items-center gap-4">
      <button class="btn-hamburger" id="hamburgerBtn" aria-label="Menu">
        <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
      </button>
      <span class="header__title" id="pageTitle">Dashboard</span>
    </div>
    <div class="header__actions">
      <!-- Notificações -->
      <button class="btn btn-ghost btn-sm" style="position:relative" title="Notificações">
        <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 20 20">
          <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
        </svg>
        <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;background:var(--color-danger);border-radius:50%;"></span>
      </button>
      <!-- Avatar header -->
      <?php if ($usuario_foto): ?>
        <img src="<?= $usuario_foto ?>" alt="" style="width:32px;height:32px;border-radius:var(--radius-full);object-fit:cover;flex-shrink:0">
      <?php else: ?>
        <div class="avatar" style="width:32px;height:32px;font-size:var(--text-xs)"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
      <?php endif; ?>
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
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Aula
          </button>
        </div>

        <!-- Stat Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-card__icon icon-bg-blue">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
              </svg>
            </div>
            <div>
              <div class="stat-card__value" id="dash-val-alunos">—</div>
              <div class="stat-card__label">Total de Alunos</div>
              <span class="trend trend-up" id="dash-trend-alunos">
                <svg viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                …
              </span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card__icon icon-bg-purple">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <div class="stat-card__value" id="dash-val-prof">—</div>
              <div class="stat-card__label">Professores</div>
              <span class="trend trend-up" id="dash-trend-prof">
                <svg viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                …
              </span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card__icon icon-bg-green">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
              </svg>
            </div>
            <div>
              <div class="stat-card__value" id="dash-val-turmas">—</div>
              <div class="stat-card__label">Turmas Ativas</div>
              <span class="trend trend-up" id="dash-trend-turmas">
                <svg viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                …
              </span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card__icon icon-bg-orange">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <div class="stat-card__value">34</div>
              <div class="stat-card__label">Aulas este Mês</div>
              <span class="trend trend-down">
                <svg viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                -3 vs mês anterior
              </span>
            </div>
          </div>
        </div>

        <!-- Tabela + Painel lateral -->
        <div class="two-col">

          <!-- Coluna esquerda -->
          <div style="display:flex;flex-direction:column;gap:var(--space-6);">

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
                <tbody id="tbody-ultimas-matriculas">
                  <tr>
                    <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Carregando…</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Aniversariantes do Mês -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">
                <svg style="width:16px;height:16px;fill:currentColor;vertical-align:middle;margin-right:6px;color:var(--color-pink,#ec4899)" viewBox="0 0 20 20">
                  <path d="M10 2a1 1 0 011 1v1.07A7.002 7.002 0 0117 11v5a1 1 0 01-1 1H4a1 1 0 01-1-1v-5a7.002 7.002 0 016-6.93V3a1 1 0 011-1zM7 8a1 1 0 00-1 1v1H5v1h1v4h8v-4h1v-1h-1V9a1 1 0 00-1-1H7z"/>
                </svg>
                Aniversariantes do Mês
              </span>
              <span class="badge badge-primary" id="dash-aniv-mes"><?php echo date('m/Y'); ?></span>
            </div>
            <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none;">
              <table class="table">
                <thead>
                  <tr>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Data de Nasc.</th>
                  </tr>
                </thead>
                <tbody id="tbody-aniversariantes">
                  <tr>
                    <td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Carregando…</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          </div><!-- /col esquerda -->

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

      <?php elseif ($pagina === 'alunos'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  LISTAGEM DE ALUNOS                            -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Alunos</h1>
            <p class="page-subtitle">Gerencie os alunos cadastrados no sistema.</p>
          </div>
          <a href="index.php?pagina=aluno-novo" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Novo Aluno
          </a>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="padding:var(--space-4) var(--space-6)">
            <div style="display:grid;grid-template-columns:1fr 200px 200px auto;gap:var(--space-3);align-items:flex-end">
              <div>
                <label class="form-label" for="filtro-busca">Buscar</label>
                <input type="text" id="filtro-busca" class="form-control" placeholder="Nome, CPF ou e-mail…">
              </div>
              <div>
                <label class="form-label" for="filtro-status">Status</label>
                <select id="filtro-status" class="form-control">
                  <option value="">Todos</option>
                  <option value="ativo">Ativo</option>
                  <option value="pendente">Pendente</option>
                  <option value="inativo">Inativo</option>
                </select>
              </div>
              <div>
                <label class="form-label" for="filtro-turma">Turma</label>
              <select id="filtro-turma" class="form-control">
                  <option value="">Todas</option>
                </select>
              </div>
              <button class="btn btn-secondary" id="btnFiltrar">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                Filtrar
              </button>
            </div>
          </div>
        </div>

        <!-- Alerta de operação -->
        <div id="lista-alert" style="display:none;margin-bottom:var(--space-4)"></div>

        <!-- Tabela -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Lista de Alunos</span>
            <span id="total-alunos" class="badge badge-primary" style="font-size:var(--text-sm)">—</span>
          </div>
          <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
            <table class="table" id="tabela-alunos">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Foto</th>
                  <th>Nome</th>
                  <th>Turma</th>
                  <th>Telefone</th>
                  <th>Matrícula</th>
                  <th>Status</th>
                  <th style="text-align:right">Ações</th>
                </tr>
              </thead>
              <tbody id="tbody-alunos">
                <tr>
                  <td colspan="8" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">
                    <svg style="width:32px;height:32px;fill:currentColor;display:block;margin:0 auto var(--space-2)" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Carregando…
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <div class="modal-overlay" id="modalExcluir" style="display:none">
          <div class="modal" style="max-width:420px">
            <div class="modal-header">
              <span class="modal-title">Confirmar exclusão</span>
              <button class="modal-close" onclick="document.getElementById('modalExcluir').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
              <p style="margin:0">Tem certeza que deseja excluir o aluno <strong id="modal-nome-aluno"></strong>?<br>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
              </p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="document.getElementById('modalExcluir').style.display='none'">Cancelar</button>
              <button class="btn btn-danger" id="btnConfirmarExcluir">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Excluir
              </button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'aluno-novo'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CADASTRO DE ALUNO                             -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Cadastrar Aluno</h1>
            <p class="page-subtitle">Preencha os dados do novo aluno para realizar o cadastro.</p>
          </div>
          <a href="index.php?pagina=alunos" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Voltar
          </a>
        </div>

        <div id="aluno-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formAluno" novalidate data-modo="criar" data-id="0">

          <!-- ── Dados Pessoais ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </span>
                Dados Pessoais
              </span>
            </div>
            <div class="card-body">

              <!-- Foto + Nome -->
              <div style="display:flex;align-items:flex-start;gap:var(--space-6);margin-bottom:var(--space-6)">
                <!-- Upload de foto -->
                <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);flex-shrink:0">
                  <!-- Círculo clicável -->
                  <div id="fotoPreview" style="width:96px;height:96px;border-radius:50%;border:2px dashed var(--color-border);background:var(--color-gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:border-color var(--transition-fast);position:relative" onclick="document.getElementById('fotoInput').click()" title="Clique para trocar a foto">
                    <svg id="fotoIcon" style="width:36px;height:36px;fill:var(--color-gray-400)" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <img id="fotoImg" src="" alt="Foto do aluno" style="display:none;width:100%;height:100%;object-fit:cover">
                    <!-- Overlay de hover -->
                    <div id="fotoOverlay" style="display:none;position:absolute;inset:0;background:rgba(0,0,0,.45);border-radius:50%;align-items:center;justify-content:center">
                      <svg style="width:24px;height:24px;fill:#fff" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                  </div>
                  <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none">
                  <!-- Botões de ação -->
                  <div style="display:flex;gap:var(--space-2)">
                    <button type="button" id="btnTrocarFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px" onclick="document.getElementById('fotoInput').click()" title="Trocar foto">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                      Trocar
                    </button>
                    <button type="button" id="btnRemoverFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px;color:var(--color-danger);display:none" onclick="removerFotoPreview()" title="Remover foto">
                      <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      Remover
                    </button>
                  </div>
                  <input type="hidden" id="fotoRemover" name="foto_remover" value="0">
                  <span style="font-size:var(--text-xs);color:var(--color-text-muted);text-align:center">JPG, PNG ou WebP<br>máx. 2 MB</span>
                </div>

                <!-- Nome + CPF + Data Nasc. -->
                <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                  <div class="form-group" style="grid-column:1/-1;margin-bottom:0">
                    <label class="form-label" for="nome">Nome completo <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: Maria Aparecida da Silva" maxlength="120" required autocomplete="name">
                    <span class="form-error" id="nome-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" autocomplete="off" inputmode="numeric">
                    <span class="form-error" id="cpf-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="data_nascimento">Data de Nascimento <span style="color:var(--color-danger)">*</span></label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required>
                    <span class="form-error" id="nascimento-error"></span>
                  </div>
                </div>
              </div>

              <!-- Sexo / Estado Civil / Nacionalidade -->
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="sexo">Sexo <span style="color:var(--color-danger)">*</span></label>
                  <select id="sexo" name="sexo" class="form-control" required>
                    <option value="">Selecione…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                  </select>
                  <span class="form-error" id="sexo-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado_civil">Estado Civil</label>
                  <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">Selecione…</option>
                    <option value="solteiro">Solteiro(a)</option>
                    <option value="casado">Casado(a)</option>
                    <option value="divorciado">Divorciado(a)</option>
                    <option value="viuvo">Viúvo(a)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="profissao">Profissão</label>
                  <input type="text" id="profissao" name="profissao" class="form-control" placeholder="Ex.: Professor(a)" maxlength="80">
                </div>
              </div>

            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-success)" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                </span>
                Contato
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="telefone">Telefone <span style="color:var(--color-danger)">*</span></label>
                  <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" required autocomplete="tel">
                  <span class="form-error" id="telefone-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="email">E-mail</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="aluno@email.com" maxlength="120" autocomplete="email">
                  <span class="form-error" id="email-error"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Endereço ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-warning)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                </span>
                Endereço
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:180px 1fr 80px 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cep">CEP</label>
                  <div style="position:relative">
                    <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000" maxlength="9" inputmode="numeric" autocomplete="postal-code">
                    <span id="cep-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%)">
                      <svg style="width:16px;height:16px;fill:var(--color-gray-400);animation:spin 1s linear infinite" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                      </svg>
                    </span>
                  </div>
                  <span class="form-error" id="cep-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="logradouro">Logradouro</label>
                  <input type="text" id="logradouro" name="logradouro" class="form-control" placeholder="Rua, Avenida…" maxlength="150" autocomplete="street-address">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="numero">Nº</label>
                  <input type="text" id="numero" name="numero" class="form-control" placeholder="Nº" maxlength="10">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="complemento">Complemento</label>
                  <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto, Bloco…" maxlength="60">
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 100px;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Bairro" maxlength="80">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado">UF</label>
                  <select id="estado" name="estado" class="form-control" autocomplete="address-level1">
                    <option value="">UF</option>
                    <option>AC</option>
                    <option>AL</option>
                    <option>AP</option>
                    <option>AM</option>
                    <option>BA</option>
                    <option>CE</option>
                    <option>DF</option>
                    <option>ES</option>
                    <option>GO</option>
                    <option>MA</option>
                    <option>MT</option>
                    <option>MS</option>
                    <option>MG</option>
                    <option>PA</option>
                    <option>PB</option>
                    <option>PR</option>
                    <option>PE</option>
                    <option>PI</option>
                    <option>RJ</option>
                    <option>RN</option>
                    <option>RS</option>
                    <option>RO</option>
                    <option>RR</option>
                    <option>SC</option>
                    <option>SP</option>
                    <option>SE</option>
                    <option>TO</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Responsável (menor de idade) ── -->
          <div class="card" id="cardResponsavel" style="margin-bottom:var(--space-6);display:none">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-danger)" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                  </svg>
                </span>
                Responsável
              </span>
              <span class="badge badge-warning">Aluno menor de 18 anos</span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0;grid-column:1/3">
                  <label class="form-label" for="resp_nome">Nome do Responsável <span style="color:var(--color-danger)">*</span></label>
                  <input type="text" id="resp_nome" name="resp_nome" class="form-control" placeholder="Nome completo do responsável" maxlength="120">
                  <span class="form-error" id="resp-nome-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="resp_parentesco">Parentesco</label>
                  <select id="resp_parentesco" name="resp_parentesco" class="form-control">
                    <option value="">Selecione…</option>
                    <option value="pai">Pai</option>
                    <option value="mae">Mãe</option>
                    <option value="avo">Avô/Avó</option>
                    <option value="tio">Tio(a)</option>
                    <option value="outro">Outro</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="resp_telefone">Telefone do Responsável <span style="color:var(--color-danger)">*</span></label>
                  <input type="tel" id="resp_telefone" name="resp_telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel">
                  <span class="form-error" id="resp-telefone-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="resp_email">E-mail do Responsável</label>
                  <input type="email" id="resp_email" name="resp_email" class="form-control" placeholder="responsavel@email.com" maxlength="120">
                </div>
              </div>
            </div>
          </div>

          <!-- ── Matrícula ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                  </svg>
                </span>
                Matrícula
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="turma">Turma <span style="color:var(--color-danger)">*</span></label>
                  <select id="turma" name="turma" class="form-control" required>
                    <option value="">Selecione a turma…</option>
                  </select>
                  <span class="form-error" id="turma-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="data_matricula">Data de Matrícula <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="data_matricula" name="data_matricula" class="form-control" required>
                  <span class="form-error" id="matricula-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="status">Status <span style="color:var(--color-danger)">*</span></label>
                  <select id="status" name="status" class="form-control" required>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="inativo">Inativo</option>
                  </select>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="docente">Docente?</label>
                  <select id="docente" name="docente" class="form-control">
                    <option value="N">Não</option>
                    <option value="S">Sim</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Informações adicionais, necessidades especiais, histórico…" maxlength="500" rows="3"></textarea>
                <span class="form-hint"><span id="obs-count">0</span>/500 caracteres</span>
              </div>
            </div>
          </div>

          <!-- ── Rodapé do formulário ── -->
          <div style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-4) 0;border-top:1px solid var(--color-border)">
            <p class="form-hint" style="margin:0"><span style="color:var(--color-danger)">*</span> Campos obrigatórios</p>
            <div style="display:flex;gap:var(--space-3)">
              <a href="index.php?pagina=alunos" class="btn btn-secondary">Cancelar</a>
              <button type="reset" class="btn btn-ghost" id="btnLimpar">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Limpar
              </button>
              <button type="submit" class="btn btn-primary" id="btnSalvar">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Salvar Aluno
              </button>
            </div>
          </div>

        </form>

      <?php elseif ($pagina === 'aluno-editar'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  EDITAR ALUNO                                  -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Editar Aluno</h1>
            <p class="page-subtitle">Altere os dados do aluno e salve as modificações.</p>
          </div>
          <a href="index.php?pagina=alunos" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Voltar
          </a>
        </div>

        <div id="aluno-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formAluno" novalidate data-modo="editar" data-id="<?= (int)($_GET['id'] ?? 0) ?>">

          <!-- ── Dados Pessoais ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </span>
                Dados Pessoais
              </span>
            </div>
            <div class="card-body">
              <!-- Foto -->
              <div style="display:flex;align-items:flex-start;gap:var(--space-6);margin-bottom:var(--space-6)">
                <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);flex-shrink:0">
                  <div id="fotoPreview" style="width:96px;height:96px;border-radius:50%;border:2px dashed var(--color-border);background:var(--color-gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:border-color var(--transition-fast);position:relative" onclick="document.getElementById('fotoInput').click()" title="Clique para trocar a foto">
                    <svg id="fotoIcon" style="width:36px;height:36px;fill:var(--color-gray-400)" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <img id="fotoImg" src="" alt="Foto do aluno" style="display:none;width:100%;height:100%;object-fit:cover">
                    <div id="fotoOverlay" style="display:none;position:absolute;inset:0;background:rgba(0,0,0,.45);border-radius:50%;align-items:center;justify-content:center">
                      <svg style="width:24px;height:24px;fill:#fff" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                  </div>
                  <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none">
                  <div style="display:flex;gap:var(--space-2)">
                    <button type="button" id="btnTrocarFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px" onclick="document.getElementById('fotoInput').click()" title="Trocar foto">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                      Trocar
                    </button>
                    <button type="button" id="btnRemoverFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px;color:var(--color-danger);display:none" onclick="removerFotoPreview()" title="Remover foto">
                      <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      Remover
                    </button>
                  </div>
                  <input type="hidden" id="fotoRemover" name="foto_remover" value="0">
                  <span style="font-size:var(--text-xs);color:var(--color-text-muted);text-align:center">JPG, PNG ou WebP<br>máx. 2 MB</span>
                </div>

                <!-- Campos ao lado da foto -->
                <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                  <div class="form-group" style="grid-column:1/-1;margin-bottom:0">
                    <label class="form-label" for="nome">Nome completo <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: Maria Aparecida da Silva" maxlength="120" required autocomplete="name">
                    <span class="form-error" id="nome-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" autocomplete="off" inputmode="numeric">
                    <span class="form-error" id="cpf-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                  </div>
                </div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="sexo">Sexo <span style="color:var(--color-danger)">*</span></label>
                  <select id="sexo" name="sexo" class="form-control" required>
                    <option value="">Selecione…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                  </select>
                  <span class="form-error" id="sexo-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado_civil">Estado Civil</label>
                  <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">Selecione…</option>
                    <option value="solteiro">Solteiro(a)</option>
                    <option value="casado">Casado(a)</option>
                    <option value="divorciado">Divorciado(a)</option>
                    <option value="viuvo">Viúvo(a)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="profissao">Profissão</label>
                  <input type="text" id="profissao" name="profissao" class="form-control" placeholder="Ex.: Professor(a)" maxlength="80">
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-success)" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                </span>
                Contato
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="telefone">Telefone <span style="color:var(--color-danger)">*</span></label>
                  <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" required autocomplete="tel">
                  <span class="form-error" id="telefone-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="email">E-mail</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="aluno@email.com" maxlength="120" autocomplete="email">
                  <span class="form-error" id="email-error"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Endereço ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-warning)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                </span>
                Endereço
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:180px 1fr 80px 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cep">CEP</label>
                  <div style="position:relative">
                    <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000" maxlength="9" inputmode="numeric" autocomplete="postal-code">
                    <span id="cep-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%)">
                      <svg style="width:16px;height:16px;fill:var(--color-gray-400);animation:spin 1s linear infinite" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                      </svg>
                    </span>
                  </div>
                  <span class="form-error" id="cep-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="logradouro">Logradouro</label>
                  <input type="text" id="logradouro" name="logradouro" class="form-control" placeholder="Rua, Avenida…" maxlength="150" autocomplete="street-address">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="numero">Nº</label>
                  <input type="text" id="numero" name="numero" class="form-control" placeholder="Nº" maxlength="10">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="complemento">Complemento</label>
                  <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto, Bloco…" maxlength="60">
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 100px;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Bairro" maxlength="80">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado">UF</label>
                  <select id="estado" name="estado" class="form-control">
                    <option value="">UF</option>
                    <option>AC</option>
                    <option>AL</option>
                    <option>AP</option>
                    <option>AM</option>
                    <option>BA</option>
                    <option>CE</option>
                    <option>DF</option>
                    <option>ES</option>
                    <option>GO</option>
                    <option>MA</option>
                    <option>MT</option>
                    <option>MS</option>
                    <option>MG</option>
                    <option>PA</option>
                    <option>PB</option>
                    <option>PR</option>
                    <option>PE</option>
                    <option>PI</option>
                    <option>RJ</option>
                    <option>RN</option>
                    <option>RS</option>
                    <option>RO</option>
                    <option>RR</option>
                    <option>SC</option>
                    <option>SP</option>
                    <option>SE</option>
                    <option>TO</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Matrícula ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                  </svg>
                </span>
                Matrícula
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="turma">Turma <span style="color:var(--color-danger)">*</span></label>
                  <select id="turma" name="turma" class="form-control" required>
                    <option value="">Selecione a turma…</option>
                  </select>
                  <span class="form-error" id="turma-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="data_matricula">Data de Matrícula <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="data_matricula" name="data_matricula" class="form-control" required>
                  <span class="form-error" id="matricula-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="status">Status <span style="color:var(--color-danger)">*</span></label>
                  <select id="status" name="status" class="form-control" required>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="inativo">Inativo</option>
                  </select>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="docente">Docente?</label>
                  <select id="docente" name="docente" class="form-control">
                    <option value="N">Não</option>
                    <option value="S">Sim</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Informações adicionais…" maxlength="500" rows="3"></textarea>
                <span class="form-hint"><span id="obs-count">0</span>/500 caracteres</span>
              </div>
            </div>
          </div>

          <!-- Rodapé -->
          <div style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-4) 0;border-top:1px solid var(--color-border)">
            <p class="form-hint" style="margin:0"><span style="color:var(--color-danger)">*</span> Campos obrigatórios</p>
            <div style="display:flex;gap:var(--space-3)">
              <a href="index.php?pagina=alunos" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" id="btnSalvar">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Salvar Alterações
              </button>
            </div>
          </div>

        </form>

      <?php elseif ($pagina === 'professores'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  LISTAGEM DE PROFESSORES                       -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Professores</h1>
            <p class="page-subtitle">Gerencie os professores cadastrados no sistema.</p>
          </div>
          <a href="index.php?pagina=professor-novo" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Novo Professor
          </a>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="padding:var(--space-4) var(--space-6)">
            <div style="display:grid;grid-template-columns:1fr 200px 200px auto;gap:var(--space-3);align-items:flex-end">
              <div>
                <label class="form-label" for="filtro-prof-busca">Buscar</label>
                <input type="text" id="filtro-prof-busca" class="form-control" placeholder="Nome, CPF ou e-mail…">
              </div>
              <div>
                <label class="form-label" for="filtro-prof-status">Status</label>
                <select id="filtro-prof-status" class="form-control">
                  <option value="">Todos</option>
                  <option value="ativo">Ativo</option>
                  <option value="pendente">Pendente</option>
                  <option value="inativo">Inativo</option>
                </select>
              </div>
              <div>
                <label class="form-label" for="filtro-prof-turma">Turma</label>
                <select id="filtro-prof-turma" class="form-control">
                  <option value="">Todas</option>
                </select>
              </div>
              <button class="btn btn-secondary" id="btnFiltrarProf">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                Filtrar
              </button>
            </div>
          </div>
        </div>

        <!-- Alerta de operação -->
        <div id="lista-prof-alert" style="display:none;margin-bottom:var(--space-4)"></div>

        <!-- Tabela -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Lista de Professores</span>
            <span id="total-professores" class="badge badge-primary" style="font-size:var(--text-sm)">—</span>
          </div>
          <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
            <table class="table" id="tabela-professores">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Foto</th>
                  <th>Nome</th>
                  <th>Turma</th>
                  <th>Telefone</th>
                  <th>Matrícula</th>
                  <th>Status</th>
                  <th style="text-align:right">Ações</th>
                </tr>
              </thead>
              <tbody id="tbody-professores">
                <tr>
                  <td colspan="8" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">
                    Carregando…
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <div class="modal-overlay" id="modalExcluirProf" style="display:none">
          <div class="modal" style="max-width:420px">
            <div class="modal-header">
              <span class="modal-title">Confirmar exclusão</span>
              <button class="modal-close" onclick="document.getElementById('modalExcluirProf').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
              <p style="margin:0">Tem certeza que deseja excluir o professor <strong id="modal-nome-prof"></strong>?<br>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
              </p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="document.getElementById('modalExcluirProf').style.display='none'">Cancelar</button>
              <button class="btn btn-danger" id="btnConfirmarExcluirProf">
                <svg class="icon" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Excluir
              </button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'professor-novo'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CADASTRO DE PROFESSOR                         -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Cadastrar Professor</h1>
            <p class="page-subtitle">Preencha os dados do novo professor para realizar o cadastro.</p>
          </div>
          <a href="index.php?pagina=professores" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Voltar
          </a>
        </div>

        <div id="aluno-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formAluno" novalidate data-modo="criar" data-id="0" data-retorno="professores">

          <!-- ── Dados Pessoais ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </span>
                Dados Pessoais
              </span>
            </div>
            <div class="card-body">
              <div style="display:flex;align-items:flex-start;gap:var(--space-6);margin-bottom:var(--space-6)">
                <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);flex-shrink:0">
                  <div id="fotoPreview" style="width:96px;height:96px;border-radius:50%;border:2px dashed var(--color-border);background:var(--color-gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:border-color var(--transition-fast);position:relative" onclick="document.getElementById('fotoInput').click()" title="Clique para trocar a foto">
                    <svg id="fotoIcon" style="width:36px;height:36px;fill:var(--color-gray-400)" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <img id="fotoImg" src="" alt="Foto do professor" style="display:none;width:100%;height:100%;object-fit:cover">
                    <div id="fotoOverlay" style="display:none;position:absolute;inset:0;background:rgba(0,0,0,.45);border-radius:50%;align-items:center;justify-content:center">
                      <svg style="width:24px;height:24px;fill:#fff" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                  </div>
                  <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none">
                  <div style="display:flex;gap:var(--space-2)">
                    <button type="button" id="btnTrocarFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px" onclick="document.getElementById('fotoInput').click()" title="Trocar foto">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                      Trocar
                    </button>
                    <button type="button" id="btnRemoverFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px;color:var(--color-danger);display:none" onclick="removerFotoPreview()" title="Remover foto">
                      <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      Remover
                    </button>
                  </div>
                  <input type="hidden" id="fotoRemover" name="foto_remover" value="0">
                  <span style="font-size:var(--text-xs);color:var(--color-text-muted);text-align:center">JPG, PNG ou WebP<br>máx. 2 MB</span>
                </div>
                <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                  <div class="form-group" style="grid-column:1/-1;margin-bottom:0">
                    <label class="form-label" for="nome">Nome completo <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: João da Silva" maxlength="120" required autocomplete="name">
                    <span class="form-error" id="nome-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" autocomplete="off" inputmode="numeric">
                    <span class="form-error" id="cpf-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                  </div>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="sexo">Sexo <span style="color:var(--color-danger)">*</span></label>
                  <select id="sexo" name="sexo" class="form-control" required>
                    <option value="">Selecione…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                  </select>
                  <span class="form-error" id="sexo-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado_civil">Estado Civil</label>
                  <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">Selecione…</option>
                    <option value="solteiro">Solteiro(a)</option>
                    <option value="casado">Casado(a)</option>
                    <option value="divorciado">Divorciado(a)</option>
                    <option value="viuvo">Viúво(a)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="profissao">Profissão</label>
                  <input type="text" id="profissao" name="profissao" class="form-control" placeholder="Ex.: Pastor(a)" maxlength="80">
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-success)" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                </span>
                Contato
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="telefone">Telefone <span style="color:var(--color-danger)">*</span></label>
                  <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" required autocomplete="tel">
                  <span class="form-error" id="telefone-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="email">E-mail</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="professor@email.com" maxlength="120" autocomplete="email">
                  <span class="form-error" id="email-error"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Endereço ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-warning)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                </span>
                Endereço
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:180px 1fr 80px 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cep">CEP</label>
                  <div style="position:relative">
                    <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000" maxlength="9" inputmode="numeric" autocomplete="postal-code">
                    <span id="cep-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%)">
                      <svg style="width:16px;height:16px;fill:var(--color-gray-400);animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    </span>
                  </div>
                  <span class="form-error" id="cep-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="logradouro">Logradouro</label>
                  <input type="text" id="logradouro" name="logradouro" class="form-control" placeholder="Rua, Avenida…" maxlength="150" autocomplete="street-address">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="numero">Nº</label>
                  <input type="text" id="numero" name="numero" class="form-control" placeholder="Nº" maxlength="10">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="complemento">Complemento</label>
                  <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto, Bloco…" maxlength="60">
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 100px;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Bairro" maxlength="80">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado">UF</label>
                  <select id="estado" name="estado" class="form-control" autocomplete="address-level1">
                    <option value="">UF</option>
                    <option>AC</option><option>AL</option><option>AP</option><option>AM</option><option>BA</option>
                    <option>CE</option><option>DF</option><option>ES</option><option>GO</option><option>MA</option>
                    <option>MT</option><option>MS</option><option>MG</option><option>PA</option><option>PB</option>
                    <option>PR</option><option>PE</option><option>PI</option><option>RJ</option><option>RN</option>
                    <option>RS</option><option>RO</option><option>RR</option><option>SC</option><option>SP</option>
                    <option>SE</option><option>TO</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Matrícula ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                  </svg>
                </span>
                Matrícula
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="turma">Turma <span style="color:var(--color-danger)">*</span></label>
                  <select id="turma" name="turma" class="form-control" required>
                    <option value="">Selecione a turma…</option>
                  </select>
                  <span class="form-error" id="turma-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="data_matricula">Data de Matrícula <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="data_matricula" name="data_matricula" class="form-control" required>
                  <span class="form-error" id="matricula-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="status">Status <span style="color:var(--color-danger)">*</span></label>
                  <select id="status" name="status" class="form-control" required>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="inativo">Inativo</option>
                  </select>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="docente">Docente?</label>
                  <select id="docente" name="docente" class="form-control">
                    <option value="N">Não</option>
                    <option value="S" selected>Sim</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Informações adicionais…" maxlength="500" rows="3"></textarea>
                <span class="form-hint"><span id="obs-count">0</span>/500 caracteres</span>
              </div>
            </div>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-4) 0;border-top:1px solid var(--color-border)">
            <p class="form-hint" style="margin:0"><span style="color:var(--color-danger)">*</span> Campos obrigatórios</p>
            <div style="display:flex;gap:var(--space-3)">
              <a href="index.php?pagina=professores" class="btn btn-secondary">Cancelar</a>
              <button type="reset" class="btn btn-ghost" id="btnLimpar">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                Limpar
              </button>
              <button type="submit" class="btn btn-primary" id="btnSalvar">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar Professor
              </button>
            </div>
          </div>

        </form>

      <?php elseif ($pagina === 'professor-editar'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  EDITAR PROFESSOR                              -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Editar Professor</h1>
            <p class="page-subtitle">Altere os dados do professor e salve as modificações.</p>
          </div>
          <a href="index.php?pagina=professores" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Voltar
          </a>
        </div>

        <div id="aluno-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formAluno" novalidate data-modo="editar" data-id="<?= (int)($_GET['id'] ?? 0) ?>" data-retorno="professores">

          <!-- ── Dados Pessoais ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </span>
                Dados Pessoais
              </span>
            </div>
            <div class="card-body">
              <div style="display:flex;align-items:flex-start;gap:var(--space-6);margin-bottom:var(--space-6)">
                <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);flex-shrink:0">
                  <div id="fotoPreview" style="width:96px;height:96px;border-radius:50%;border:2px dashed var(--color-border);background:var(--color-gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:border-color var(--transition-fast);position:relative" onclick="document.getElementById('fotoInput').click()" title="Clique para trocar a foto">
                    <svg id="fotoIcon" style="width:36px;height:36px;fill:var(--color-gray-400)" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <img id="fotoImg" src="" alt="Foto do professor" style="display:none;width:100%;height:100%;object-fit:cover">
                    <div id="fotoOverlay" style="display:none;position:absolute;inset:0;background:rgba(0,0,0,.45);border-radius:50%;align-items:center;justify-content:center">
                      <svg style="width:24px;height:24px;fill:#fff" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                  </div>
                  <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none">
                  <div style="display:flex;gap:var(--space-2)">
                    <button type="button" id="btnTrocarFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px" onclick="document.getElementById('fotoInput').click()" title="Trocar foto">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                      Trocar
                    </button>
                    <button type="button" id="btnRemoverFoto" class="btn btn-ghost btn-sm" style="font-size:var(--text-xs);padding:2px 8px;color:var(--color-danger);display:none" onclick="removerFotoPreview()" title="Remover foto">
                      <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      Remover
                    </button>
                  </div>
                  <input type="hidden" id="fotoRemover" name="foto_remover" value="0">
                  <span style="font-size:var(--text-xs);color:var(--color-text-muted);text-align:center">JPG, PNG ou WebP<br>máx. 2 MB</span>
                </div>
                <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                  <div class="form-group" style="grid-column:1/-1;margin-bottom:0">
                    <label class="form-label" for="nome">Nome completo <span style="color:var(--color-danger)">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: João da Silva" maxlength="120" required autocomplete="name">
                    <span class="form-error" id="nome-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" autocomplete="off" inputmode="numeric">
                    <span class="form-error" id="cpf-error"></span>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label" for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                  </div>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="sexo">Sexo <span style="color:var(--color-danger)">*</span></label>
                  <select id="sexo" name="sexo" class="form-control" required>
                    <option value="">Selecione…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                  </select>
                  <span class="form-error" id="sexo-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado_civil">Estado Civil</label>
                  <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">Selecione…</option>
                    <option value="solteiro">Solteiro(a)</option>
                    <option value="casado">Casado(a)</option>
                    <option value="divorciado">Divorciado(a)</option>
                    <option value="viuvo">Viúво(a)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="profissao">Profissão</label>
                  <input type="text" id="profissao" name="profissao" class="form-control" placeholder="Ex.: Pastor(a)" maxlength="80">
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-success)" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                </span>
                Contato
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="telefone">Telefone <span style="color:var(--color-danger)">*</span></label>
                  <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" required autocomplete="tel">
                  <span class="form-error" id="telefone-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="email">E-mail</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="professor@email.com" maxlength="120" autocomplete="email">
                  <span class="form-error" id="email-error"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Endereço ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-warning)" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                </span>
                Endereço
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:180px 1fr 80px 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cep">CEP</label>
                  <div style="position:relative">
                    <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000" maxlength="9" inputmode="numeric" autocomplete="postal-code">
                    <span id="cep-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%)">
                      <svg style="width:16px;height:16px;fill:var(--color-gray-400);animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    </span>
                  </div>
                  <span class="form-error" id="cep-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="logradouro">Logradouro</label>
                  <input type="text" id="logradouro" name="logradouro" class="form-control" placeholder="Rua, Avenida…" maxlength="150" autocomplete="street-address">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="numero">Nº</label>
                  <input type="text" id="numero" name="numero" class="form-control" placeholder="Nº" maxlength="10">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="complemento">Complemento</label>
                  <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto, Bloco…" maxlength="60">
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 100px;gap:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Bairro" maxlength="80">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="estado">UF</label>
                  <select id="estado" name="estado" class="form-control">
                    <option value="">UF</option>
                    <option>AC</option><option>AL</option><option>AP</option><option>AM</option><option>BA</option>
                    <option>CE</option><option>DF</option><option>ES</option><option>GO</option><option>MA</option>
                    <option>MT</option><option>MS</option><option>MG</option><option>PA</option><option>PB</option>
                    <option>PR</option><option>PE</option><option>PI</option><option>RJ</option><option>RN</option>
                    <option>RS</option><option>RO</option><option>RR</option><option>SC</option><option>SP</option>
                    <option>SE</option><option>TO</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Matrícula ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg style="width:16px;height:16px;fill:var(--color-primary)" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zm5.99 7.176A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                  </svg>
                </span>
                Matrícula
              </span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="turma">Turma <span style="color:var(--color-danger)">*</span></label>
                  <select id="turma" name="turma" class="form-control" required>
                    <option value="">Selecione a turma…</option>
                  </select>
                  <span class="form-error" id="turma-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="data_matricula">Data de Matrícula <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="data_matricula" name="data_matricula" class="form-control" required>
                  <span class="form-error" id="matricula-error"></span>
                </div>
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="status">Status <span style="color:var(--color-danger)">*</span></label>
                  <select id="status" name="status" class="form-control" required>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="inativo">Inativo</option>
                  </select>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4)">
                <div class="form-group" style="margin-bottom:0">
                  <label class="form-label" for="docente">Docente?</label>
                  <select id="docente" name="docente" class="form-control">
                    <option value="N">Não</option>
                    <option value="S">Sim</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Informações adicionais…" maxlength="500" rows="3"></textarea>
                <span class="form-hint"><span id="obs-count">0</span>/500 caracteres</span>
              </div>
            </div>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-4) 0;border-top:1px solid var(--color-border)">
            <p class="form-hint" style="margin:0"><span style="color:var(--color-danger)">*</span> Campos obrigatórios</p>
            <div style="display:flex;gap:var(--space-3)">
              <a href="index.php?pagina=professores" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" id="btnSalvar">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar Alterações
              </button>
            </div>
          </div>

        </form>

      <?php elseif ($pagina === 'turmas'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  LISTAGEM DE TURMAS                            -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Turmas</h1>
            <p class="page-subtitle">Gerencie as turmas da escola bíblica.</p>
          </div>
          <a href="index.php?pagina=turma-nova" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Nova Turma
          </a>
        </div>

        <div id="turmas-alert" style="display:none;margin-bottom:var(--space-4)"></div>

        <!-- Barra de busca -->
        <div class="card" style="margin-bottom:var(--space-4)">
          <div class="card-body" style="display:flex;gap:var(--space-3);align-items:flex-end;padding:var(--space-4)">
            <div style="flex:1">
              <label class="form-label" for="turma-busca">Buscar turma</label>
              <input type="text" id="turma-busca" class="form-control" placeholder="Digite o nome da turma…">
            </div>
            <button class="btn btn-secondary" id="btnBuscarTurma">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
              Buscar
            </button>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Lista de Turmas</span>
            <span id="total-turmas" class="badge badge-primary" style="font-size:var(--text-sm)">—</span>
          </div>
          <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nome da Turma</th>
                  <th>Alunos</th>
                  <th style="text-align:right">Ações</th>
                </tr>
              </thead>
              <tbody id="tbody-turmas">
                <tr><td colspan="4" class="text-center" style="padding:var(--space-10);color:var(--color-gray-400)">Carregando…</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal exclusão -->
        <div class="modal-overlay" id="modalExcluirTurma" style="display:none">
          <div class="modal" style="max-width:420px">
            <div class="modal-header">
              <span class="modal-title">Confirmar exclusão</span>
              <button class="modal-close" onclick="document.getElementById('modalExcluirTurma').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
              <p style="margin:0">Tem certeza que deseja excluir a turma <strong id="modal-nome-turma"></strong>?<br>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
              </p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="document.getElementById('modalExcluirTurma').style.display='none'">Cancelar</button>
              <button class="btn btn-danger" id="btnConfirmarExcluirTurma">Excluir</button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'turma-nova'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  NOVA TURMA                                    -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Nova Turma</h1>
            <p class="page-subtitle">Cadastre uma nova turma na escola bíblica.</p>
          </div>
          <a href="index.php?pagina=turmas" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
            Voltar
          </a>
        </div>

        <div id="turma-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formTurma" novalidate data-modo="criar">
          <div class="card" style="max-width:520px">
            <div class="card-header">
              <span class="card-title">Dados da Turma</span>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label" for="nome_turma">Nome da Turma <span style="color:var(--color-danger)">*</span></label>
                <input type="text" id="nome_turma" name="nome_turma" class="form-control" placeholder="Ex.: Abraão" maxlength="50" required autofocus>
                <span class="form-error" id="nome_turma-error"></span>
              </div>
            </div>
            <div class="card-body" style="border-top:1px solid var(--color-border);display:flex;justify-content:flex-end;gap:var(--space-3)">
              <a href="index.php?pagina=turmas" class="btn btn-secondary">Cancelar</a>
              <button type="reset" class="btn btn-ghost" id="btnLimparTurma">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                Limpar
              </button>
              <button type="submit" class="btn btn-primary" id="btnSalvarTurma">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar Turma
              </button>
            </div>
          </div>
        </form>

      <?php elseif ($pagina === 'turma-editar'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  EDITAR TURMA                                  -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Editar Turma</h1>
            <p class="page-subtitle">Altere o nome da turma e salve as modificações.</p>
          </div>
          <a href="index.php?pagina=turmas" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
            Voltar
          </a>
        </div>

        <div id="turma-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formTurma" novalidate data-modo="editar" data-id="<?= (int)($_GET['id'] ?? 0) ?>">
          <div class="card" style="max-width:520px">
            <div class="card-header">
              <span class="card-title">Dados da Turma</span>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label" for="nome_turma">Nome da Turma <span style="color:var(--color-danger)">*</span></label>
                <input type="text" id="nome_turma" name="nome_turma" class="form-control" placeholder="Ex.: Abraão" maxlength="50" required autofocus>
                <span class="form-error" id="nome_turma-error"></span>
              </div>
            </div>
            <div class="card-body" style="border-top:1px solid var(--color-border);display:flex;justify-content:flex-end;gap:var(--space-3)">
              <a href="index.php?pagina=turmas" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" id="btnSalvarTurma">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar Alterações
              </button>
            </div>
          </div>
        </form>

      <?php else: ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  PÁGINAS GENÉRICAS (em construção)             -->
        <!-- ══════════════════════════════════════════════ -->
        <?php
        $titulos = [
          'aluno-novo'     => 'Cadastrar Aluno',
          'aluno-editar'   => 'Editar Aluno',
          'professor-editar' => 'Editar Professor',
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
            <svg style="width:56px;height:56px;fill:var(--color-gray-300);margin:0 auto var(--space-4)" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
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
      dashboard: 'Dashboard',
      alunos: 'Listar Alunos',
      'aluno-novo': 'Cadastrar Aluno',
      'aluno-editar': 'Editar Aluno',
      turmas: 'Turmas',
      'turma-nova': 'Nova Turma',
      'turma-editar': 'Editar Turma',
      professores: 'Listar Professores',
      'professor-novo': 'Cadastrar Professor',
      aulas: 'Listar Aulas',
      'aula-nova': 'Registrar Aula',
      frequencia: 'Frequência',
      calendario: 'Calendário',
      'rel-geral': 'Frequência Geral',
      'rel-turma': 'Relatório por Turma',
      'rel-aluno': 'Relatório por Aluno',
      configuracoes: 'Configurações',
    };

    const params = new URLSearchParams(window.location.search);
    const current = params.get('pagina') || 'dashboard';
    const titleEl = document.getElementById('pageTitle');
    if (titleEl && titulos[current]) titleEl.textContent = titulos[current];

    // ── Mobile: hambúrguer ─────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburgerBtn = document.getElementById('hamburgerBtn');

    function openSidebar() {
      sidebar.classList.add('is-open');
      overlay.classList.add('visible');
    }

    function closeSidebar() {
      sidebar.classList.remove('is-open');
      overlay.classList.remove('visible');
    }

    hamburgerBtn.addEventListener('click', () => {
      sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // ════════════════════════════════════════════════
    //  LISTAGEM DE ALUNOS
    // ════════════════════════════════════════════════
    (function() {
      if (!document.getElementById('tabela-alunos')) return;

      const tbody = document.getElementById('tbody-alunos');
      const totalEl = document.getElementById('total-alunos');
      const listaAlert = document.getElementById('lista-alert');
      let excluirId = null;

      function showListAlert(msg, tipo) {
        listaAlert.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + msg + '</span></div>';
        listaAlert.style.display = 'block';
        setTimeout(() => {
          listaAlert.style.display = 'none';
        }, 4000);
      }

      function fmtData(s) {
        if (!s) return '—';
        const [y, m, d] = s.split('-');
        return d + '/' + m + '/' + y;
      }

      function badgeStatus(s) {
        const map = {
          ativo: 'success',
          pendente: 'warning',
          inativo: 'danger'
        };
        const label = {
          ativo: 'Ativo',
          pendente: 'Pendente',
          inativo: 'Inativo'
        };
        return '<span class="badge badge-' + (map[s] || 'primary') + '">' + (label[s] || s) + '</span>';
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

      function fmtTel(n) {
        if (!n) return '—';
        const s = String(n).replace(/\D/g, '');
        if (s.length === 11) return '(' + s.slice(0, 2) + ') ' + s.slice(2, 7) + '-' + s.slice(7);
        if (s.length === 10) return '(' + s.slice(0, 2) + ') ' + s.slice(2, 6) + '-' + s.slice(6);
        return s;
      }

      function escHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }

      function carregarAlunos() {
        const busca = document.getElementById('filtro-busca').value.trim();
        const status = document.getElementById('filtro-status').value;
        const turma = document.getElementById('filtro-turma').value;

        tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:var(--space-6);color:var(--color-gray-400)">Carregando…</td></tr>';

        const params = new URLSearchParams({
          busca,
          status,
          turma
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

      document.getElementById('btnFiltrar').addEventListener('click', carregarAlunos);
      document.getElementById('filtro-busca').addEventListener('keydown', e => {
        if (e.key === 'Enter') carregarAlunos();
      });

      // Carga inicial
      carregarAlunos();
    })();

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

      function fmtData(s) {
        if (!s) return '—';
        const [y, m, d] = s.split('-');
        return d + '/' + m + '/' + y;
      }

      function badgeStatus(s) {
        const map   = { ativo: 'success', pendente: 'warning', inativo: 'danger' };
        const label = { ativo: 'Ativo',   pendente: 'Pendente', inativo: 'Inativo' };
        return '<span class="badge badge-' + (map[s] || 'primary') + '">' + (label[s] || s) + '</span>';
      }

      function escH(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }

      function fmtTel(n) {
        if (!n) return '—';
        const s = String(n).replace(/\D/g, '');
        if (s.length === 11) return '(' + s.slice(0,2) + ') ' + s.slice(2,7) + '-' + s.slice(7);
        if (s.length === 10) return '(' + s.slice(0,2) + ') ' + s.slice(2,6) + '-' + s.slice(6);
        return s;
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
        tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:var(--space-6);color:var(--color-gray-400)">Carregando…</td></tr>';
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
      document.getElementById('filtro-prof-busca').addEventListener('keydown', e => {
        if (e.key === 'Enter') carregarProfessores();
      });

      carregarProfessores();
    })();

    // ════════════════════════════════════════════════
    //  FORMULÁRIO DE ALUNO (criar / editar)
    // ════════════════════════════════════════════════
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
            setValue('profissao', a.profissao);
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
            // Exibe foto existente no preview
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

      // ── Helpers alert/error ───────────────────────────
      function showAlert(msg, tipo) {
        const el = document.getElementById('aluno-alert');
        if (!el) return;
        el.innerHTML = '<div class="alert alert-' + tipo + '"><svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg><span>' + msg + '</span></div>';
        el.style.display = 'block';
        el.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest'
        });
      }

      function clearErrors() {
        form.querySelectorAll('.form-error').forEach(e => e.textContent = '');
        form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
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

        // Monta FormData para suportar upload de foto
        const fd = new FormData();
        fd.append('id',            alunoId || '');
        fd.append('nome',          document.getElementById('nome')?.value.trim() || '');
        fd.append('sexo',          document.getElementById('sexo')?.value || '');
        fd.append('cpf',           document.getElementById('cpf')?.value || '');
        fd.append('estado_civil',  document.getElementById('estado_civil')?.value || '');
        fd.append('data_nascimento', document.getElementById('data_nascimento')?.value || '');
        fd.append('profissao',     document.getElementById('profissao')?.value.trim() || '');
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
        // Inclui arquivo de foto apenas se o usuário selecionou um novo
        const fotoFile = document.getElementById('fotoInput')?.files[0];
        if (fotoFile) fd.append('foto', fotoFile);
        // Sinaliza remoção de foto (sem novo arquivo)
        fd.append('foto_remover', document.getElementById('fotoRemover')?.value || '0');

        const btn = document.getElementById('btnSalvar');
        btn.disabled = true;
        btn.innerHTML = '<svg class="icon" style="animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg> Salvando…';

        // POST para criar; POST com ?_method=PUT para editar (FormData não funciona com PUT nativo no PHP)
        const url = modo === 'editar' ? 'alunos_crud.php?_method=PUT' : 'alunos_crud.php';
        fetch(url, { method: 'POST', body: fd })
          .then(r => r.json())
          .then(data => {
            if (!data.ok) {
              // Exibe erros de campo retornados pelo servidor
              if (data.erros) {
                Object.entries(data.erros).forEach(([campo, msg]) => {
                  const mapId = {
                    nome: 'nome',
                    sexo: 'sexo',
                    cpf: 'cpf',
                    telefone: 'telefone',
                    email: 'email',
                    turma: 'turma',
                    data_matricula: 'data_matricula'
                  };
                  const mapErr = {
                    nome: 'nome-error',
                    sexo: 'sexo-error',
                    cpf: 'cpf-error',
                    telefone: 'telefone-error',
                    email: 'email-error',
                    turma: 'turma-error',
                    data_matricula: 'matricula-error'
                  };
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
            // reset foto preview
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

    // ── keyframe spin ─────────────────────────────────
    if (!document.getElementById('spinStyle')) {
      const s = document.createElement('style');
      s.id = 'spinStyle';
      s.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
      document.head.appendChild(s);
    }

    // ══════════════════════════════════════════════════
    // TURMAS — carrega select dinâmico em formulários de aluno
    // ══════════════════════════════════════════════════
    (function loadTurmasSelect() {
      const selects = document.querySelectorAll('select#turma, select#filtro-turma, select#filtro-prof-turma');
      if (!selects.length) return;
      fetch('turmas_crud.php')
        .then(r => r.json())
        .then(data => {
          if (!data.ok) return;
          selects.forEach(sel => {
            const val = sel.value; // preserva valor já selecionado (modo editar)
            // Mantém apenas o option vazio/padrão inicial
            while (sel.options.length > 1) sel.remove(1);
            data.turmas.forEach(t => {
              const opt = document.createElement('option');
              opt.value = t.nome_turma;
              opt.textContent = t.nome_turma;
              sel.appendChild(opt);
            });
            if (val) sel.value = val; // restaura seleção
          });
        })
        .catch(() => {});
    })();

    // ══════════════════════════════════════════════════
    // CRUD TURMAS — listagem, novo, editar
    // ══════════════════════════════════════════════════
    (function initTurmas() {
      // ── Listagem ──────────────────────────────────
      const tbody = document.getElementById('tbody-turmas');
      if (tbody) {
        let excluirId = null;

        function escHtmlT(s) {
          return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

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

        function carregarTurmas() {
          const busca = document.getElementById('turma-busca')?.value.trim() || '';
          tbody.innerHTML = '<tr><td colspan="4" class="text-center" style="padding:var(--space-6);color:var(--color-gray-400)">Carregando…</td></tr>';
          fetch('turmas_crud.php?' + new URLSearchParams({ busca }))
            .then(r => r.json())
            .then(data => {
              if (!data.ok) { showTurmaAlert(data.msg || 'Erro ao carregar.', 'danger'); return; }
              document.getElementById('total-turmas').textContent = data.total + ' turma' + (data.total !== 1 ? 's' : '');
              renderTurmas(data.turmas);
            })
            .catch(() => showTurmaAlert('Falha na comunicação com o servidor.', 'danger'));
        }

        carregarTurmas();

        document.getElementById('btnBuscarTurma')?.addEventListener('click', carregarTurmas);
        document.getElementById('turma-busca')?.addEventListener('keydown', e => { if (e.key === 'Enter') carregarTurmas(); });

        // Modal exclusão
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
      }

      // Pré-preenche no modo editar
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

    // Dashboard — cards dinâmicos
    (function initDashboard() {
      const valAlunos   = document.getElementById('dash-val-alunos');
      const trendAlunos = document.getElementById('dash-trend-alunos');
      const valProf     = document.getElementById('dash-val-prof');
      const trendProf   = document.getElementById('dash-trend-prof');
      const valTurmas   = document.getElementById('dash-val-turmas');
      const trendTurmas = document.getElementById('dash-trend-turmas');
      if (!valAlunos) return;

      const svgUp   = '<svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>';
      const svgDown = '<svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';

      fetch('alunos_crud.php?stats=1')
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          valAlunos.textContent = d.total;
          const diff = d.novos_mes - d.novos_mes_anterior;
          const label = (diff >= 0 ? '+' : '') + d.novos_mes + ' este mês';
          trendAlunos.className = 'trend ' + (diff >= 0 ? 'trend-up' : 'trend-down');
          trendAlunos.innerHTML  = (diff >= 0 ? svgUp : svgDown) + ' ' + label;
          if (valProf) {
            valProf.textContent = d.docentes;
            trendProf.innerHTML = svgUp + ' docente' + (d.docentes !== 1 ? 's' : '') + ' ativo' + (d.docentes !== 1 ? 's' : '');
          }
        })
        .catch(() => { valAlunos.textContent = '—'; });

      fetch('turmas_crud.php')
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          const total = typeof d.total === 'number' ? d.total : (d.turmas?.length ?? 0);
          valTurmas.textContent = total;
          trendTurmas.innerHTML = svgUp + ' ' + total + ' cadastrada' + (total !== 1 ? 's' : '');
        })
        .catch(() => { valTurmas.textContent = '—'; });

      // Últimas matrículas
      const tbodyMat = document.getElementById('tbody-ultimas-matriculas');
      // Aniversariantes do mês
      const tbodyAniv = document.getElementById('tbody-aniversariantes');
      if (tbodyAniv) {
        const nomeMes = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                         'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        const mesAtual = new Date().getMonth(); // 0-based
        const badge = document.getElementById('dash-aniv-mes');
        if (badge) badge.textContent = nomeMes[mesAtual] + ' ' + new Date().getFullYear();
        fetch('alunos_crud.php?aniversariantes=1')
          .then(r => r.json())
          .then(d => {
            if (!d.ok || !d.alunos.length) {
              tbodyAniv.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Nenhum aniversariante este mês.</td></tr>';
              return;
            }
            const hoje = new Date().getDate();
            tbodyAniv.innerHTML = d.alunos.map(a => {
              const [y, m, dia] = a.data_nascimento.split('-');
              const diaNum = parseInt(dia, 10);
              const isHoje = diaNum === hoje;
              const dataFmt = dia + '/' + m;
              const anoLabel = y ? '<small style="color:var(--color-text-muted)"> (' + (new Date().getFullYear() - parseInt(y,10)) + ' anos)</small>' : '';
              const turma = a.turma || '<span style="color:var(--color-text-muted)">—</span>';
              const destaque = isHoje ? 'background:var(--color-warning-light,#fef9c3)' : '';
              return `<tr style="${destaque}">
                <td><strong>${a.nome}</strong>${isHoje ? ' 🎂' : ''}</td>
                <td>${turma}</td>
                <td>${dataFmt}${anoLabel}</td>
              </tr>`;
            }).join('');
          })
          .catch(() => {
            tbodyAniv.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--color-text-muted)">Erro ao carregar.</td></tr>';
          });
      }
      if (tbodyMat) {
        const badgeCls = { ativo: 'badge-success', pendente: 'badge-warning', inativo: 'badge-danger' };
        const badgeLbl = { ativo: 'Ativo', pendente: 'Pendente', inativo: 'Inativo' };
        const fmtDate  = s => {
          if (!s) return '—';
          const [y, m, d] = s.split('-');
          return (d || '?') + '/' + (m || '?') + '/' + (y || '?');
        };
        fetch('alunos_crud.php?recentes=5')
          .then(r => r.json())
          .then(d => {
            if (!d.ok || !d.alunos.length) {
              tbodyMat.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:var(--space-6)">Nenhum aluno cadastrado ainda.</td></tr>';
              return;
            }
            tbodyMat.innerHTML = d.alunos.map(a => {
              const st  = (a.status || 'pendente').toLowerCase();
              const cls = badgeCls[st] || 'badge-secondary';
              const lbl = badgeLbl[st] || a.status;
              const email = a.usuario_email
                ? '<br><small class="text-muted">' + a.usuario_email + '</small>'
                : '';
              const turma = a.turma || '<span style="color:var(--color-text-muted)">—</span>';
              return `<tr>
                <td><strong>${a.nome}</strong>${email}</td>
                <td>${turma}</td>
                <td>${fmtDate(a.data_matricula)}</td>
                <td><span class="badge ${cls}">${lbl}</span></td>
              </tr>`;
            }).join('');
          })
          .catch(() => {
            tbodyMat.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--color-text-muted)">Erro ao carregar.</td></tr>';
          });
      }
    })();

  </script>

</body>

</html>