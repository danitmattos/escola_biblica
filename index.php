<?php
session_start();
// Protege a página — redireciona para login se não autenticado
if (!isset($_SESSION['usuario'])) {
  header('Location: login.php');
  exit();
}

// Gera CSRF token para todas as requisições do front-end
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$usuario = htmlspecialchars($_SESSION['usuario'] ?? 'Administrador', ENT_QUOTES, 'UTF-8');
$usuario_email = htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8');
$usuario_foto  = htmlspecialchars($_SESSION['usuario_foto']  ?? '', ENT_QUOTES, 'UTF-8');
$usuario_id    = (int)($_SESSION['usuario_id'] ?? 0);
$pagina  = isset($_GET['pagina']) ? htmlspecialchars($_GET['pagina'], ENT_QUOTES, 'UTF-8') : 'dashboard';
?>
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
  <title>Escola Bíblica — Sistema de Gestão</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>" />
  <script src="libs/helpers.js?v=<?php echo filemtime('libs/helpers.js'); ?>"></script>
  <!-- Aplica o tema salvo antes de renderizar (evita flash) -->
  <script>
    (function(){
      var t = localStorage.getItem('escola-theme');
      if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
    })();
  </script>
  <script>
    // Injeta CSRF token automaticamente em todos os fetch() que modificam dados
    (function(){
      var _fetch = window.fetch;
      var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      window.fetch = function(url, opts) {
        opts = opts || {};
        var method = (opts.method || 'GET').toUpperCase();
        if (method !== 'GET' && method !== 'HEAD') {
          opts.headers = opts.headers || {};
          // Se for Headers object, converte para plain object
          if (opts.headers instanceof Headers) {
            var h = {};
            opts.headers.forEach(function(v, k) { h[k] = v; });
            opts.headers = h;
          }
          if (!opts.headers['X-CSRF-Token']) {
            opts.headers['X-CSRF-Token'] = csrfToken;
          }
        }
        return _fetch.call(this, url, opts);
      };
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
      <img src="uploads/logo.png" alt="Logo" style="width:36px;height:36px;object-fit:contain;flex-shrink:0">
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
      <a href="index.php?pagina=biblia" class="sidebar__link <?= $pagina === 'biblia' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v10H4V6zm2 2v6h2V8H6zm4 0v6h2V8h-2z"/></svg>
        Bíblia
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
      <div class="sidebar__group <?= in_array($pagina, ['aulas', 'tema-novo', 'tema-editar', 'tema-detalhe', 'aula-nova', 'frequencia', 'cronograma', 'calendario', 'aula-pratica']) ? 'open' : '' ?>">
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
            <a href="index.php?pagina=aula-pratica" class="sidebar__submenu-link <?= $pagina === 'aula-pratica' ? 'active' : '' ?>">Aula na Prática</a>
          </div>
        </div>
      </div>

      <!-- ── RELATÓRIOS ── -->
      <div class="sidebar__section-title">Análise</div>

      <div class="sidebar__group <?= in_array($pagina, ['rel-geral', 'rel-turma', 'rel-aluno', 'rel-risco']) ? 'open' : '' ?>">
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
            <a href="index.php?pagina=rel-risco" class="sidebar__submenu-link <?= $pagina === 'rel-risco' ? 'active' : '' ?>">Alunos em Risco</a>
          </div>
        </div>
      </div>

      <a href="index.php?pagina=certificados" class="sidebar__link <?= $pagina === 'certificados' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        Certificados
      </a>

      <a href="index.php?pagina=vendas" class="sidebar__link <?= $pagina === 'vendas' ? 'active' : '' ?>">
        <svg class="icon" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
        </svg>
        Vendas
      </a>

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
      <div class="notif-wrap" id="notifWrap">
        <button class="btn btn-ghost btn-sm" id="notifBtn" style="position:relative" title="Próximas aulas">
          <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 20 20">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
          </svg>
          <span class="notif-badge" id="notifBadge" style="display:none"></span>
        </button>
        <div class="notif-popover" id="notifPopover">
          <div class="notif-popover__header">
            <svg style="width:16px;height:16px;fill:currentColor;color:var(--color-primary)" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg>
            <span>Próximas Aulas</span>
          </div>
          <div class="notif-popover__body" id="notifBody">
            <div class="notif-popover__loading">Carregando...</div>
          </div>
          <div class="notif-popover__footer" id="notifFooter" style="display:none">
            <button class="btn btn-sm btn-secondary" id="notifToggleRead" style="width:100%;justify-content:center">
              <svg class="notif-toggle-icon" style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"></svg>
              <span class="notif-toggle-label"></span>
            </button>
          </div>
        </div>
      </div>
      <!-- Avatar header -->
      <div class="header-avatar-wrap" id="headerAvatarWrap">
        <?php if ($usuario_foto): ?>
          <img src="<?= $usuario_foto ?>" alt="" class="header-avatar-img" id="headerAvatarBtn">
        <?php else: ?>
          <div class="avatar header-avatar-img" id="headerAvatarBtn" style="width:32px;height:32px;font-size:var(--text-xs)"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Popover do perfil -->
        <div class="profile-popover" id="profilePopover">
          <div class="profile-popover__header">
            <div class="profile-popover__avatar">
              <?php if ($usuario_foto): ?>
                <img src="<?= $usuario_foto ?>" alt="">
              <?php else: ?>
                <div class="avatar" style="width:48px;height:48px;font-size:var(--text-lg)"><?= mb_strtoupper(mb_substr($usuario, 0, 1, 'UTF-8'), 'UTF-8') ?></div>
              <?php endif; ?>
            </div>
            <div class="profile-popover__info">
              <div class="profile-popover__name" id="ppNome"><?= $usuario ?></div>
              <div class="profile-popover__email" id="ppEmail"><?= $usuario_email ?></div>
            </div>
          </div>
          <div class="profile-popover__stats" id="ppStats">
            <div class="profile-popover__stat">
              <svg style="width:16px;height:16px;fill:currentColor;color:var(--color-primary)" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3.001zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.547l1.606.688a3 3 0 002.788 0l1.606-.688v3.547a9.026 9.026 0 00-2.3 1.638z"/></svg>
              <div>
                <span class="profile-popover__stat-value" id="ppTurma">—</span>
                <span class="profile-popover__stat-label">Turma</span>
              </div>
            </div>
            <div class="profile-popover__stat">
              <svg style="width:16px;height:16px;fill:currentColor;color:var(--color-success)" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
              <div>
                <span class="profile-popover__stat-value" id="ppPresencas">—</span>
                <span class="profile-popover__stat-label" id="ppPresLabel">Presenças no trimestre</span>
              </div>
            </div>
            <div class="profile-popover__stat">
              <svg style="width:16px;height:16px;fill:currentColor;color:var(--color-warning)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <div>
                <span class="profile-popover__stat-value" id="ppPontos">—</span>
                <span class="profile-popover__stat-label">Pontos totais</span>
              </div>
            </div>
          </div>
          <div class="profile-popover__footer">
            <a href="logout.php" class="btn btn-sm btn-secondary" style="width:100%;justify-content:center">
              <svg style="width:14px;height:14px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
              Sair
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>


  <!-- ╔══════════════════════════════════════════════╗ -->
  <!-- ║  CONTEÚDO PRINCIPAL                          ║ -->
  <!-- ╚══════════════════════════════════════════════╝ -->
  <div class="main-content">
    <div class="page">

      <?php
      /* ── Breadcrumbs ────────────────────────────────── */
      $breadcrumbMap = [
        'dashboard'        => [['Dashboard','dashboard']],
        'alunos'           => [['Acadêmico',null],['Alunos','alunos']],
        'aluno-novo'       => [['Acadêmico',null],['Alunos','alunos'],['Cadastrar','aluno-novo']],
        'aluno-editar'     => [['Acadêmico',null],['Alunos','alunos'],['Editar','aluno-editar']],
        'professores'      => [['Acadêmico',null],['Professores','professores']],
        'professor-novo'   => [['Acadêmico',null],['Professores','professores'],['Cadastrar','professor-novo']],
        'professor-editar' => [['Acadêmico',null],['Professores','professores'],['Editar','professor-editar']],
        'turmas'           => [['Acadêmico',null],['Turmas','turmas']],
        'turma-nova'       => [['Acadêmico',null],['Turmas','turmas'],['Nova Turma','turma-nova']],
        'turma-editar'     => [['Acadêmico',null],['Turmas','turmas'],['Editar','turma-editar']],
        'aulas'            => [['Acadêmico',null],['Temas de Aulas','aulas']],
        'tema-novo'        => [['Acadêmico',null],['Temas de Aulas','aulas'],['Novo Tema','tema-novo']],
        'tema-editar'      => [['Acadêmico',null],['Temas de Aulas','aulas'],['Editar Tema','tema-editar']],
        'tema-detalhe'     => [['Acadêmico',null],['Temas de Aulas','aulas'],['Detalhe','tema-detalhe']],
        'cronograma'       => [['Acadêmico',null],['Cronograma','cronograma']],
        'calendario'       => [['Acadêmico',null],['Calendário','calendario']],
        'frequencia'       => [['Acadêmico',null],['Frequência','frequencia']],
        'aula-pratica'     => [['Acadêmico',null],['Aula na Prática','aula-pratica']],
        'rel-geral'        => [['Análise',null],['Frequência Geral','rel-geral']],
        'rel-turma'        => [['Análise',null],['Frequência por Turma','rel-turma']],
        'rel-aluno'        => [['Análise',null],['Frequência Individual','rel-aluno']],
        'rel-risco'        => [['Análise',null],['Alunos em Risco','rel-risco']],
        'certificados'     => [['Análise',null],['Certificados','certificados']],
        'vendas'           => [['Análise',null],['Vendas','vendas']],
        'configuracoes'    => [['Sistema',null],['Configurações','configuracoes']],
      ];
      $crumbs = $breadcrumbMap[$pagina] ?? [['Página',null]];
      if ($pagina !== 'dashboard'):
      ?>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="index.php?pagina=dashboard">
          <svg style="width:14px;height:14px;fill:currentColor;vertical-align:-2px" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v6a1 1 0 001 1h3a1 1 0 001-1v-3h2v3a1 1 0 001 1h3a1 1 0 001-1v-6h1a1 1 0 00.707-1.707l-7-7z"/></svg>
        </a>
        <?php foreach ($crumbs as $i => $c):
          $isLast = ($i === count($crumbs) - 1);
        ?>
          <span class="breadcrumb__sep">›</span>
          <?php if ($isLast): ?>
            <span class="breadcrumb__current"><?= htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8') ?></span>
          <?php elseif ($c[1]): ?>
            <a href="index.php?pagina=<?= htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8') ?></a>
          <?php else: ?>
            <span><?= htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>

      <?php if ($pagina === 'dashboard'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  DASHBOARD                                     -->
        <!-- ══════════════════════════════════════════════ -->

        <div class="page-header">
          <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Bem-vindo, <?= $usuario ?>! Aqui está um resumo do sistema.</p>
          </div>
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
                <span class="sk sk-h-4" style="width:60px"></span>
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
                </tbody>
                <script>document.getElementById('tbody-ultimas-matriculas').innerHTML=skeletonTable(4,4);</script>
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
                </tbody>
                <script>document.getElementById('tbody-aniversariantes').innerHTML=skeletonTable(3,4);</script>
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
                </div>
                <script>document.getElementById('dash-domingo-lista').innerHTML=skeletonCards(3);</script>
              </div>
            </div>

            <!-- Frequência por Turma -->
            <div class="card" id="dash-card-freq-turma">
              <div class="card-header" style="flex-wrap:wrap;gap:var(--space-2)">
                <span class="card-title">Frequência por Turma</span>
                <div style="display:flex;align-items:center;gap:var(--space-2)">
                  <select id="dash-freq-trimestre" class="form-control" style="width:auto;font-size:var(--text-xs);padding:2px 8px">
                    <option value="0">Todos os trimestres</option>
                    <option value="1">1º Trimestre</option>
                    <option value="2">2º Trimestre</option>
                    <option value="3">3º Trimestre</option>
                    <option value="4">4º Trimestre</option>
                  </select>
                  <select id="dash-freq-ano" class="form-control" style="width:auto;font-size:var(--text-xs);padding:2px 8px">
                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                  </select>
                  <a href="index.php?pagina=frequencia" class="btn btn-ghost btn-sm">Ver detalhes</a>
                </div>
              </div>
              <div class="card-body" id="dash-freq-body">
              </div>
              <script>document.getElementById('dash-freq-body').innerHTML=skeletonBars(4);</script>
            </div>

            <!-- Ranking Aula na Prática -->
            <div class="card" id="dash-card-ranking-pratica">
              <div class="card-header" style="flex-wrap:wrap;gap:var(--space-2)">
                <span class="card-title">
                  <svg style="width:16px;height:16px;fill:currentColor;vertical-align:middle;margin-right:6px;color:var(--color-warning)" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  Ranking — Aula na Prática
                </span>
                <div style="display:flex;align-items:center;gap:var(--space-2)">
                  <select id="dash-ranking-ano" class="form-control" style="width:auto;font-size:var(--text-xs);padding:2px 8px">
                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                  </select>
                  <a href="index.php?pagina=aula-pratica" class="btn btn-ghost btn-sm">Ver módulo</a>
                </div>
              </div>
              <div id="dash-ranking-pratica-body">
              </div>
              <script>document.getElementById('dash-ranking-pratica-body').innerHTML=skeletonBars(5);</script>
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
          </div>
          <script>document.getElementById('dash-proximos-lista').innerHTML=skeletonCards(3);</script>
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
              </tbody>
              <script>document.getElementById('tbody-alunos').innerHTML=skeletonTable(8,5,{avatar:1});</script>
            </table>
          </div>
        </div>

        <!-- Paginação -->
        <div id="pag-alunos" class="pagination" style="display:none"></div>

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
          <div style="display:flex;gap:var(--space-3)">
            <button class="btn btn-secondary" onclick="abrirFichaCadastro()" title="Abrir ficha de cadastro para impressão">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
              Ficha de Cadastro
            </button>
            <a href="index.php?pagina=alunos" class="btn btn-secondary">
              <svg class="icon" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
              </svg>
              Voltar
            </a>
          </div>
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
                  <!-- Profissão removida -->
                </div>
              </div>

            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-success-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-warning-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-danger-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                  <!-- Profissão removida -->
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-success-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-warning-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
              </tbody>
              <script>document.getElementById('tbody-professores').innerHTML=skeletonTable(8,5,{avatar:1});</script>
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
                  <!-- Profissão removida -->
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-success-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-warning-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                  <!-- Profissão removida -->
                </div>
              </div>
            </div>
          </div>

          <!-- ── Contato ── -->
          <div class="card" style="margin-bottom:var(--space-6)">
            <div class="card-header">
              <span class="card-title" style="display:flex;align-items:center;gap:var(--space-3)">
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-success-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
                <span style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--color-warning-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
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
              </tbody>
              <script>document.getElementById('tbody-turmas').innerHTML=skeletonTable(4,5);</script>
            </table>
          </div>
        </div>

        <!-- Paginação -->
        <div id="pag-turmas" class="pagination" style="display:none"></div>

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
        </div>
        <script>document.getElementById('temas-container').innerHTML=skeletonSections(3);</script>

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
              <h1 class="page-title" id="tdh-titulo"><span class="sk sk-h-5" style="width:200px"></span></h1>
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
                </tbody>
                <script>document.getElementById('tdh-tbody').innerHTML=skeletonTable(5,3);</script>
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
                  <label class="form-label" for="aula-professor">Professor Titular</label>
                  <select id="aula-professor" class="form-control">
                    <option value="">— Selecionar professor —</option>
                  </select>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                  <label class="form-label" for="aula-professor-substituto">Professor Substituto <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <select id="aula-professor-substituto" class="form-control">
                    <option value="">— Selecionar professor substituto —</option>
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
          <div id="cron-export-btns" style="display:none;gap:var(--space-2);flex-wrap:wrap">
            <button class="btn btn-secondary" id="btnExportarXls" title="Exportar como Excel (.xls)">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              Exportar XLS
            </button>
            <button class="btn btn-secondary" id="btnExportarPdf" title="Exportar como PDF">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
              Exportar PDF
            </button>
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
                  <span class="form-error" id="comp-titulo-error"></span>
                </div>

                <!-- Data -->
                <div class="form-group">
                  <label class="form-label" for="comp-data">Data <span style="color:var(--color-danger)">*</span></label>
                  <input type="date" id="comp-data" class="form-control">
                  <span class="form-error" id="comp-data-error"></span>
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

      <?php elseif ($pagina === 'aula-pratica'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  AULA NA PRÁTICA                               -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Aula na Prática</h1>
            <p class="page-subtitle">Registre pontos dos alunos que respondem às perguntas após a aula.</p>
          </div>
          <button class="btn btn-primary" id="btnNovaSessao">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Nova Sessão
          </button>
        </div>

        <!-- ── Alerta global ── -->
        <div id="ap-alert" style="display:none;margin-bottom:var(--space-4)"></div>

        <!-- Etapas dos domingos do trimestre -->
        <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-2)">
          <span style="font-size:var(--text-sm);font-weight:600;color:var(--color-text-muted)">Domingos do trimestre:</span>
          <select id="ap-steps-trimestre" class="form-control" style="width:auto;font-size:var(--text-sm)">
            <option value="1">1º Trimestre</option>
            <option value="2">2º Trimestre</option>
            <option value="3">3º Trimestre</option>
            <option value="4">4º Trimestre</option>
          </select>
          <select id="ap-steps-ano" class="form-control" style="width:auto;font-size:var(--text-sm)">
            <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
              <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div id="ap-steps-domingos"></div>

        <!-- ── Wrapper: lista de sessões + painel ativo ── -->
        <div style="display:grid;grid-template-columns:340px 1fr;gap:var(--space-6);align-items:start">

          <!-- ══ Coluna esquerda: lista de sessões ══ -->
          <div class="card" style="min-height:400px">
            <div class="card-header">
              <span class="card-title">Sessões</span>
              <select id="ap-filtro-turma" class="form-control" style="width:auto;font-size:var(--text-xs)">
                <option value="0">Todas as turmas</option>
              </select>
            </div>
            <!-- Abas Ativas / Arquivadas -->
            <div style="display:flex;border-bottom:1px solid var(--color-border,#e5e7eb)">
              <button class="ap-tab active" data-tab="ativas" id="ap-tab-ativas" style="flex:1;padding:var(--space-2) var(--space-3);font-size:var(--text-xs);font-weight:600;background:none;border:none;border-bottom:2px solid var(--color-primary);color:var(--color-primary);cursor:pointer">Ativas</button>
              <button class="ap-tab" data-tab="arquivadas" id="ap-tab-arquivadas" style="flex:1;padding:var(--space-2) var(--space-3);font-size:var(--text-xs);font-weight:600;background:none;border:none;border-bottom:2px solid transparent;color:var(--color-text-muted);cursor:pointer">Arquivadas</button>
            </div>
            <div id="ap-sessoes-lista" style="padding:var(--space-2) 0">
            </div>
            <script>document.getElementById('ap-sessoes-lista').innerHTML=skeletonCards(3);</script>
          </div>

          <!-- ══ Coluna direita: painel da sessão ativa ══ -->
          <div id="ap-painel" style="display:none;flex-direction:column;gap:var(--space-6)">

            <!-- Cabeçalho da sessão -->
            <div class="card">
              <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;gap:var(--space-4);flex-wrap:wrap">
                <div>
                  <div style="font-size:var(--text-lg);font-weight:700;color:var(--color-text)" id="ap-sessao-titulo">—</div>
                  <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px" id="ap-sessao-info">—</div>
                </div>
                <div style="display:flex;gap:var(--space-2);align-items:center">
                  <button class="btn btn-warning btn-sm" id="btnEncerrarSessao" title="Encerrar sessão">
                    <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/></svg>
                    Encerrar
                  </button>
                  <button class="btn btn-success btn-sm" id="btnReabrirSessao" title="Reabrir sessão" style="display:none">
                    <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>
                    Reabrir
                  </button>
                  <button class="btn btn-danger btn-sm" id="btnExcluirSessao" title="Excluir sessão">
                    <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Excluir
                  </button>
                </div>
              </div>
            </div>

            <!-- Presença -->
            <div class="card" id="ap-presenca-card">
              <div class="card-header">
                <span class="card-title">
                  <svg style="width:15px;height:15px;fill:currentColor;vertical-align:middle;margin-right:6px" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Presença
                </span>
                <span class="badge badge-success" id="ap-presenca-badge">0 presentes</span>
              </div>
              <div id="ap-presenca-container" style="padding:var(--space-3) var(--space-4)">
              </div>
              <script>document.getElementById('ap-presenca-container').innerHTML=skeletonTable(2,4);</script>
            </div>

            <!-- Registrar resposta -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">Registrar Resposta</span>
              </div>
              <div class="card-body" style="display:flex;flex-direction:column;gap:var(--space-4)">
                <div id="ap-resposta-alert" style="display:none"></div>

                <!-- Pergunta -->
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ap-pergunta-sel">Pergunta <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <select id="ap-pergunta-sel" class="form-control">
                    <option value="">— Selecionar pergunta —</option>
                    <option value="__outra__">✏️ Outra pergunta…</option>
                  </select>
                  <input type="text" id="ap-pergunta-txt" class="form-control" placeholder="Descreva a pergunta respondida…" maxlength="300" style="margin-top:var(--space-2);display:none">
                  <!-- Resposta para conferência -->
                  <div id="ap-resposta-preview" style="display:none;margin-top:var(--space-2);padding:var(--space-3) var(--space-4);background:var(--color-success-bg,#f0fdf4);border:1px solid var(--color-success-border,#bbf7d0);border-radius:var(--radius-md);font-size:var(--text-sm)">
                    <span style="font-weight:600;color:var(--color-success)">✅ Resposta:</span>
                    <span id="ap-resposta-preview-txt" style="color:var(--color-success);margin-left:var(--space-2)"></span>
                  </div>
                </div>

                <!-- Aluno -->
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ap-aluno">Aluno <span style="color:var(--color-danger)">*</span></label>
                  <select id="ap-aluno" class="form-control">
                    <option value="">— Selecionar aluno —</option>
                  </select>
                </div>

                <!-- Tipo de resposta -->
                <div>
                  <label class="form-label">Tipo de Resposta</label>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                    <button type="button" class="btn btn-pratica" id="btnSemLeitura" data-tipo="sem_leitura">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      Sem Leitura
                      <span class="badge badge-warning" style="margin-left:var(--space-1)">+2 pts</span>
                    </button>
                    <button type="button" class="btn btn-pratica-sec" id="btnComLeitura" data-tipo="com_leitura">
                      <svg class="icon" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.468 0 2.816.479 3.9 1.272A7.969 7.969 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                      Com Leitura
                      <span class="badge badge-primary" style="margin-left:var(--space-1)">+1 pt</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ranking -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">🏆 Ranking da Sessão</span>
                <span class="badge badge-primary" id="ap-ranking-count">0 alunos</span>
              </div>
              <div id="ap-ranking-container">
                <div style="padding:var(--space-6);text-align:center;color:var(--color-text-muted)">Nenhuma resposta ainda.</div>
              </div>
            </div>

            <!-- Histórico -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">Histórico de Respostas</span>
              </div>
              <div class="table-wrapper" style="border:none;border-radius:0;box-shadow:none">
                <table class="table" id="ap-historico-table">
                  <thead>
                    <tr>
                      <th>Aluno</th>
                      <th>Pergunta</th>
                      <th>Tipo</th>
                      <th>Pontos</th>
                      <th>Horário</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="ap-historico-tbody">
                    <tr><td colspan="6" style="text-align:center;color:var(--color-text-muted)">Nenhuma resposta ainda.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div><!-- /ap-painel -->

          <!-- Placeholder quando nenhuma sessão selecionada -->
          <div id="ap-placeholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:var(--space-16);color:var(--color-text-muted);text-align:center">
            <svg style="width:56px;height:56px;fill:currentColor;opacity:.3;margin-bottom:var(--space-4)" viewBox="0 0 20 20">
              <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.468 0 2.816.479 3.9 1.272A7.969 7.969 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
            </svg>
            <p>Selecione uma sessão na lista<br>ou crie uma nova para começar.</p>
          </div>

        </div><!-- /grid -->

        <!-- ════════════ MODAL: NOVA SESSÃO ════════════ -->
        <div class="modal-overlay" id="modalNovaSessao" style="display:none">
          <div class="modal" style="max-width:480px">
            <div class="modal-header">
              <span class="modal-title">Nova Sessão — Aula na Prática</span>
              <button class="modal-close" id="btnFecharModalSessao">&times;</button>
            </div>
            <div class="modal-body">
              <div id="ns-alert" style="display:none;margin-bottom:var(--space-4)"></div>
              <div style="display:flex;flex-direction:column;gap:var(--space-4)">
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ns-titulo">Título <span style="color:var(--color-danger)">*</span></label>
                  <input type="text" id="ns-titulo" class="form-control" placeholder="Ex.: Aula 1 — Gênesis" maxlength="200">
                  <span class="form-error" id="ns-titulo-error"></span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                  <div class="form-group" style="margin:0">
                    <label class="form-label" for="ns-data">Data</label>
                    <input type="date" id="ns-data" class="form-control" value="<?= date('Y-m-d') ?>">
                  </div>
                  <div class="form-group" style="margin:0">
                    <label class="form-label" for="ns-turma">Turma</label>
                    <select id="ns-turma" class="form-control">
                      <option value="0">— Todas —</option>
                    </select>
                  </div>
                </div>
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ns-aula">Aula <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <select id="ns-aula" class="form-control">
                    <option value="0">— Selecionar aula —</option>
                  </select>
                </div>
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ns-professor">Professor Titular <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <select id="ns-professor" class="form-control">
                    <option value="0">— Nenhum —</option>
                  </select>
                </div>
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ns-professor-substituto">Professor Substituto <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <select id="ns-professor-substituto" class="form-control">
                    <option value="0">— Nenhum —</option>
                  </select>
                </div>
                <div class="form-group" style="margin:0">
                  <label class="form-label" for="ns-descricao">Descrição <span style="color:var(--color-text-muted);font-weight:400">(opcional)</span></label>
                  <textarea id="ns-descricao" class="form-control" rows="2" maxlength="500" placeholder="Tema ou conteúdo da aula…"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" id="btnCancelarSessao">Cancelar</button>
              <button class="btn btn-primary" id="btnSalvarSessao">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Criar Sessão
              </button>
            </div>
          </div>
        </div>

        <!-- ════════════ MODAL: CONFIRMAR EXCLUSÃO ════════════ -->
        <div class="modal-overlay" id="modalConfirmarExcluirSessao" style="display:none">
          <div class="modal" style="max-width:400px">
            <div class="modal-header">
              <span class="modal-title">Excluir Sessão</span>
              <button class="modal-close" onclick="document.getElementById('modalConfirmarExcluirSessao').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
              <p style="margin:0">Deseja excluir a sessão <strong id="excluir-sessao-nome"></strong>?<br>
              <small class="text-muted">Todas as respostas e pontuações serão apagadas.</small></p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="document.getElementById('modalConfirmarExcluirSessao').style.display='none'">Cancelar</button>
              <button class="btn btn-danger" id="btnConfirmarExcluirSessao">Excluir</button>
            </div>
          </div>
        </div>

        <!-- ════════════ JAVASCRIPT ════════════ -->
        <script src="libs/js/aula-pratica.js?v=<?php echo filemtime('libs/js/aula-pratica.js'); ?>"></script>

      <?php elseif ($pagina === 'frequencia'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  FREQUÊNCIA DE ALUNOS                         -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Frequência de Alunos</h1>
            <p class="page-subtitle">Presenças por aula, trimestre e total geral.</p>
          </div>
          <div id="freq-export-btns" style="display:none;gap:var(--space-2)">
            <button class="btn btn-secondary" id="btnFreqXls" title="Exportar como Excel (.xls)">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              Exportar XLS
            </button>
            <button class="btn btn-secondary" id="btnFreqPdf">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
              Exportar PDF
            </button>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-4);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 100px">
              <label class="form-label">Ano</label>
              <select id="freq-ano" class="form-control"></select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:180px">
              <label class="form-label">Turma <span style="color:var(--color-danger)">*</span></label>
              <select id="freq-turma" class="form-control">
                <option value="0">— Selecione —</option>
              </select>
            </div>
            <button class="btn btn-primary" id="btnGerarFreq" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Gerar
            </button>
          </div>
        </div>

        <div id="freq-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="freq-loading" style="display:none;text-align:center;padding:var(--space-8)">
        </div>
        <script>document.getElementById('freq-loading').innerHTML=skeletonTable(6,5);</script>
        <div id="freq-container"></div>

        <script src="libs/js/frequencia.js?v=<?php echo filemtime('libs/js/frequencia.js'); ?>"></script>

      <?php elseif ($pagina === 'rel-geral'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  RELATÓRIO — FREQUÊNCIA GERAL                 -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Frequência Geral</h1>
            <p class="page-subtitle">Comparativo de frequência por turma e trimestre.</p>
          </div>
          <div style="display:flex;gap:var(--space-2);align-items:center">
            <select id="rg-ano" class="form-control" style="width:auto"></select>
            <button class="btn btn-primary" id="btnRgGerar">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Gerar
            </button>
            <button class="btn btn-secondary" id="btnRgXls" style="display:none">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              XLS
            </button>
            <button class="btn btn-secondary" id="btnRgPdf" style="display:none">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
              PDF
            </button>
          </div>
        </div>
        <div id="rg-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="rg-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('rg-loading').innerHTML=skeletonTable(5,5);</script>
        <div id="rg-container"></div>

        <script src="libs/js/rel-geral.js?v=<?php echo filemtime('libs/js/rel-geral.js'); ?>"></script>

      <?php elseif ($pagina === 'rel-turma'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  RELATÓRIO — FREQUÊNCIA POR TURMA             -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Frequência por Turma</h1>
            <p class="page-subtitle">Presença de cada aluno, por aula e trimestre.</p>
          </div>
        </div>
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-4);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 100px">
              <label class="form-label">Ano</label>
              <select id="rt-ano" class="form-control"></select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:180px">
              <label class="form-label">Turma <span style="color:var(--color-danger)">*</span></label>
              <select id="rt-turma" class="form-control"><option value="0">— Selecione —</option></select>
            </div>
            <button class="btn btn-primary" id="btnRtGerar" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Gerar
            </button>
            <button class="btn btn-secondary" id="btnRtPdf" style="display:none">PDF</button>
            <button class="btn btn-secondary" id="btnRtXls" style="display:none">XLS</button>
          </div>
        </div>
        <div id="rt-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="rt-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('rt-loading').innerHTML=skeletonTable(5,5);</script>
        <div id="rt-container"></div>

        <script src="libs/js/rel-turma.js?v=<?php echo filemtime('libs/js/rel-turma.js'); ?>"></script>

      <?php elseif ($pagina === 'rel-aluno'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  RELATÓRIO — FREQUÊNCIA INDIVIDUAL            -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Frequência Individual</h1>
            <p class="page-subtitle">Histórico de presença de um único aluno.</p>
          </div>
        </div>
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-4);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 100px">
              <label class="form-label">Ano</label>
              <select id="ra-ano" class="form-control"></select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:160px">
              <label class="form-label">Turma</label>
              <select id="ra-turma" class="form-control"><option value="0">— Selecione —</option></select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:160px">
              <label class="form-label">Aluno <span style="color:var(--color-danger)">*</span></label>
              <select id="ra-aluno" class="form-control" disabled><option value="0">— Selecione a turma —</option></select>
            </div>
            <button class="btn btn-primary" id="btnRaGerar" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Gerar
            </button>
            <button class="btn btn-secondary" id="btnRaXls" style="display:none">XLS</button>
            <button class="btn btn-secondary" id="btnRaPdf" style="display:none">PDF</button>
          </div>
        </div>
        <div id="ra-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="ra-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('ra-loading').innerHTML=skeletonTable(4,5);</script>
        <div id="ra-container"></div>

        <script src="libs/js/rel-aluno.js?v=<?php echo filemtime('libs/js/rel-aluno.js'); ?>"></script>

      <?php elseif ($pagina === 'rel-risco'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  RELATÓRIO — ALUNOS EM RISCO                  -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Alunos em Risco</h1>
            <p class="page-subtitle">Alunos com frequência abaixo do limiar definido.</p>
          </div>
        </div>
        <div class="card" style="margin-bottom:var(--space-6)">
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-4);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 100px">
              <label class="form-label">Ano</label>
              <select id="rr-ano" class="form-control"></select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 155px">
              <label class="form-label">Trimestre</label>
              <select id="rr-trimestre" class="form-control">
                <option value="0">Todos</option>
                <option value="1">1º Trimestre</option>
                <option value="2">2º Trimestre</option>
                <option value="3">3º Trimestre</option>
                <option value="4">4º Trimestre</option>
              </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:160px">
              <label class="form-label">Turma</label>
              <select id="rr-turma" class="form-control"><option value="0">Todas</option></select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 130px">
              <label class="form-label">Limiar (%)</label>
              <input type="number" id="rr-limiar" class="form-control" value="75" min="1" max="100">
            </div>
            <button class="btn btn-primary" id="btnRrGerar" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Gerar
            </button>
            <button class="btn btn-secondary" id="btnRrXls" style="display:none">XLS</button>
            <button class="btn btn-secondary" id="btnRrPdf" style="display:none">PDF</button>
          </div>
        </div>
        <div id="rr-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="rr-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('rr-loading').innerHTML=skeletonTable(5,5);</script>
        <div id="rr-container"></div>

        <script src="libs/js/rel-risco.js?v=<?php echo filemtime('libs/js/rel-risco.js'); ?>"></script>

      <?php elseif ($pagina === 'certificados'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  CERTIFICADOS                                  -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Certificados</h1>
            <p class="page-subtitle">Emita certificados trimestrais ou anuais com desempenho, presença e pontuação.</p>
          </div>
        </div>

        <div class="card" style="margin-bottom:var(--space-5)">
          <div class="card-header"><span class="card-title">Filtros</span></div>
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-3);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:1 1 150px">
              <label class="form-label">Turma</label>
              <select id="cert-turma" class="form-control"><option value="">Carregando…</option></select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 120px">
              <label class="form-label">Ano</label>
              <select id="cert-ano" class="form-control"><option value="">Carregando…</option></select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 160px">
              <label class="form-label">Tipo</label>
              <select id="cert-tipo" class="form-control">
                <option value="anual">Anual</option>
                <option value="trimestral">Trimestral</option>
              </select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 160px;display:none" id="cert-tri-wrap">
              <label class="form-label">Trimestre</label>
              <select id="cert-trimestre" class="form-control">
                <option value="1">1º Trimestre</option>
                <option value="2">2º Trimestre</option>
                <option value="3">3º Trimestre</option>
                <option value="4">4º Trimestre</option>
              </select>
            </div>
            <button class="btn btn-primary" id="btnCertGerar" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Consultar
            </button>
          </div>
        </div>

        <div id="cert-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="cert-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('cert-loading').innerHTML=skeletonTable(6,5);</script>
        <div id="cert-container"></div>

        <!-- Modal de impressão do certificado -->
        <div id="cert-modal" class="modal-overlay" style="display:none">
          <div class="modal" style="max-width:850px;padding:0;background:transparent;box-shadow:none;overflow:visible">
            <div id="cert-print-area"></div>
            <div style="display:flex;gap:var(--space-3);justify-content:center;padding:var(--space-4)">
              <button class="btn btn-primary" id="btnCertPrint">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                Imprimir
              </button>
              <button class="btn btn-secondary" id="btnCertFechar">Fechar</button>
            </div>
          </div>
        </div>

        <script src="libs/js/certificados.js?v=<?php echo filemtime('libs/js/certificados.js'); ?>"></script>

      <?php elseif ($pagina === 'biblia'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  BÍBLIA DIGITAL                               -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Bíblia Digital</h1>
            <p class="page-subtitle">Consulte qualquer versículo da Bíblia Sagrada (Almeida) rapidamente.</p>
          </div>
        </div>
        <div class="card" style="max-width:600px;margin-bottom:var(--space-5)">
          <div class="card-header"><span class="card-title">Navegação</span></div>
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-3);align-items:flex-end">
            <div class="form-group" style="flex:1 1 180px">
              <label class="form-label">Livro</label>
              <select id="biblia-livro" class="form-control"></select>
            </div>
            <div class="form-group" style="flex:0 0 100px">
              <label class="form-label">Capítulo</label>
              <select id="biblia-capitulo" class="form-control"></select>
            </div>
            <div class="form-group" style="flex:0 0 100px">
              <label class="form-label">Versículo</label>
              <select id="biblia-versiculo" class="form-control"></select>
            </div>
            <button class="btn btn-primary" id="btnBuscarBiblia">Buscar</button>
          </div>
        </div>
        <div id="biblia-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="biblia-resultado" class="card" style="display:none;max-width:600px"></div>

        <script src="libs/js/biblia.js?v=<?php echo time(); ?>"></script>

      <?php elseif ($pagina === 'vendas'): ?>
        <!-- ══════════════════════════════════════════════ -->
        <!--  VENDAS DE REVISTAS                            -->
        <!-- ══════════════════════════════════════════════ -->
        <div class="page-header">
          <div>
            <h1 class="page-title">Vendas de Revistas</h1>
            <p class="page-subtitle">Registre vendas, acompanhe pagamentos e gerencie débitos.</p>
          </div>
          <button class="btn btn-primary" id="btnNovaVenda">
            <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Nova Venda
          </button>
        </div>

        <!-- Dashboard cards -->
        <div class="stats-grid" style="margin-bottom:var(--space-5)">
          <div class="stat-card">
            <div class="stat-card__icon icon-bg-blue">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/></svg>
            </div>
            <div>
              <div class="stat-card__value" id="vd-total-vendas">—</div>
              <div class="stat-card__label">Total Vendido</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card__icon icon-bg-green">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <div>
              <div class="stat-card__value" id="vd-total-pago">—</div>
              <div class="stat-card__label">Total Pago</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card__icon icon-bg-orange">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
            <div>
              <div class="stat-card__value" id="vd-total-debito">—</div>
              <div class="stat-card__label">Total em Débito</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card__icon icon-bg-purple">
              <svg style="width:22px;height:22px;fill:currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
            </div>
            <div>
              <div class="stat-card__value" id="vd-qtd-revistas">—</div>
              <div class="stat-card__label">Revistas Vendidas</div>
            </div>
          </div>
        </div>

        <!-- Dash de Pizza -->
        <div class="card" style="max-width:420px;margin-bottom:var(--space-4)">
          <div class="card-header"><span class="card-title">Situação dos Pagamentos</span></div>
          <div class="card-body" style="display:flex;justify-content:center;align-items:center">
            <canvas id="vd-pizza" width="180" height="180"></canvas>
          </div>
        </div>

        <!-- Abas -->
        <div class="vd-tabs" style="margin-bottom:var(--space-4)">
          <button class="vd-tab active" data-vd-tab="historico">Histórico de Vendas</button>
          <button class="vd-tab" data-vd-tab="devedores">Em débito</button>
        </div>

        <!-- Filtros do histórico -->
        <div class="card" id="vd-filtros-card" style="margin-bottom:var(--space-5)">
          <div class="card-header"><span class="card-title">Filtros</span></div>
          <div class="card-body" style="display:flex;flex-wrap:wrap;gap:var(--space-3);align-items:flex-end">
            <div class="form-group" style="margin:0;flex:0 0 120px">
              <label class="form-label">Ano</label>
              <select id="vd-ano" class="form-control"><option value="">Carregando…</option></select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 160px">
              <label class="form-label">Trimestre</label>
              <select id="vd-trimestre" class="form-control">
                <option value="">Todos</option>
                <option value="1">1º Trimestre</option>
                <option value="2">2º Trimestre</option>
                <option value="3">3º Trimestre</option>
                <option value="4">4º Trimestre</option>
              </select>
            </div>
            <div class="form-group" style="margin:0;flex:0 0 140px">
              <label class="form-label">Status</label>
              <select id="vd-status" class="form-control">
                <option value="">Todos</option>
                <option value="pago">Pago</option>
                <option value="fiado">Fiado</option>
              </select>
            </div>
            <button class="btn btn-primary" id="btnVdFiltrar" style="flex-shrink:0">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V17a1 1 0 01-.553.894l-4-2A1 1 0 018 15v-4.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
              Filtrar
            </button>
          </div>
        </div>

        <div id="vd-alert" style="display:none;margin-bottom:var(--space-4)"></div>
        <div id="vd-loading" style="display:none;padding:var(--space-4)"></div>
        <script>document.getElementById('vd-loading').innerHTML=skeletonTable(7,5);</script>
        <div id="vd-container"></div>

        <!-- Modal Nova Venda -->
        <div id="vd-modal" class="modal-overlay" style="display:none">
          <div class="modal">
            <div class="modal-header">
              <h3 class="modal-title">Nova Venda de Revista</h3>
              <button class="modal-close" id="btnVdFecharModal">&times;</button>
            </div>
            <div class="modal-body">
              <div id="vd-modal-alert" style="display:none;margin-bottom:var(--space-3)"></div>
              <div class="form-group">
                <label class="form-label">Pessoa (Aluno/Professor)</label>
                <select id="vd-pessoa" class="form-control"><option value="">Carregando…</option></select>
              </div>
              <div class="form-group">
                <label class="form-label">Tipo de Revista</label>
                <select id="vd-tipo-revista" class="form-control">
                  <option value="aluno">Revista do Aluno — R$ 10,00</option>
                  <option value="professor">Revista do Professor — R$ 15,00</option>
                </select>
              </div>
              <div style="display:flex;gap:var(--space-3)">
                <div class="form-group" style="flex:1">
                  <label class="form-label">Trimestre</label>
                  <select id="vd-modal-tri" class="form-control">
                    <option value="1">1º Trimestre</option>
                    <option value="2">2º Trimestre</option>
                    <option value="3">3º Trimestre</option>
                    <option value="4">4º Trimestre</option>
                  </select>
                </div>
                <div class="form-group" style="flex:1">
                  <label class="form-label">Ano</label>
                  <select id="vd-modal-ano" class="form-control"></select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Forma de Pagamento</label>
                <select id="vd-forma" class="form-control">
                  <option value="dinheiro">Dinheiro</option>
                  <option value="pix">Pix</option>
                  <option value="cartao">Cartão</option>
                  <option value="transferencia">Transferência</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer">
                  <input type="checkbox" id="vd-fiado">
                  <span style="font-size:var(--text-sm);font-weight:500">Fiado (pagar depois)</span>
                </label>
              </div>
              <div class="form-group">
                <label class="form-label">Observação <span style="font-weight:400;color:var(--color-text-muted)">(opcional)</span></label>
                <input type="text" id="vd-obs" class="form-control" maxlength="255" placeholder="Ex: Vai pagar no próximo domingo">
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" id="btnVdCancelar">Cancelar</button>
              <button class="btn btn-primary" id="btnVdSalvar">
                <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Registrar Venda
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Quitar -->
        <div id="vd-modal-quitar" class="modal-overlay" style="display:none">
          <div class="modal" style="max-width:400px">
            <div class="modal-header">
              <h3 class="modal-title">Registrar Pagamento</h3>
              <button class="modal-close" id="btnVdFecharQuitar">&times;</button>
            </div>
            <div class="modal-body">
              <p id="vd-quitar-info" style="margin:0 0 var(--space-3)"></p>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Forma de Pagamento</label>
                <select id="vd-quitar-forma" class="form-control">
                  <option value="dinheiro">Dinheiro</option>
                  <option value="pix">Pix</option>
                  <option value="cartao">Cartão</option>
                  <option value="transferencia">Transferência</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" id="btnVdCancelarQuitar">Cancelar</button>
              <button class="btn btn-primary" id="btnVdConfirmarQuitar">Confirmar Pagamento</button>
            </div>
          </div>
        </div>

        <script src="libs/chart.min.js?v=<?php echo filemtime('libs/chart.min.js'); ?>"></script>
        <script src="libs/js/vendas.js?v=<?php echo filemtime('libs/js/vendas.js'); ?>"></script>

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
          'aula-pratica'   => 'Aula na Prática',
          'rel-geral'      => 'Relatório — Frequência Geral',
          'rel-turma'      => 'Relatório — Frequência por Turma',
          'rel-aluno'      => 'Relatório — Frequência Individual',
          'rel-risco'      => 'Relatório — Alunos em Risco',
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


  <script src="libs/js/global.js?v=<?php echo filemtime('libs/js/global.js'); ?>"></script>
  <?php
  $jsPageMap = [
    'dashboard'        => ['dashboard.js'],
    'alunos'           => ['alunos.js'],
    'aluno-novo'       => ['form-pessoa.js'],
    'aluno-editar'     => ['form-pessoa.js'],
    'professores'      => ['professores.js'],
    'professor-novo'   => ['form-pessoa.js'],
    'professor-editar' => ['form-pessoa.js'],
    'turmas'           => ['turmas.js'],
    'turma-nova'       => ['turmas.js'],
    'turma-editar'     => ['turmas.js'],
    'aulas'            => ['temas.js'],
    'tema-novo'        => ['temas.js'],
    'tema-editar'      => ['temas.js'],
    'tema-detalhe'     => ['temas.js'],
    'cronograma'       => ['cronograma.js'],
    'calendario'       => ['calendario.js'],
    'configuracoes'    => ['configuracoes.js'],
  ];
  foreach ($jsPageMap[$pagina] ?? [] as $js) {
    $path = 'libs/js/' . $js;
    echo '<script src="' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '?v=' . filemtime($path) . '"></script>' . "\n";
  }
  ?>

</body>

</html>
