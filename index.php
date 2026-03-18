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
  <!-- Aplica o tema salvo antes de renderizar (evita flash) -->
  <script>
    (function(){
      var t = localStorage.getItem('escola-theme');
      if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
    })();
  </script>
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
      <div class="sidebar__group <?= in_array($pagina, ['aulas', 'tema-novo', 'tema-editar', 'tema-detalhe', 'aula-nova', 'frequencia', 'cronograma', 'calendario']) ? 'open' : '' ?>">
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
            <a href="index.php?pagina=aulas" class="sidebar__submenu-link <?= in_array($pagina, ['aulas','tema-detalhe']) ? 'active' : '' ?>">Temas de Aulas</a>
            <a href="index.php?pagina=tema-novo" class="sidebar__submenu-link <?= $pagina === 'tema-novo' ? 'active' : '' ?>">Novo Tema</a>
            <a href="index.php?pagina=cronograma" class="sidebar__submenu-link <?= $pagina === 'cronograma' ? 'active' : '' ?>">Cronograma</a>
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
              <div class="stat-card__value" id="dash-val-aulas">—</div>
              <div class="stat-card__label">Aulas este Mês</div>
              <span class="trend" id="dash-trend-aulas">
                <svg viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                carregando…
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

            <!-- Aulas do Próximo Domingo -->
            <div class="card" id="card-aulas-domingo">
              <div class="card-header">
                <span class="card-title">Aulas do Próximo Domingo</span>
                <span class="badge badge-primary" id="dash-domingo-data">—</span>
              </div>
              <div class="card-body" style="padding-top:var(--space-2)">
                <div id="dash-domingo-lista">
                  <div style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">Carregando…</div>
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

        <!-- Próximos Compromissos -->
        <div class="card" style="margin-top:var(--space-6)" id="card-proximos-compromissos">
          <div class="card-header">
            <span class="card-title">
              <svg style="width:16px;height:16px;fill:currentColor;vertical-align:middle;margin-right:6px;color:var(--color-primary)" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
              </svg>
              Próximos Compromissos
            </span>
            <a href="index.php?pagina=calendario" class="btn btn-ghost btn-sm">Ver calendário</a>
          </div>
          <div id="dash-proximos-lista" style="padding:var(--space-4) var(--space-6);color:var(--color-text-muted);font-size:var(--text-sm)">
            Carregando…
          </div>
        </div>

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

      <?php elseif ($pagina === 'aulas'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  TEMAS DE AULAS                                -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Temas de Aulas</h1>
            <p class="page-subtitle">Organize os conteúdos por trimestre e turma.</p>
          </div>
          <a href="index.php?pagina=tema-novo" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Novo Tema
          </a>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="padding:var(--space-4) var(--space-6)">
            <div style="display:grid;grid-template-columns:110px 190px 230px auto;gap:var(--space-3);align-items:flex-end">
              <div>
                <label class="form-label" for="temas-ano">Ano</label>
                <input type="number" id="temas-ano" class="form-control" value="<?= date('Y') ?>" min="2000" max="2100">
              </div>
              <div>
                <label class="form-label" for="temas-trimestre">Trimestre</label>
                <select id="temas-trimestre" class="form-control">
                  <option value="0">Todos</option>
                  <option value="1">1º Trimestre</option>
                  <option value="2">2º Trimestre</option>
                  <option value="3">3º Trimestre</option>
                  <option value="4">4º Trimestre</option>
                </select>
              </div>
              <div>
                <label class="form-label" for="temas-turma">Turma</label>
                <select id="temas-turma" class="form-control">
                  <option value="0">Todas</option>
                </select>
              </div>
              <button class="btn btn-secondary" id="btnFiltrarTemas">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
                Filtrar
              </button>
            </div>
          </div>
        </div>

        <div id="temas-alert" style="display:none;margin-bottom:var(--space-4)"></div>

        <!-- Container dos trimestres -->
        <div id="temas-container">
          <div style="text-align:center;padding:var(--space-12);color:var(--color-text-muted)">
            <svg style="width:36px;height:36px;fill:currentColor;margin:0 auto var(--space-3);display:block;opacity:.4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            Carregando…
          </div>
        </div>

        <!-- Modal: Confirmar exclusão de tema -->
        <div class="modal-overlay" id="modalExcluirTema" style="display:none">
          <div class="modal" style="max-width:420px">
            <div class="modal-header">
              <span class="modal-title">Excluir Tema</span>
              <button class="modal-close" onclick="document.getElementById('modalExcluirTema').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
              <p style="margin:0">Tem certeza que deseja excluir o tema <strong id="excluir-tema-nome"></strong>?<br>
                <small class="text-muted">Todas as aulas vinculadas também serão excluídas.</small></p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="document.getElementById('modalExcluirTema').style.display='none'">Cancelar</button>
              <button class="btn btn-danger" id="btnConfirmarExcluirTema">Excluir</button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'tema-novo' || $pagina === 'tema-editar'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  NOVO TEMA / EDITAR TEMA                       -->
        <!-- ══════════════════════════════════════════════ -->
        <?php
          $modoTema  = ($pagina === 'tema-editar') ? 'editar' : 'criar';
          $temaId    = (int)($_GET['id'] ?? 0);
          $temaAno   = (int)($_GET['ano'] ?? date('Y'));
        ?>
        <div class="page-header">
          <div>
            <h1 class="page-title"><?= $modoTema === 'editar' ? 'Editar Tema' : 'Novo Tema' ?></h1>
            <p class="page-subtitle"><?= $modoTema === 'editar' ? 'Altere os dados e salve.' : 'Preencha os dados para criar um novo tema de aula.' ?></p>
          </div>
          <a href="index.php?pagina=aulas" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
            Voltar
          </a>
        </div>

        <div id="tema-form-alert" style="display:none;margin-bottom:var(--space-5)"></div>

        <form id="formTema" novalidate data-modo="<?= $modoTema ?>" data-id="<?= $temaId ?>">
          <div class="card" style="max-width:620px">
            <div class="card-header">
              <span class="card-title">Dados do Tema</span>
            </div>
            <div class="card-body">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">

                <!-- Título -->
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="tema-titulo">Título <span style="color:var(--color-danger)">*</span></label>
                  <input type="text" id="tema-titulo" class="form-control" placeholder="Ex.: A Vida dos Patriarcas" maxlength="200" required autofocus>
                  <span class="form-error" id="tema-titulo-error"></span>
                </div>

                <!-- Trimestre -->
                <div class="form-group">
                  <label class="form-label" for="tema-trimestre">Trimestre <span style="color:var(--color-danger)">*</span></label>
                  <select id="tema-trimestre" class="form-control" required>
                    <option value="">Selecione…</option>
                    <option value="1">1º Trimestre</option>
                    <option value="2">2º Trimestre</option>
                    <option value="3">3º Trimestre</option>
                    <option value="4">4º Trimestre</option>
                  </select>
                  <span class="form-error" id="tema-trimestre-error"></span>
                </div>

                <!-- Ano -->
                <div class="form-group">
                  <label class="form-label" for="tema-ano">Ano <span style="color:var(--color-danger)">*</span></label>
                  <input type="number" id="tema-ano" class="form-control" value="<?= date('Y') ?>" min="2000" max="2100" required>
                  <span class="form-error" id="tema-ano-error"></span>
                </div>

                <!-- Turma -->
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="tema-turma-select">Turma</label>
                  <select id="tema-turma-select" class="form-control">
                    <option value="0">Sem turma específica</option>
                  </select>
                </div>

                <!-- Descrição -->
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="tema-descricao">Descrição</label>
                  <textarea id="tema-descricao" class="form-control" rows="3" placeholder="Resumo do conteúdo do tema…" maxlength="1000"></textarea>
                </div>

              </div>
            </div>
            <div class="card-body" style="border-top:1px solid var(--color-border);display:flex;justify-content:flex-end;gap:var(--space-3)">
              <a href="index.php?pagina=aulas" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" id="btnSalvarTema">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <?= $modoTema === 'editar' ? 'Salvar Alterações' : 'Criar Tema' ?>
              </button>
            </div>
          </div>
        </form>

      <?php elseif ($pagina === 'tema-detalhe'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  DETALHES DO TEMA + AULAS                      -->
        <!-- ══════════════════════════════════════════════ -->
        <?php $temaDetalheId = (int)($_GET['id'] ?? 0); ?>
        <div id="tema-detalhe-wrap">

          <!-- Header dinâmico (preenchido por JS) -->
          <div class="page-header" id="tdh-header">
            <div>
              <h1 class="page-title" id="tdh-titulo">Carregando…</h1>
              <p class="page-subtitle" id="tdh-sub"></p>
            </div>
            <div style="display:flex;gap:var(--space-3)">
              <a href="index.php?pagina=aulas" class="btn btn-secondary">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                Voltar
              </a>
              <button class="btn btn-primary" id="btnNovaAula">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                Nova Aula
              </button>
            </div>
          </div>

          <!-- Info do tema (badges) -->
          <div id="tdh-info" style="margin-bottom:var(--space-6);display:flex;flex-wrap:wrap;gap:var(--space-3)"></div>

          <div id="tdh-alert" style="display:none;margin-bottom:var(--space-4)"></div>

          <!-- Tabela de aulas -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">Aulas do Tema</span>
              <span class="badge badge-primary" id="tdh-total">0</span>
            </div>
            <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
              <table class="table" id="tdh-tabela">
                <thead>
                  <tr>
                    <th style="width:40px">#</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Professor</th>
                    <th>Descrição</th>
                    <th style="text-align:right">Ações</th>
                  </tr>
                </thead>
                <tbody id="tdh-tbody">
                  <tr><td colspan="5" style="text-align:center;padding:var(--space-10);color:var(--color-gray-400)">Carregando…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Modal: Nova / Editar Aula -->
        <div class="modal-overlay" id="modalAula" style="display:none">
          <div class="modal" style="max-width:620px">
            <div class="modal-header">
              <span class="modal-title" id="modalAulaTitulo">Nova Aula</span>
              <button class="modal-close" id="btnFecharModalAula">&times;</button>
            </div>
            <div class="modal-body">
              <div id="modal-aula-alert" style="display:none;margin-bottom:var(--space-4)"></div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="aula-titulo">Título <span style="color:var(--color-danger)">*</span></label>
                  <input type="text" id="aula-titulo" class="form-control" placeholder="Ex.: Aula 1 — Criação" maxlength="200">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="aula-data">Data da Aula</label>
                  <input type="date" id="aula-data" class="form-control">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="aula-professor">Professor</label>
                  <select id="aula-professor" class="form-control">
                    <option value="">— Selecionar professor —</option>
                  </select>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="aula-descricao">Descrição / Conteúdo</label>
                  <textarea id="aula-descricao" class="form-control" rows="3" placeholder="Resumo do conteúdo desta aula…" maxlength="1000"></textarea>
                </div>
              </div>

              <!-- Perguntas de revisão -->
              <div class="perg-section">
                <div class="perg-section-header">
                  <span class="perg-section-title">
                    Perguntas de Revisão
                    <small style="font-weight:400;color:var(--color-text-muted)">(máx. 5)</small>
                  </span>
                  <button type="button" class="btn btn-secondary btn-sm" id="btnAdicionarPergunta">
                    <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                    Adicionar
                  </button>
                </div>
                <div id="aula-perguntas-lista"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-danger btn-sm" id="btnExcluirAula" style="display:none;margin-right:auto">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Excluir
              </button>
              <button class="btn btn-secondary" id="btnCancelarAula">Cancelar</button>
              <button class="btn btn-primary" id="btnSalvarAula">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar
              </button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'cronograma'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CRONOGRAMA DE AULAS                          -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Cronograma de Aulas</h1>
            <p class="page-subtitle">Todas as aulas organizadas por turma e professor.</p>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-4);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 90px">
              <label class="form-label">Ano</label>
              <input type="number" id="cron-ano" class="form-control" value="<?= date('Y') ?>" min="2000" max="2100">
            </div>
            <div class="form-group" style="margin:0;flex:0 0 170px">
              <label class="form-label">Trimestre</label>
              <select id="cron-trimestre" class="form-control">
                <option value="0">Todos</option>
                <option value="1">1º Trimestre</option>
                <option value="2">2º Trimestre</option>
                <option value="3">3º Trimestre</option>
                <option value="4">4º Trimestre</option>
              </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:160px">
              <label class="form-label">Turma</label>
              <select id="cron-turma" class="form-control">
                <option value="">Todas as turmas</option>
              </select>
            </div>
            <button class="btn btn-primary" id="btnFiltrarCron" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Filtrar
            </button>
          </div>
        </div>

        <div id="cron-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="cron-container"></div>

      <?php elseif ($pagina === 'calendario'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CALENDÁRIO                                    -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Calendário</h1>
            <p class="page-subtitle">Gerencie seus compromissos e receba lembretes.</p>
          </div>
          <button class="btn btn-primary" id="btnNovoCompromisso">
            <svg class="icon" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            Novo Compromisso
          </button>
        </div>

        <!-- Banner de aviso (lembretes do dia) -->
        <div id="calAlertBanner" style="display:none;margin-bottom:var(--space-4)"></div>

        <div class="cal-wrapper">

          <!-- ── Painel do calendário ── -->
          <div class="card cal-card">

            <!-- Navegação do mês -->
            <div class="cal-nav">
              <button class="btn btn-ghost btn-sm" id="calPrev" title="Mês anterior">
                <svg style="width:18px;height:18px;fill:currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </button>
              <h2 class="cal-month-title" id="calTitle">—</h2>
              <button class="btn btn-ghost btn-sm" id="calNext" title="Próximo mês">
                <svg style="width:18px;height:18px;fill:currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </button>
              <button class="btn btn-secondary btn-sm" id="calHoje" style="margin-left:var(--space-2)">Hoje</button>
            </div>

            <!-- Cabeçalho dos dias da semana -->
            <div class="cal-weekdays">
              <span>Dom</span><span>Seg</span><span>Ter</span>
              <span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
            </div>

            <!-- Grade de dias -->
            <div class="cal-grid" id="calGrid">
              <!-- preenchida por JS -->
            </div>
          </div>

          <!-- ── Lista lateral de eventos do mês ── -->
          <div class="cal-sidebar-panel">
            <div class="card" style="height:100%">
              <div class="card-header">
                <span class="card-title" id="calListTitle">Compromissos</span>
                <span class="badge badge-primary" id="calListCount">0</span>
              </div>
              <div id="calEventList" style="overflow-y:auto;max-height:520px">
                <div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">
                  <svg style="width:32px;height:32px;fill:currentColor;margin:0 auto var(--space-2);display:block" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                  Nenhum compromisso este mês.
                </div>
              </div>
            </div>
          </div>

        </div><!-- /cal-wrapper -->

        <!-- ════════════ MODAL: NOVO / EDITAR COMPROMISSO ════════════ -->
        <div class="modal-overlay" id="modalCompromisso" style="display:none">
          <div class="modal" style="max-width:520px">
            <div class="modal-header">
              <span class="modal-title" id="modalCompTitulo">Novo Compromisso</span>
              <button class="modal-close" id="btnFecharModalComp">&times;</button>
            </div>
            <div class="modal-body">
              <div id="comp-alert" style="display:none;margin-bottom:var(--space-4)"></div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">

                <!-- Título -->
                <div style="grid-column:1/-1" class="form-group">
                  <label class="form-label" for="comp-titulo">Título <span style="color:var(--color-danger)">*</span></label>
                  <input type="text" id="comp-titulo" class="form-control" placeholder="Ex.: Reunião de Professores" maxlength="200">
                </div>

                <!-- Data -->
                <div class="form-group">
                  <label class="form-label" for="comp-data">Data <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="comp-data" class="form-control">
                </div>

                <!-- Categoria -->
                <div class="form-group">
                  <label class="form-label" for="comp-categoria">Categoria</label>
                  <select id="comp-categoria" class="form-control">
                    <option value="geral">🔵 Geral</option>
                    <option value="aula">🟢 Aula</option>
                    <option value="evento">🟠 Evento</option>
                    <option value="reuniao">🟣 Reunião</option>
                    <option value="urgente">🔴 Urgente</option>
                  </select>
                </div>

                <!-- Hora início -->
                <div class="form-group">
                  <label class="form-label" for="comp-hora-inicio">Horário de início</label>
                  <input type="time" id="comp-hora-inicio" class="form-control">
                </div>

                <!-- Hora fim -->
                <div class="form-group">
                  <label class="form-label" for="comp-hora-fim">Horário de término</label>
                  <input type="time" id="comp-hora-fim" class="form-control">
                </div>

                <!-- Lembrete -->
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="comp-lembrete">Lembrete</label>
                  <select id="comp-lembrete" class="form-control">
                    <option value="0">Sem lembrete</option>
                    <option value="15">15 minutos antes</option>
                    <option value="30" selected>30 minutos antes</option>
                    <option value="60">1 hora antes</option>
                    <option value="120">2 horas antes</option>
                    <option value="1440">1 dia antes</option>
                  </select>
                </div>

                <!-- Descrição -->
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="comp-descricao">Descrição</label>
                  <textarea id="comp-descricao" class="form-control" rows="3" placeholder="Detalhes do compromisso…" maxlength="1000"></textarea>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-danger btn-sm" id="btnExcluirComp" style="display:none;margin-right:auto">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Excluir
              </button>
              <button class="btn btn-secondary" id="btnCancelarComp">Cancelar</button>
              <button class="btn btn-primary" id="btnSalvarComp">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Salvar
              </button>
            </div>
          </div>
        </div>

      <?php elseif ($pagina === 'configuracoes'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CONFIGURAÇÕES                                 -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Configurações</h1>
            <p class="page-subtitle">Personalize a aparência e o comportamento do sistema.</p>
          </div>
        </div>

        <div class="card" style="max-width:600px">
          <div class="card-header">
            <span class="card-title">Aparência</span>
          </div>
          <div class="card-body" style="display:flex;flex-direction:column;gap:var(--space-5)">

            <!-- Modo Noturno -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:var(--space-4)">
              <div>
                <div style="font-weight:600;font-size:var(--text-sm)">Modo Noturno</div>
                <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px">Ativa o tema escuro em toda a interface</div>
              </div>
              <label class="cfg-toggle" title="Modo noturno">
                <input type="checkbox" id="toggleDarkMode">
                <span class="cfg-toggle__track">
                  <span class="cfg-toggle__thumb"></span>
                </span>
              </label>
            </div>

          </div>
        </div>

      <?php else: ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  PÁGINAS GENÉRICAS (em construção)             -->
        <!-- ══════════════════════════════════════════════ -->
        <?php
        $titulos = [
          'aluno-novo'     => 'Cadastrar Aluno',
          'aluno-editar'   => 'Editar Aluno',
          'professor-editar' => 'Editar Professor',
          'aulas'          => 'Temas de Aulas',
          'cronograma'     => 'Cronograma de Aulas',
          'tema-novo'      => 'Novo Tema',
          'tema-editar'    => 'Editar Tema',
          'tema-detalhe'   => 'Detalhes do Tema',
          'aula-nova'      => 'Nova Aula',
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
      aulas: 'Temas de Aulas',
      cronograma: 'Cronograma de Aulas',
      'tema-novo': 'Novo Tema',
      'tema-editar': 'Editar Tema',
      'tema-detalhe': 'Detalhes do Tema',
      'aula-nova': 'Nova Aula',
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
    // TURMAS — carrega select dinâmico em formulários de aluno/professor e módulo aulas
    // ══════════════════════════════════════════════════
    (function loadTurmasSelect() {
      const selects = document.querySelectorAll('select#turma, select#filtro-turma, select#filtro-prof-turma, select#temas-turma, select#tema-turma-select');
      if (!selects.length) return;
      fetch('turmas_crud.php')
        .then(r => r.json())
        .then(data => {
          if (!data.ok) return;
          selects.forEach(sel => {
            const isTemasSel = (sel.id === 'temas-turma' || sel.id === 'tema-turma-select');
            const val = sel.value;
            while (sel.options.length > 1) sel.remove(1);
            data.turmas.forEach(t => {
              const opt = document.createElement('option');
              // Módulo de temas usa id numérico; outros usam nome
              opt.value       = isTemasSel ? t.id : t.nome_turma;
              opt.textContent = t.nome_turma;
              sel.appendChild(opt);
            });
            if (val) sel.value = val;
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

      const valAulas   = document.getElementById('dash-val-aulas');
      const trendAulas = document.getElementById('dash-trend-aulas');
      if (valAulas) {
        fetch('aulas_temas_crud.php?recurso=aulas-stats')
          .then(r => r.json())
          .then(d => {
            if (!d.ok) return;
            valAulas.textContent = d.atual;
            const diff  = d.diff;
            const label = (diff === 0 ? 'igual ao' : (diff > 0 ? '+' + diff + ' vs' : diff + ' vs')) + ' mês anterior';
            trendAulas.className  = 'trend ' + (diff >= 0 ? 'trend-up' : 'trend-down');
            trendAulas.innerHTML  = (diff >= 0 ? svgUp : svgDown) + ' ' + label;
          })
          .catch(() => { valAulas.textContent = '—'; });
      }

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

    // ══════════════════════════════════════════════════
    //  TEMAS DE AULAS — lista de temas (pagina=aulas)
    // ══════════════════════════════════════════════════
    (function() {
      if (!document.getElementById('temas-container')) return;

      const container  = document.getElementById('temas-container');
      const alertEl    = document.getElementById('temas-alert');
      let excluirTemaId = null;

      const TRIM_INFO = {
        1: { label: '1º Trimestre', cor: '#eff6ff', corTexto: '#1d4ed8', corBorda: '#bfdbfe' },
        2: { label: '2º Trimestre', cor: '#f0fdf4', corTexto: '#166534', corBorda: '#bbf7d0' },
        3: { label: '3º Trimestre', cor: '#fffbeb', corTexto: '#92400e', corBorda: '#fde68a' },
        4: { label: '4º Trimestre', cor: '#fdf4ff', corTexto: '#6b21a8', corBorda: '#e9d5ff' },
      };

      // Retorna "DD/MM/AAAA – DD/MM/AAAA" para o trimestre no ano dado
      function trimPeriodo(trimestre, ano) {
        const inicio = [
          [1,  1], // T1 01/Jan
          [1,  4], // T2 01/Abr
          [1,  7], // T3 01/Jul
          [1, 10], // T4 01/Out
        ][trimestre - 1];
        const fim = [
          [31, 3],  // T1 31/Mar
          [30, 6],  // T2 30/Jun
          [30, 9],  // T3 30/Set
          [31, 12], // T4 31/Dez
        ][trimestre - 1];
        const fmt = (d, m, a) => String(d).padStart(2,'0') + '/' + String(m).padStart(2,'0') + '/' + a;
        return fmt(inicio[0], inicio[1], ano) + ' – ' + fmt(fim[0], fim[1], ano);
      }

      function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }
      function showAlert(msg, tipo) {
        alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
        alertEl.style.display = 'block';
        if (tipo !== 'danger') setTimeout(() => alertEl.style.display = 'none', 4000);
      }

      function carregarTemas() {
        const ano       = parseInt(document.getElementById('temas-ano').value) || new Date().getFullYear();
        const trimestre = document.getElementById('temas-trimestre').value;
        const turma_id  = document.getElementById('temas-turma').value;

        container.innerHTML = '<div style="text-align:center;padding:var(--space-10);color:var(--color-text-muted)">Carregando…</div>';

        const p = new URLSearchParams({ recurso: 'temas', ano, trimestre, turma_id });
        fetch('aulas_temas_crud.php?' + p)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) { showAlert(data.msg || 'Erro ao carregar.', 'danger'); container.innerHTML = ''; return; }
            renderTemas(data.temas, parseInt(trimestre));
          })
          .catch(() => showAlert('Falha na comunicação com o servidor.', 'danger'));
      }

      function renderTemas(temas, filtroTrim) {
        const trims = filtroTrim > 0 ? [filtroTrim] : [1, 2, 3, 4];
        const ano   = parseInt(document.getElementById('temas-ano').value) || new Date().getFullYear();
        let html = '';

        trims.forEach(t => {
          const info    = TRIM_INFO[t];
          const lista   = temas.filter(tm => parseInt(tm.trimestre) === t);
          const periodo = trimPeriodo(t, ano);

          html += `<div class="trim-section" style="margin-bottom:var(--space-6)">
            <div class="trim-header" style="background:${info.cor};border:1px solid ${info.corBorda}">
              <div style="display:flex;flex-direction:column;gap:2px">
                <span class="trim-title" style="color:${info.corTexto}">${info.label}</span>
                <span style="font-size:var(--text-xs);color:${info.corTexto};opacity:.75">${periodo}</span>
              </div>
              <span class="badge" style="background:${info.corBorda};color:${info.corTexto}">${lista.length} tema${lista.length !== 1 ? 's' : ''}</span>
              <a href="index.php?pagina=tema-novo" class="btn btn-sm" style="background:${info.corBorda};color:${info.corTexto};border:none;margin-left:auto" onclick="event.stopPropagation();document.getElementById('tema-trimestre') && (document.getElementById('tema-trimestre').value='${t}')">
                <svg style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                Adicionar tema
              </a>
            </div>`;

          if (lista.length === 0) {
            html += `<div class="trim-empty">
              <svg style="width:24px;height:24px;fill:currentColor;opacity:.3;flex-shrink:0" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
              Nenhum tema cadastrado neste trimestre.
            </div>`;
          } else {
            html += '<div class="temas-grid">';
            lista.forEach(tm => {
              html += `<div class="tema-card">
                <div class="tema-card__head">
                  <div>
                    <div class="tema-card__title">${esc(tm.titulo)}</div>
                    ${tm.descricao ? `<div class="tema-card__desc">${esc(tm.descricao)}</div>` : ''}
                  </div>
                </div>
                <div class="tema-card__meta">
                  <span class="badge badge-primary">${esc(tm.nome_turma || 'Sem turma')}</span>
                  <span class="badge" style="background:var(--color-gray-100);color:var(--color-gray-600)">
                    <svg style="width:11px;height:11px;fill:currentColor;margin-right:3px" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>
                    ${tm.total_aulas} aula${tm.total_aulas != 1 ? 's' : ''}
                  </span>
                </div>
                <div class="tema-card__actions">
                  <a href="index.php?pagina=tema-detalhe&id=${tm.id}" class="btn btn-primary btn-sm" style="flex:1">
                    <svg class="icon" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                    Ver Aulas
                  </a>
                  <a href="index.php?pagina=tema-editar&id=${tm.id}" class="btn btn-secondary btn-sm" title="Editar tema">
                    <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                  </a>
                  <button class="btn btn-ghost btn-sm" style="color:var(--color-danger)" title="Excluir tema"
                    onclick="abrirExcluirTema(${tm.id}, '${esc(tm.titulo).replace(/'/g,"\\'")}')">
                    <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                  </button>
                </div>
              </div>`;
            });
            html += '</div>';
          }
          html += '</div>';
        });

        container.innerHTML = html;
      }

      // Exclusão de tema
      window.abrirExcluirTema = function(id, nome) {
        excluirTemaId = id;
        document.getElementById('excluir-tema-nome').textContent = nome;
        document.getElementById('modalExcluirTema').style.display = 'flex';
      };
      document.getElementById('btnConfirmarExcluirTema').addEventListener('click', function() {
        if (!excluirTemaId) return;
        this.disabled = true;
        this.textContent = 'Excluindo…';
        fetch('aulas_temas_crud.php?recurso=tema&id=' + excluirTemaId, { method: 'DELETE' })
          .then(r => r.json())
          .then(d => {
            document.getElementById('modalExcluirTema').style.display = 'none';
            showAlert(d.msg || (d.ok ? 'Excluído.' : 'Erro.'), d.ok ? 'success' : 'danger');
            if (d.ok) carregarTemas();
          })
          .catch(() => showAlert('Falha ao excluir.', 'danger'))
          .finally(() => {
            this.disabled = false;
            this.textContent = 'Excluir';
            excluirTemaId = null;
          });
      });

      document.getElementById('btnFiltrarTemas').addEventListener('click', carregarTemas);
      document.getElementById('temas-ano').addEventListener('keydown', e => { if (e.key === 'Enter') carregarTemas(); });

      carregarTemas();
    })();

    // ══════════════════════════════════════════════════
    //  FORMULÁRIO TEMA (criar / editar)
    // ══════════════════════════════════════════════════
    (function() {
      const form = document.getElementById('formTema');
      if (!form) return;

      const modo   = form.dataset.modo;
      const temaId = parseInt(form.dataset.id || '0');
      const alertEl = document.getElementById('tema-form-alert');

      function showAlert(msg, tipo) {
        alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + msg + '</span></div>';
        alertEl.style.display = 'block';
      }

      // Pré-carrega dados se for edição
      if (modo === 'editar' && temaId > 0) {
        fetch('aulas_temas_crud.php?recurso=tema&id=' + temaId)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) { showAlert(data.msg || 'Tema não encontrado.', 'danger'); return; }
            const t = data.tema;
            document.getElementById('tema-titulo').value    = t.titulo    || '';
            document.getElementById('tema-trimestre').value = t.trimestre || '';
            document.getElementById('tema-ano').value       = t.ano       || new Date().getFullYear();
            document.getElementById('tema-turma-select').value = t.turma_id || '0';
            document.getElementById('tema-descricao').value = t.descricao || '';
          })
          .catch(() => showAlert('Erro ao carregar dados do tema.', 'danger'));
      }

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const titulo    = document.getElementById('tema-titulo').value.trim();
        const trimestre = parseInt(document.getElementById('tema-trimestre').value);
        const ano       = parseInt(document.getElementById('tema-ano').value);
        const turma_id  = parseInt(document.getElementById('tema-turma-select').value) || 0;
        const descricao = document.getElementById('tema-descricao').value.trim();

        // Validação
        let valid = true;
        if (!titulo) {
          document.getElementById('tema-titulo-error').textContent = 'Título é obrigatório.';
          valid = false;
        } else { document.getElementById('tema-titulo-error').textContent = ''; }
        if (!trimestre || trimestre < 1 || trimestre > 4) {
          document.getElementById('tema-trimestre-error').textContent = 'Selecione o trimestre.';
          valid = false;
        } else { document.getElementById('tema-trimestre-error').textContent = ''; }
        if (!valid) return;

        const btn = document.getElementById('btnSalvarTema');
        btn.disabled = true;
        btn.textContent = 'Salvando…';

        const body   = { titulo, trimestre, turma_id, ano, descricao };
        const method = modo === 'editar' ? 'PUT' : 'POST';
        if (modo === 'editar') body.id = temaId;

        fetch('aulas_temas_crud.php', {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
          .then(r => r.json())
          .then(data => {
            if (!data.ok) { showAlert(data.msg || 'Erro ao salvar.', 'danger'); return; }
            showAlert(data.msg || 'Salvo!', 'success');
            setTimeout(() => {
              window.location.href = modo === 'editar'
                ? 'index.php?pagina=tema-detalhe&id=' + temaId
                : (data.id ? 'index.php?pagina=tema-detalhe&id=' + data.id : 'index.php?pagina=aulas');
            }, 900);
          })
          .catch(() => showAlert('Falha na comunicação.', 'danger'))
          .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> ' + (modo === 'editar' ? 'Salvar Alterações' : 'Criar Tema');
          });
      });
    })();

    // ══════════════════════════════════════════════════
    //  TEMA DETALHE — aulas (pagina=tema-detalhe)
    // ══════════════════════════════════════════════════
    (function() {
      const wrap = document.getElementById('tema-detalhe-wrap');
      if (!wrap) return;

      const params   = new URLSearchParams(window.location.search);
      const temaId   = parseInt(params.get('id') || '0');
      const tbodyEl  = document.getElementById('tdh-tbody');
      const totalEl  = document.getElementById('tdh-total');
      const alertEl  = document.getElementById('tdh-alert');
      let editandoAulaId = null;
      let perguntasLocais = [];

      const TRIM_LABELS = { '1':'1º Trimestre','2':'2º Trimestre','3':'3º Trimestre','4':'4º Trimestre' };

      function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }
      function fmtData(s) {
        if (!s) return '—';
        const [y,m,d] = s.split('-');
        return d + '/' + m + '/' + y;
      }
      function showAlert(msg, tipo) {
        alertEl.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
        alertEl.style.display = 'block';
        if (tipo !== 'danger') setTimeout(() => alertEl.style.display = 'none', 4000);
      }
      function showModalAlert(msg, tipo) {
        const el = document.getElementById('modal-aula-alert');
        el.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + esc(msg) + '</span></div>';
        el.style.display = 'block';
      }

      if (!temaId) {
        document.getElementById('tdh-titulo').textContent = 'Tema não encontrado';
        return;
      }

      // Carrega informações do tema
      function carregarTema() {
        fetch('aulas_temas_crud.php?recurso=tema&id=' + temaId)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) return;
            const t = data.tema;
            document.getElementById('tdh-titulo').textContent = t.titulo;
            document.title = t.titulo + ' — Escola Bíblica';
            document.getElementById('pageTitle').textContent  = t.titulo;
            document.getElementById('tdh-sub').textContent   = (t.nome_turma || 'Sem turma') + ' · ' + t.ano;
            const info = document.getElementById('tdh-info');
            info.innerHTML =
              `<span class="badge badge-primary" style="font-size:var(--text-sm);padding:6px 12px">${TRIM_LABELS[t.trimestre] || ''}</span>
               <span class="badge" style="background:var(--color-gray-100);color:var(--color-gray-700);font-size:var(--text-sm);padding:6px 12px">
                 <svg style="width:13px;height:13px;fill:currentColor;margin-right:4px;vertical-align:middle" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                 ${esc(t.nome_turma || 'Sem turma')}
               </span>
               ${t.descricao ? `<span style="font-size:var(--text-sm);color:var(--color-text-muted)">${esc(t.descricao)}</span>` : ''}`;
          });
      }

      // Carrega aulas
      function carregarAulas() {
        tbodyEl.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--color-gray-400)">Carregando…</td></tr>';
        fetch('aulas_temas_crud.php?recurso=aulas&tema_id=' + temaId)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) { showAlert(data.msg || 'Erro.', 'danger'); return; }
            totalEl.textContent = data.total;
            if (!data.aulas.length) {
              tbodyEl.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:var(--space-10);color:var(--color-gray-400)">Nenhuma aula cadastrada ainda. Clique em "Nova Aula" para começar.</td></tr>';
              return;
            }
            tbodyEl.innerHTML = data.aulas.map((a, i) => `
              <tr>
                <td style="text-align:center;color:var(--color-gray-400);font-size:var(--text-xs)">${i+1}</td>
                <td><strong>${esc(a.titulo)}</strong></td>
                <td style="white-space:nowrap">${fmtData(a.data_aula)}</td>
                <td>${esc(a.professor || '—')}</td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--color-text-muted);font-size:var(--text-sm)">${esc(a.descricao || '—')}</td>
                <td style="text-align:right;white-space:nowrap">
                  <button class="btn btn-ghost btn-sm" title="Editar" onclick="editarAula(${a.id})">
                    <svg class="icon" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                  </button>
                </td>
              </tr>`).join('');
          })
          .catch(() => showAlert('Falha na comunicação.', 'danger'));
      }

      // Carrega professores (docente=S) no select
      function carregarProfessores(valorAtual) {
        fetch('alunos_crud.php?docente=S')
          .then(r => r.json())
          .then(data => {
            const sel = document.getElementById('aula-professor');
            sel.innerHTML = '<option value="">— Selecionar professor —</option>';
            (data.alunos || []).forEach(a => {
              const opt = document.createElement('option');
              opt.value = a.nome;
              opt.textContent = a.nome;
              if (valorAtual && a.nome === valorAtual) opt.selected = true;
              sel.appendChild(opt);
            });
          })
          .catch(() => {});
      }

      // Renderiza perguntas locais no modal
      function renderPerguntas() {
        const container  = document.getElementById('aula-perguntas-lista');
        const btnAdd     = document.getElementById('btnAdicionarPergunta');
        if (!container) return;
        if (btnAdd) btnAdd.disabled = perguntasLocais.length >= 5;

        if (perguntasLocais.length === 0) {
          container.innerHTML = '<p class="perg-empty">Nenhuma pergunta adicionada.</p>';
          return;
        }

        container.innerHTML = perguntasLocais.map((p, i) => `
          <div class="perg-row">
            <span class="perg-num">${i + 1}</span>
            <div class="perg-fields">
              <input type="text" class="form-control" placeholder="Pergunta…" maxlength="500"
                     value="${esc(p.pergunta)}" data-pi="${i}" data-pf="pergunta">
              <textarea class="form-control" rows="2" placeholder="Resposta… (opcional)" maxlength="1000"
                        data-pi="${i}" data-pf="resposta">${esc(p.resposta)}</textarea>
            </div>
            <button type="button" class="perg-del" data-pd="${i}" title="Remover pergunta">
              <svg viewBox="0 0 20 20" width="16" height="16"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" fill="currentColor"/></svg>
            </button>
          </div>`).join('');

        container.querySelectorAll('[data-pf]').forEach(el => {
          el.addEventListener('input', e => {
            perguntasLocais[+e.target.dataset.pi][e.target.dataset.pf] = e.target.value;
          });
        });
        container.querySelectorAll('[data-pd]').forEach(btn => {
          btn.addEventListener('click', () => {
            perguntasLocais.splice(+btn.dataset.pd, 1);
            renderPerguntas();
          });
        });
      }

      // Carrega perguntas de uma aula existente
      function carregarPerguntas(aulaId) {
        fetch('aulas_temas_crud.php?recurso=perguntas&aula_id=' + aulaId)
          .then(r => r.json())
          .then(d => {
            perguntasLocais = (d.perguntas || []).map(p => ({
              pergunta: p.pergunta || '',
              resposta: p.resposta || '',
            }));
            renderPerguntas();
          })
          .catch(() => { perguntasLocais = []; renderPerguntas(); });
      }

      // Abrir modal nova aula
      window.abrirNovaAula = function() {
        editandoAulaId = null;
        document.getElementById('modalAulaTitulo').textContent  = 'Nova Aula';
        document.getElementById('btnExcluirAula').style.display = 'none';
        document.getElementById('modal-aula-alert').style.display = 'none';
        document.getElementById('aula-titulo').value     = '';
        document.getElementById('aula-data').value       = '';
        document.getElementById('aula-descricao').value  = '';
        perguntasLocais = [];
        renderPerguntas();
        carregarProfessores('');
        document.getElementById('modalAula').style.display = 'flex';
        document.getElementById('aula-titulo').focus();
      };

      // Abrir modal editar aula
      window.editarAula = function(id) {
        fetch('aulas_temas_crud.php?recurso=aula&id=' + id)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) return;
            const a = data.aula;
            editandoAulaId = a.id;
            document.getElementById('modalAulaTitulo').textContent  = 'Editar Aula';
            document.getElementById('btnExcluirAula').style.display = '';
            document.getElementById('modal-aula-alert').style.display = 'none';
            document.getElementById('aula-titulo').value    = a.titulo    || '';
            document.getElementById('aula-data').value      = a.data_aula || '';
            document.getElementById('aula-descricao').value = a.descricao  || '';
            carregarProfessores(a.professor || '');
            perguntasLocais = [];
            renderPerguntas();
            carregarPerguntas(a.id);
            document.getElementById('modalAula').style.display = 'flex';
          });
      };

      // Salvar aula
      document.getElementById('btnSalvarAula').addEventListener('click', function() {
        const titulo = document.getElementById('aula-titulo').value.trim();
        if (!titulo) { showModalAlert('O título é obrigatório.', 'danger'); return; }

        const body = {
          tema_id:   temaId,
          titulo,
          data_aula:  document.getElementById('aula-data').value,
          professor:  document.getElementById('aula-professor').value,
          descricao:  document.getElementById('aula-descricao').value.trim(),
          perguntas:  perguntasLocais.filter(p => p.pergunta.trim() !== ''),
        };
        const method = editandoAulaId ? 'PUT' : 'POST';
        if (editandoAulaId) body.id = editandoAulaId;

        this.disabled = true;
        this.textContent = 'Salvando…';
        fetch('aulas_temas_crud.php?recurso=aula', {
          method,
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify(body),
        })
          .then(r => r.json())
          .then(d => {
            if (!d.ok) { showModalAlert(d.msg || 'Erro.', 'danger'); return; }
            document.getElementById('modalAula').style.display = 'none';
            showAlert(d.msg || 'Salvo!', 'success');
            carregarAulas();
          })
          .catch(() => showModalAlert('Falha na comunicação.', 'danger'))
          .finally(() => {
            this.disabled = false;
            this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar';
          });
      });

      // Excluir aula
      document.getElementById('btnExcluirAula').addEventListener('click', function() {
        if (!editandoAulaId) return;
        if (!confirm('Excluir esta aula?')) return;
        fetch('aulas_temas_crud.php?recurso=aula&id=' + editandoAulaId, { method: 'DELETE' })
          .then(r => r.json())
          .then(d => {
            if (!d.ok) { showModalAlert(d.msg || 'Erro.', 'danger'); return; }
            document.getElementById('modalAula').style.display = 'none';
            showAlert(d.msg || 'Excluída!', 'success');
            carregarAulas();
          })
          .catch(() => showModalAlert('Falha ao excluir.', 'danger'));
      });

      // Fechar modal
      const fecharModal = () => {
        document.getElementById('modalAula').style.display = 'none';
        editandoAulaId = null;
      };
      document.getElementById('btnFecharModalAula').addEventListener('click', fecharModal);
      document.getElementById('btnCancelarAula').addEventListener('click', fecharModal);
      document.getElementById('modalAula').addEventListener('click', e => { if (e.target === document.getElementById('modalAula')) fecharModal(); });

      document.getElementById('btnNovaAula').addEventListener('click', abrirNovaAula);

      document.getElementById('btnAdicionarPergunta').addEventListener('click', function() {
        if (perguntasLocais.length >= 5) return;
        perguntasLocais.push({ pergunta: '', resposta: '' });
        renderPerguntas();
      });

      carregarTema();
      carregarAulas();
    })();

    // ══════════════════════════════════════════════════
    //  CRONOGRAMA DE AULAS
    // ══════════════════════════════════════════════════
    (function() {
      const cronContainer = document.getElementById('cron-container');
      if (!cronContainer) return;

      const alertEl = document.getElementById('cron-alert');

      const TRIM_INFO = {
        '1': { label: '1º Trimestre', cor: '#1d4ed8', bg: '#eff6ff', borda: '#bfdbfe' },
        '2': { label: '2º Trimestre', cor: '#166534', bg: '#f0fdf4', borda: '#bbf7d0' },
        '3': { label: '3º Trimestre', cor: '#92400e', bg: '#fffbeb', borda: '#fde68a' },
        '4': { label: '4º Trimestre', cor: '#6b21a8', bg: '#fdf4ff', borda: '#e9d5ff' },
      };
      const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

      function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      }
      function fmtData(d) {
        if (!d) return '—';
        const [y,m,day] = d.split('-');
        return day + '/' + m + '/' + y;
      }
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

        cronContainer.innerHTML = '<div style="text-align:center;padding:var(--space-10);color:var(--color-text-muted)">Carregando…</div>';

        const p = new URLSearchParams({ recurso: 'cronograma', ano, trimestre, turma_id });
        fetch('aulas_temas_crud.php?' + p)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) { showAlert(data.msg || 'Erro ao carregar.', 'danger'); cronContainer.innerHTML = ''; return; }
            renderCronograma(data.turmas);
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

        let html = '';
        turmas.forEach(turma => {
          const aulas = turma.aulas;
          html += `<div class="cron-turma-block">
            <div class="cron-turma-header">
              <svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
              <span>${esc(turma.nome_turma)}</span>
              <span class="badge" style="background:rgba(255,255,255,.25);color:inherit;margin-left:auto">${aulas.length} aula${aulas.length !== 1 ? 's' : ''}</span>
            </div>
            <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width:110px">Data</th>
                    <th>Aula</th>
                    <th>Tema</th>
                    <th>Professor</th>
                    <th style="width:130px">Trimestre</th>
                  </tr>
                </thead>
                <tbody>`;

          aulas.forEach(a => {
            const trim = TRIM_INFO[a.trimestre] || TRIM_INFO['1'];
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
              <td><span class="badge" style="background:${trim.bg};color:${trim.cor};border:1px solid ${trim.borda}">${trim.label}</span></td>
            </tr>`;
          });

          html += `</tbody></table></div></div>`;
        });

        cronContainer.innerHTML = html;
      }

      document.getElementById('btnFiltrarCron').addEventListener('click', carregarCronograma);
      document.getElementById('cron-ano').addEventListener('keydown', e => { if (e.key === 'Enter') carregarCronograma(); });

      carregarCronograma();
    })();

    // ══════════════════════════════════════════════════
    //  CALENDÁRIO DE COMPROMISSOS
    // ══════════════════════════════════════════════════
    (function() {
      if (!document.getElementById('calGrid')) return;

      const MESES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                     'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
      const CAT_COLOR = {
        geral:   { bg: '#eff6ff', text: '#1d4ed8', dot: '#2563eb' },
        aula:    { bg: '#f0fdf4', text: '#166534', dot: '#16a34a' },
        evento:  { bg: '#fff7ed', text: '#c2410c', dot: '#f97316' },
        reuniao: { bg: '#faf5ff', text: '#6d28d9', dot: '#7c3aed' },
        urgente: { bg: '#fef2f2', text: '#991b1b', dot: '#dc2626' },
      };

      let viewAno  = new Date().getFullYear();
      let viewMes  = new Date().getMonth() + 1; // 1-12
      let todayStr = new Date().toISOString().split('T')[0];
      let eventosCache = {};    // chave: 'YYYY-MM'
      let editandoId   = null;

      // ── Utilitários ────────────────────────────────
      function cacheKey(a, m) { return a + '-' + String(m).padStart(2,'0'); }
      function fmtHora(t) {
        if (!t) return '';
        const parts = t.split(':');
        return parts[0] + ':' + parts[1];
      }
      function escH(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }
      function showCompAlert(msg, tipo) {
        const el = document.getElementById('comp-alert');
        el.innerHTML = '<div class="alert alert-' + tipo + '"><span>' + escH(msg) + '</span></div>';
        el.style.display = 'block';
        if (tipo !== 'danger') setTimeout(() => el.style.display = 'none', 3000);
      }
      function showBanner(msg, tipo) {
        const el = document.getElementById('calAlertBanner');
        el.innerHTML = '<div class="alert alert-' + tipo + '" style="display:flex;gap:var(--space-3);align-items:flex-start"><svg style="width:18px;height:18px;fill:currentColor;flex-shrink:0;margin-top:1px" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg><span>' + msg + '</span></div>';
        el.style.display = 'block';
      }

      const TRIMESTRE_LABEL = ['', 'Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Trimestre 4'];
      function trimestre(mes) { return Math.ceil(mes / 3); }

      // ── Renderiza o calendário ──────────────────────
      function renderCal(eventos) {
        const grid  = document.getElementById('calGrid');
        const title = document.getElementById('calTitle');
        const trim  = trimestre(viewMes);
        title.innerHTML = escH(MESES[viewMes - 1] + ' ' + viewAno)
          + ' <span style="font-size:var(--text-sm);font-weight:500;color:var(--color-text-muted);background:var(--color-gray-100);padding:3px 10px;border-radius:var(--radius-full);vertical-align:middle;margin-left:8px">'
          + escH(TRIMESTRE_LABEL[trim]) + '</span>';

        // Primeiro dia da semana (0=Dom … 6=Sáb) e total de dias
        const primeiroDia = new Date(viewAno, viewMes - 1, 1).getDay();
        const totalDias   = new Date(viewAno, viewMes, 0).getDate();

        // Mapeia eventos por dia
        const evPorDia = {};
        (eventos || []).forEach(ev => {
          const d = ev.data_evento; // 'YYYY-MM-DD'
          if (!evPorDia[d]) evPorDia[d] = [];
          evPorDia[d].push(ev);
        });

        let html = '';
        // Células vazias antes do dia 1
        for (let i = 0; i < primeiroDia; i++) {
          html += '<div class="cal-cell cal-cell--empty"></div>';
        }
        // Dias do mês
        for (let d = 1; d <= totalDias; d++) {
          const dateStr = viewAno + '-' + String(viewMes).padStart(2,'0') + '-' + String(d).padStart(2,'0');
          const isToday = dateStr === todayStr;
          const dayEvs  = evPorDia[dateStr] || [];
          const pills   = dayEvs.slice(0, 3).map(ev => {
            const c = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
            const hora = ev.hora_inicio ? ' · ' + fmtHora(ev.hora_inicio) : '';
            return `<div class="cal-pill" style="background:${c.bg};color:${c.text}" data-ev-id="${ev.id}" title="${escH(ev.titulo)}">${escH(ev.titulo.length > 14 ? ev.titulo.slice(0,13)+'…' : ev.titulo)}${hora}</div>`;
          }).join('');
          const moreTag = dayEvs.length > 3 ? `<div class="cal-pill cal-pill--more">+${dayEvs.length - 3} mais</div>` : '';

          html += `<div class="cal-cell${isToday ? ' cal-cell--today' : ''}" data-date="${dateStr}">
            <span class="cal-day-num">${d}</span>
            <div class="cal-pills">${pills}${moreTag}</div>
          </div>`;
        }
        // Completar última linha
        const total = primeiroDia + totalDias;
        const resto = total % 7;
        if (resto !== 0) {
          for (let i = 0; i < 7 - resto; i++) {
            html += '<div class="cal-cell cal-cell--empty"></div>';
          }
        }
        grid.innerHTML = html;

        // Clique no dia (área vazia) → abrir modal no modo criar
        grid.querySelectorAll('.cal-cell:not(.cal-cell--empty)').forEach(cell => {
          cell.addEventListener('click', function(e) {
            if (e.target.closest('.cal-pill[data-ev-id]')) return;
            abrirModalCriar(this.dataset.date);
          });
        });

        // Clique em uma pílula de evento → abrir modal no modo editar
        grid.querySelectorAll('.cal-pill[data-ev-id]').forEach(pill => {
          pill.addEventListener('click', function(e) {
            e.stopPropagation();
            abrirModalEditar(this.dataset.evId);
          });
        });
      }

      // ── Lista lateral ───────────────────────────────
      function renderLista(eventos) {
        const el    = document.getElementById('calEventList');
        const count = document.getElementById('calListCount');
        const title = document.getElementById('calListTitle');
        title.textContent = 'Compromissos — ' + MESES[viewMes - 1] + ' (' + TRIMESTRE_LABEL[trimestre(viewMes)] + ')';
        count.textContent = (eventos || []).length;

        if (!eventos || !eventos.length) {
          el.innerHTML = '<div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)"><svg style="width:28px;height:28px;fill:currentColor;margin:0 auto var(--space-2);display:block" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>Nenhum compromisso.</div>';
          return;
        }

        el.innerHTML = eventos.map(ev => {
          const c = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
          const [, mes, dia] = (ev.data_evento || '').split('-');
          const horaStr = ev.hora_inicio ? fmtHora(ev.hora_inicio) : '';
          return `<div class="cal-list-item" style="cursor:pointer" data-ev-id="${ev.id}">
            <div class="cal-list-dot" style="background:${c.dot}"></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-gray-800);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.titulo)}</div>
              <div style="font-size:var(--text-xs);color:var(--color-text-muted)">${dia}/${mes}${horaStr ? ' · ' + horaStr : ''}</div>
              ${ev.descricao ? `<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.descricao)}</div>` : ''}
            </div>
            <span class="badge" style="background:${c.bg};color:${c.text};flex-shrink:0">${escH(ev.categoria)}</span>
          </div>`;
        }).join('');

        el.querySelectorAll('.cal-list-item').forEach(item => {
          item.addEventListener('click', () => abrirModalEditar(item.dataset.evId));
        });
      }

      // ── Carrega eventos ─────────────────────────────
      function carregarMes(ano, mes) {
        const key = cacheKey(ano, mes);
        if (eventosCache[key]) {
          renderCal(eventosCache[key]);
          renderLista(eventosCache[key]);
          return;
        }
        fetch('calendario_crud.php?ano=' + ano + '&mes=' + mes)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) return;
            eventosCache[key] = data.eventos;
            renderCal(data.eventos);
            renderLista(data.eventos);
          })
          .catch(() => {});
      }

      function invalidarCache() {
        eventosCache = {};
      }

      // ── Modal ───────────────────────────────────────
      function abrirModal() {
        document.getElementById('comp-alert').style.display = 'none';
        document.getElementById('modalCompromisso').style.display = 'flex';
      }
      function fecharModal() {
        document.getElementById('modalCompromisso').style.display = 'none';
        editandoId = null;
      }

      function abrirModalCriar(dateStr) {
        editandoId = null;
        document.getElementById('modalCompTitulo').textContent = 'Novo Compromisso';
        document.getElementById('btnExcluirComp').style.display = 'none';
        document.getElementById('comp-titulo').value      = '';
        document.getElementById('comp-data').value        = dateStr || todayStr;
        document.getElementById('comp-hora-inicio').value = '';
        document.getElementById('comp-hora-fim').value    = '';
        document.getElementById('comp-categoria').value   = 'geral';
        document.getElementById('comp-lembrete').value    = '30';
        document.getElementById('comp-descricao').value   = '';
        abrirModal();
        document.getElementById('comp-titulo').focus();
      }

      function abrirModalEditar(id) {
        fetch('calendario_crud.php?id=' + id)
          .then(r => r.json())
          .then(data => {
            if (!data.ok) return;
            const ev = data.evento;
            editandoId = ev.id;
            document.getElementById('modalCompTitulo').textContent = 'Editar Compromisso';
            document.getElementById('btnExcluirComp').style.display = '';
            document.getElementById('comp-titulo').value      = ev.titulo;
            document.getElementById('comp-data').value        = ev.data_evento;
            document.getElementById('comp-hora-inicio').value = ev.hora_inicio ? ev.hora_inicio.slice(0,5) : '';
            document.getElementById('comp-hora-fim').value    = ev.hora_fim    ? ev.hora_fim.slice(0,5)    : '';
            document.getElementById('comp-categoria').value   = ev.categoria   || 'geral';
            document.getElementById('comp-lembrete').value    = ev.lembrete_minutos || '30';
            document.getElementById('comp-descricao').value   = ev.descricao   || '';
            abrirModal();
          });
      }

      // ── Salvar ──────────────────────────────────────
      document.getElementById('btnSalvarComp').addEventListener('click', function() {
        const titulo = document.getElementById('comp-titulo').value.trim();
        const data   = document.getElementById('comp-data').value;
        if (!titulo) { showCompAlert('O título é obrigatório.', 'danger'); return; }
        if (!data)   { showCompAlert('A data é obrigatória.', 'danger'); return; }

        const body = {
          titulo,
          descricao:       document.getElementById('comp-descricao').value.trim(),
          data_evento:     data,
          hora_inicio:     document.getElementById('comp-hora-inicio').value,
          hora_fim:        document.getElementById('comp-hora-fim').value,
          categoria:       document.getElementById('comp-categoria').value,
          lembrete_minutos: parseInt(document.getElementById('comp-lembrete').value) || 0,
        };

        const method = editandoId ? 'PUT' : 'POST';
        if (editandoId) body.id = editandoId;

        this.disabled = true;
        this.textContent = 'Salvando…';

        fetch('calendario_crud.php', {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
          .then(r => r.json())
          .then(d => {
            if (!d.ok) { showCompAlert(d.msg || 'Erro ao salvar.', 'danger'); return; }
            showCompAlert(d.msg || 'Salvo!', 'success');
            invalidarCache();
            carregarMes(viewAno, viewMes);
            setTimeout(fecharModal, 900);
            agendarNotificacoes();
          })
          .catch(() => showCompAlert('Falha na comunicação.', 'danger'))
          .finally(() => {
            this.disabled = false;
            this.innerHTML = '<svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Salvar';
          });
      });

      // ── Excluir ─────────────────────────────────────
      document.getElementById('btnExcluirComp').addEventListener('click', function() {
        if (!editandoId) return;
        if (!confirm('Excluir este compromisso?')) return;
        fetch('calendario_crud.php?id=' + editandoId, { method: 'DELETE' })
          .then(r => r.json())
          .then(d => {
            if (!d.ok) { showCompAlert(d.msg || 'Erro ao excluir.', 'danger'); return; }
            fecharModal();
            invalidarCache();
            carregarMes(viewAno, viewMes);
            agendarNotificacoes();
          })
          .catch(() => showCompAlert('Falha ao excluir.', 'danger'));
      });

      // ── Fechar modal ────────────────────────────────
      document.getElementById('btnFecharModalComp').addEventListener('click', fecharModal);
      document.getElementById('btnCancelarComp').addEventListener('click', fecharModal);
      document.getElementById('modalCompromisso').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
      });

      // ── Botão "Novo Compromisso" ────────────────────
      document.getElementById('btnNovoCompromisso').addEventListener('click', () => abrirModalCriar(todayStr));

      // ── Navegação mês ───────────────────────────────
      document.getElementById('calPrev').addEventListener('click', () => {
        viewMes--;
        if (viewMes < 1) { viewMes = 12; viewAno--; }
        carregarMes(viewAno, viewMes);
      });
      document.getElementById('calNext').addEventListener('click', () => {
        viewMes++;
        if (viewMes > 12) { viewMes = 1; viewAno++; }
        carregarMes(viewAno, viewMes);
      });
      document.getElementById('calHoje').addEventListener('click', () => {
        const now = new Date();
        viewAno = now.getFullYear();
        viewMes = now.getMonth() + 1;
        carregarMes(viewAno, viewMes);
      });

      // ══════════════════════════════════════════════
      //  SISTEMA DE NOTIFICAÇÕES / LEMBRETES
      // ══════════════════════════════════════════════
      const notifTimers = [];

      function agendarNotificacoes() {
        // Cancela timers existentes
        notifTimers.forEach(t => clearTimeout(t));
        notifTimers.length = 0;

        fetch('calendario_crud.php?proximos=2')
          .then(r => r.json())
          .then(data => {
            if (!data.ok) return;
            const agora = new Date();
            const hoje  = agora.toISOString().split('T')[0];
            const eventosHoje = [];

            data.eventos.forEach(ev => {
              if (!ev.lembrete_minutos || ev.lembrete_minutos <= 0) return;

              const hora = ev.hora_inicio ? ev.hora_inicio.slice(0,5) : '00:00';
              const dtEvento = new Date(ev.data_evento + 'T' + hora + ':00');
              const dtLembrete = new Date(dtEvento.getTime() - ev.lembrete_minutos * 60000);
              const msAte = dtLembrete.getTime() - agora.getTime();

              if (ev.data_evento === hoje) eventosHoje.push(ev);

              if (msAte > 0 && msAte < 24 * 60 * 60 * 1000) {
                const t = setTimeout(() => {
                  dispararNotificacao(ev);
                }, msAte);
                notifTimers.push(t);
              }
            });

            // Banner visual para eventos de hoje
            if (eventosHoje.length > 0) {
              const lista = eventosHoje.map(ev => {
                const h = ev.hora_inicio ? ' às ' + fmtHora(ev.hora_inicio) : '';
                return '<strong>' + escH(ev.titulo) + '</strong>' + h;
              }).join(' · ');
              showBanner('Compromissos de hoje: ' + lista, 'info');
            }
          })
          .catch(() => {});
      }

      function dispararNotificacao(ev) {
        const hora = ev.hora_inicio ? ' às ' + fmtHora(ev.hora_inicio) : '';
        // Toast visual
        mostrarToast(ev.titulo + hora, ev.categoria);
        // Browser Notification
        if ('Notification' in window && Notification.permission === 'granted') {
          new Notification('📅 Lembrete — Escola Bíblica', {
            body: ev.titulo + hora + (ev.descricao ? '\n' + ev.descricao : ''),
            icon: 'uploads/fotos/icon.png',
            tag:  'comp-' + ev.id,
          });
        }
      }

      function mostrarToast(msg, categoria) {
        const c = CAT_COLOR[categoria] || CAT_COLOR.geral;
        const toast = document.createElement('div');
        toast.style.cssText = `
          position:fixed;bottom:var(--space-6);right:var(--space-6);
          background:${c.bg};color:${c.text};
          border:1px solid ${c.dot};border-radius:var(--radius-md);
          padding:var(--space-3) var(--space-5);
          box-shadow:var(--shadow-lg);
          font-size:var(--text-sm);font-weight:500;
          z-index:9999;display:flex;align-items:center;gap:var(--space-2);
          animation:slideInToast .3s ease;
          max-width:320px;
        `;
        toast.innerHTML = `<svg style="width:16px;height:16px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
          <span>🔔 Lembrete: <strong>${escH(msg)}</strong></span>`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .4s'; setTimeout(() => toast.remove(), 400); }, 5000);
      }

      // Solicita permissão de notificação ao abrir o calendário
      if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
      }

      // Estilo da animação do toast
      if (!document.getElementById('toastStyle')) {
        const s = document.createElement('style');
        s.id = 'toastStyle';
        s.textContent = '@keyframes slideInToast { from { transform:translateY(20px);opacity:0; } to { transform:none;opacity:1; } }';
        document.head.appendChild(s);
      }

      // ── Inicialização ───────────────────────────────
      carregarMes(viewAno, viewMes);
      agendarNotificacoes();
    })();

    // ══════════════════════════════════════════════════
    //  DASHBOARD — Aulas do Próximo Domingo
    // ══════════════════════════════════════════════════
    (function() {
      const lista = document.getElementById('dash-domingo-lista');
      if (!lista) return;

      // Calcula a data do próximo domingo (ou hoje se já for domingo)
      function proximoDomingo() {
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        const diasAte = (7 - hoje.getDay()) % 7; // 0 = hoje é domingo
        const dom = new Date(hoje);
        dom.setDate(hoje.getDate() + diasAte);
        return dom;
      }

      function fmtDateBR(d) {
        return String(d.getDate()).padStart(2,'0') + '/' +
               String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
      }
      function fmtDateISO(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth()+1).padStart(2,'0') + '-' +
               String(d.getDate()).padStart(2,'0');
      }
      function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      }

      const TRIM_COR = { '1':'#1d4ed8','2':'#166534','3':'#92400e','4':'#6b21a8' };
      const TRIM_BG  = { '1':'#eff6ff','2':'#f0fdf4','3':'#fffbeb','4':'#fdf4ff' };

      const domingo = proximoDomingo();
      const eHoje   = domingo.getDay() === new Date().getDay() && domingo.toDateString() === new Date().toDateString();

      // Atualiza badge
      const badge = document.getElementById('dash-domingo-data');
      if (badge) badge.textContent = (eHoje ? 'Hoje · ' : '') + fmtDateBR(domingo);

      fetch('aulas_temas_crud.php?recurso=aulas-data&data=' + fmtDateISO(domingo))
        .then(r => r.json())
        .then(data => {
          if (!data.ok) { lista.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Erro ao carregar aulas.</div>'; return; }
          if (!data.aulas.length) {
            lista.innerHTML = '<div style="text-align:center;padding:var(--space-8);color:var(--color-text-muted)">' +
              '<svg style="width:28px;height:28px;fill:currentColor;display:block;margin:0 auto var(--space-2)" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg>' +
              'Nenhuma aula cadastrada para este domingo.</div>';
            return;
          }
          lista.innerHTML = data.aulas.map(a => {
            const cor  = TRIM_COR[a.trimestre] || 'var(--color-primary)';
            const corBg = TRIM_BG[a.trimestre] || 'var(--color-primary-light)';
            return `<div style="display:flex;gap:var(--space-3);padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);align-items:flex-start">
              <span style="width:6px;height:6px;border-radius:50%;background:${cor};flex-shrink:0;margin-top:6px"></span>
              <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:var(--text-sm);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(a.titulo)}</div>
                <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px">
                  ${a.nome_turma ? `<span style="margin-right:6px">📚 ${esc(a.nome_turma)}</span>` : ''}
                  ${a.professor  ? `<span>🎓 ${esc(a.professor)}</span>` : ''}
                </div>
                <div style="margin-top:4px">
                  <span class="badge" style="background:${corBg};color:${cor};font-size:10px">${esc(a.tema_titulo)}</span>
                </div>
              </div>
            </div>`;
          }).join('');
        })
        .catch(() => { lista.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Falha na comunicação.</div>'; });
    })();

    // ══════════════════════════════════════════════════
    //  DASHBOARD — Próximos Compromissos
    // ══════════════════════════════════════════════════
    (function() {
      const container = document.getElementById('dash-proximos-lista');
      if (!container) return;

      const CAT_COLOR = {
        geral:   { bg: '#eff6ff', text: '#1d4ed8', dot: '#2563eb' },
        aula:    { bg: '#f0fdf4', text: '#166534', dot: '#16a34a' },
        evento:  { bg: '#fff7ed', text: '#c2410c', dot: '#f97316' },
        reuniao: { bg: '#faf5ff', text: '#6d28d9', dot: '#7c3aed' },
        urgente: { bg: '#fef2f2', text: '#991b1b', dot: '#dc2626' },
      };

      const CAT_LABEL = {
        geral: 'Geral', aula: 'Aula', evento: 'Evento', reuniao: 'Reunião', urgente: 'Urgente'
      };

      function escH(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }
      function fmtHora(t) {
        if (!t) return '';
        const p = t.split(':');
        return p[0] + ':' + p[1];
      }
      function fmtDataBR(s) {
        if (!s) return '';
        const [y, m, d] = s.split('-');
        return d + '/' + m + '/' + y;
      }
      function diasAte(dateStr) {
        const hoje = new Date(); hoje.setHours(0,0,0,0);
        const ev   = new Date(dateStr + 'T00:00:00');
        const diff = Math.round((ev - hoje) / 86400000);
        if (diff === 0) return '<span style="color:var(--color-primary);font-weight:600">Hoje</span>';
        if (diff === 1) return '<span style="color:var(--color-warning);font-weight:600">Amanhã</span>';
        return 'em ' + diff + ' dias';
      }

      fetch('calendario_crud.php?proximos=30')
        .then(r => r.json())
        .then(data => {
          if (!data.ok || !data.eventos.length) {
            container.innerHTML = '<div style="padding:var(--space-4) 0;display:flex;align-items:center;gap:var(--space-3);color:var(--color-text-muted)">'
              + '<svg style="width:20px;height:20px;fill:currentColor;flex-shrink:0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>'
              + 'Nenhum compromisso nos próximos 30 dias.</div>';
            return;
          }

          container.innerHTML = data.eventos.map(ev => {
            const c    = CAT_COLOR[ev.categoria] || CAT_COLOR.geral;
            const hora = ev.hora_inicio ? ' · ' + fmtHora(ev.hora_inicio) : '';
            const dur  = (ev.hora_inicio && ev.hora_fim) ? ' – ' + fmtHora(ev.hora_fim) : '';
            return `<div class="dash-compromisso-item">
              <div class="dash-comp-dot" style="background:${c.dot}"></div>
              <div style="flex:1;min-width:0">
                <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-gray-800)">${escH(ev.titulo)}</div>
                <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:1px">
                  ${fmtDataBR(ev.data_evento)}${hora}${dur}
                </div>
                ${ev.descricao ? `<div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(ev.descricao)}</div>` : ''}
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0">
                <span class="badge" style="background:${c.bg};color:${c.text}">${CAT_LABEL[ev.categoria] || ev.categoria}</span>
                <span style="font-size:var(--text-xs);color:var(--color-text-muted)">${diasAte(ev.data_evento)}</span>
              </div>
            </div>`;
          }).join('');
        })
        .catch(() => {
          container.innerHTML = '<div style="color:var(--color-danger);font-size:var(--text-sm)">Erro ao carregar compromissos.</div>';
        });
    })();

    // ══════════════════════════════════════════════════
    //  CONFIGURAÇÕES — Modo Noturno
    // ══════════════════════════════════════════════════
    (function() {
      const toggle = document.getElementById('toggleDarkMode');
      if (!toggle) return;

      const html = document.documentElement;

      // Sincroniza estado inicial do toggle com o que já foi aplicado
      toggle.checked = html.getAttribute('data-theme') === 'dark';

      toggle.addEventListener('change', function() {
        if (this.checked) {
          html.setAttribute('data-theme', 'dark');
          localStorage.setItem('escola-theme', 'dark');
        } else {
          html.removeAttribute('data-theme');
          localStorage.setItem('escola-theme', 'light');
        }
      });
    })();

  </script>

</body>

</html>