<?php
/**
 * frequencia_crud.php
 * Relatório de frequência por turma / ano
 *
 * GET ?recurso=turmas          — lista turmas
 * GET ?recurso=anos             — anos com aulas cadastradas
 * GET ?recurso=relatorio&turma_id=X&ano=YYYY — dados do relatório
 */

require_once __DIR__ . '/libs/helpers.php';
/** @var mysqli $conexao */
requireAuth();
csrfCheck();

$recurso = strtolower(trim($_GET['recurso'] ?? ''));

/* ─── Turmas ─────────────────────────────────────────────── */
if ($recurso === 'turmas') {
    $r = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
    $rows = [];
    while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
    ok(['turmas' => $rows]);
    exit;
}

/* ─── Anos disponíveis ───────────────────────────────────── */
if ($recurso === 'anos') {
    $r = $conexao->query("
        SELECT DISTINCT t.ano
        FROM tb_cad_temas t
        INNER JOIN tb_cad_aulas a ON a.tema_id = t.id
        INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id
        ORDER BY t.ano DESC
    ");
    $rows = [];
    while ($row = mysqli_fetch_assoc($r)) $rows[] = (int)$row['ano'];
    if (empty($rows)) {
        $r2 = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
        while ($row = mysqli_fetch_assoc($r2)) $rows[] = (int)$row['ano'];
    }
    ok(['anos' => $rows]);
    exit;
}

/* ─── Dashboard: % de frequência por turma ──────────────── */
if ($recurso === 'dashboard-freq') {
    $ano       = (int)($_GET['ano']       ?? date('Y'));
    $trimestre = (int)($_GET['trimestre'] ?? 0);

    $ano = ($ano >= 2000 && $ano <= 2100) ? $ano : (int)date('Y');

    $rT = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
    $turmas = [];
    while ($row = $rT->fetch_assoc()) $turmas[] = $row;

    $rY = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
    $anos = [];
    while ($row = $rY->fetch_assoc()) $anos[] = (int)$row['ano'];
    $anoAtual = (int)date('Y');
    if (!in_array($anoAtual, $anos)) array_unshift($anos, $anoAtual);

    /* Prepara statements reutilizáveis */
    $sqlAulas = 'SELECT COUNT(a.id) AS total FROM tb_cad_aulas a INNER JOIN tb_cad_temas t ON t.id = a.tema_id INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id WHERE t.turma_id = ? AND t.ano = ?'
        . ($trimestre > 0 ? ' AND t.trimestre = ?' : '');
    $stAulas = $conexao->prepare($sqlAulas);

    $sqlAlunos = $conexao->prepare("SELECT COUNT(*) AS total FROM tb_cad_alunos WHERE turma COLLATE utf8mb4_general_ci = ? AND LOWER(status) = 'ativo'");

    $sqlPres = 'SELECT COUNT(p.id) AS total FROM tb_aula_pratica_presenca p INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id INNER JOIN tb_cad_aulas a ON a.id = s.aula_id INNER JOIN tb_cad_temas t ON t.id = a.tema_id WHERE t.turma_id = ? AND t.ano = ?'
        . ($trimestre > 0 ? ' AND t.trimestre = ?' : '');
    $stPres = $conexao->prepare($sqlPres);

    $resultado = [];

    foreach ($turmas as $turma) {
        $tid       = (int)$turma['id'];
        $nomeTurma = $turma['nome_turma'];

        if ($trimestre > 0) { $stAulas->bind_param('iii', $tid, $ano, $trimestre); }
        else { $stAulas->bind_param('ii', $tid, $ano); }
        $stAulas->execute();
        $totalAulas = (int)($stAulas->get_result()->fetch_assoc()['total'] ?? 0);

        $sqlAlunos->bind_param('s', $nomeTurma);
        $sqlAlunos->execute();
        $totalAlunos = (int)($sqlAlunos->get_result()->fetch_assoc()['total'] ?? 0);

        $totalPossivel = $totalAulas * $totalAlunos;
        $pct = 0;

        if ($totalPossivel > 0) {
            if ($trimestre > 0) { $stPres->bind_param('iii', $tid, $ano, $trimestre); }
            else { $stPres->bind_param('ii', $tid, $ano); }
            $stPres->execute();
            $totalPresencas = (int)($stPres->get_result()->fetch_assoc()['total'] ?? 0);
            $pct = round(($totalPresencas / $totalPossivel) * 100);
        }

        $resultado[] = [
            'turma_id'     => $tid,
            'nome_turma'   => $nomeTurma,
            'total_aulas'  => $totalAulas,
            'total_alunos' => $totalAlunos,
            'pct'          => $pct,
        ];
    }
    $stAulas->close();
    $sqlAlunos->close();
    $stPres->close();

    usort($resultado, function($a, $b) {
        return $b['pct'] - $a['pct'];
    });

    ok(['turmas' => $resultado, 'anos' => $anos, 'ano' => $ano, 'trimestre' => $trimestre]);
    exit;
}

/* ─── rel-geral: comparativo por turma × trimestre ─────── */
if ($recurso === 'rel-geral') {
    $ano = (int)($_GET['ano'] ?? date('Y'));
    $ano = ($ano >= 2000 && $ano <= 2100) ? $ano : (int)date('Y');

    $rY = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
    $anos = [];
    while ($row = $rY->fetch_assoc()) $anos[] = (int)$row['ano'];
    if (!in_array((int)date('Y'), $anos)) array_unshift($anos, (int)date('Y'));

    $stTri = $conexao->prepare("SELECT DISTINCT trimestre FROM tb_cad_temas WHERE ano = ? ORDER BY trimestre ASC");
    $stTri->bind_param('i', $ano);
    $stTri->execute();
    $rTri = $stTri->get_result();
    $trimestres = [];
    while ($row = $rTri->fetch_assoc()) $trimestres[] = (int)$row['trimestre'];
    $stTri->close();

    $rT = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
    $turmas = [];
    while ($row = $rT->fetch_assoc()) $turmas[] = $row;

    /* Prepared statements reutilizáveis no loop */
    $stAlunos = $conexao->prepare("SELECT COUNT(*) AS c FROM tb_cad_alunos WHERE turma COLLATE utf8mb4_general_ci = ? AND LOWER(status) = 'ativo'");
    $stAulas  = $conexao->prepare('SELECT COUNT(a.id) AS c FROM tb_cad_aulas a INNER JOIN tb_cad_temas t ON t.id = a.tema_id INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id WHERE t.turma_id = ? AND t.ano = ? AND t.trimestre = ?');
    $stPres   = $conexao->prepare('SELECT COUNT(p.id) AS c FROM tb_aula_pratica_presenca p INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id INNER JOIN tb_cad_aulas a ON a.id = s.aula_id INNER JOIN tb_cad_temas t ON t.id = a.tema_id WHERE t.turma_id = ? AND t.ano = ? AND t.trimestre = ?');

    $dados = [];
    $totalPresencasGeral = 0;
    $totalPossivelGeral  = 0;

    foreach ($turmas as $turma) {
        $tid       = (int)$turma['id'];
        $nomeTurma = $turma['nome_turma'];

        $stAlunos->bind_param('s', $nomeTurma);
        $stAlunos->execute();
        $totalAlunos = (int)($stAlunos->get_result()->fetch_assoc()['c'] ?? 0);

        $porTri = [];
        $aulasTotalTurma = 0;
        $presencasTotalTurma = 0;

        foreach ($trimestres as $tri) {
            $stAulas->bind_param('iii', $tid, $ano, $tri);
            $stAulas->execute();
            $aulasTri = (int)($stAulas->get_result()->fetch_assoc()['c'] ?? 0);
            $possivel = $aulasTri * $totalAlunos;

            $presencas = 0;
            if ($possivel > 0) {
                $stPres->bind_param('iii', $tid, $ano, $tri);
                $stPres->execute();
                $presencas = (int)($stPres->get_result()->fetch_assoc()['c'] ?? 0);
            }

            $pct = $possivel > 0 ? round($presencas / $possivel * 100) : null;
            $porTri[$tri] = ['aulas' => $aulasTri, 'presencas' => $presencas, 'possivel' => $possivel, 'pct' => $pct];
            $aulasTotalTurma    += $aulasTri;
            $presencasTotalTurma += $presencas;
        }

        $possivelTotal = $aulasTotalTurma * $totalAlunos;
        $pctGeral = $possivelTotal > 0 ? round($presencasTotalTurma / $possivelTotal * 100) : null;
        $totalPresencasGeral += $presencasTotalTurma;
        $totalPossivelGeral  += $possivelTotal;

        if ($aulasTotalTurma > 0) {
            $dados[] = [
                'turma_id'     => $tid,
                'nome_turma'   => $nomeTurma,
                'total_alunos' => $totalAlunos,
                'por_trimestre'=> $porTri,
                'pct_geral'    => $pctGeral,
            ];
        }
    }
    $stAlunos->close();
    $stAulas->close();
    $stPres->close();

    $pctGeralGlobal = $totalPossivelGeral > 0 ? round($totalPresencasGeral / $totalPossivelGeral * 100) : null;
    ok(['dados' => $dados, 'trimestres' => $trimestres, 'anos' => $anos, 'ano' => $ano, 'pct_global' => $pctGeralGlobal]);
    exit;
}

/* ─── rel-aluno: frequência individual ──────────────────── */
if ($recurso === 'rel-aluno') {
    $aluno_id = (int)($_GET['aluno_id'] ?? 0);
    $ano      = (int)($_GET['ano']      ?? date('Y'));
    $ano = ($ano >= 2000 && $ano <= 2100) ? $ano : (int)date('Y');

    if ($aluno_id <= 0) { err('Selecione um aluno.'); exit; }

    $st = $conexao->prepare('SELECT id, nome, turma, data_matricula, telefone, status FROM tb_cad_alunos WHERE id = ? LIMIT 1');
    $st->bind_param('i', $aluno_id);
    $st->execute();
    $aluno = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$aluno) { err('Aluno não encontrado.'); exit; }

    $st = $conexao->prepare('SELECT id FROM tb_cad_turmas WHERE nome_turma = ? LIMIT 1');
    $st->bind_param('s', $aluno['turma']);
    $st->execute();
    $turmaRow = $st->get_result()->fetch_assoc();
    $st->close();
    $turma_id = $turmaRow ? (int)$turmaRow['id'] : 0;

    $rY = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
    $anos = [];
    while ($row = $rY->fetch_assoc()) $anos[] = (int)$row['ano'];
    if (!in_array((int)date('Y'), $anos)) array_unshift($anos, (int)date('Y'));

    if ($turma_id <= 0) {
        ok(['aluno' => $aluno, 'anos' => $anos, 'trimestres' => [], 'pct_geral' => null]);
        exit;
    }

    $st = $conexao->prepare('
        SELECT a.id AS aula_id, a.titulo, a.data_aula, t.trimestre
        FROM tb_cad_aulas a
        INNER JOIN tb_cad_temas t ON t.id = a.tema_id
        INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id
        WHERE t.turma_id = ? AND t.ano = ?
        ORDER BY t.trimestre ASC, a.data_aula ASC, a.ordem ASC, a.id ASC
    ');
    $st->bind_param('ii', $turma_id, $ano);
    $st->execute();
    $rAulas = $st->get_result();
    $aulasPorTri = [];
    $aula_ids = [];
    while ($row = $rAulas->fetch_assoc()) {
        $tri = (int)$row['trimestre'];
        $aulasPorTri[$tri][] = ['id' => (int)$row['aula_id'], 'titulo' => $row['titulo'], 'data_aula' => $row['data_aula']];
        $aula_ids[] = (int)$row['aula_id'];
    }
    $st->close();

    $presenteSet = [];
    if (!empty($aula_ids)) {
        $placeholders = implode(',', array_fill(0, count($aula_ids), '?'));
        $types = 'i' . str_repeat('i', count($aula_ids));
        $params = array_merge([$aluno_id], $aula_ids);
        $st = $conexao->prepare("SELECT s.aula_id FROM tb_aula_pratica_presenca p INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id WHERE p.aluno_id = ? AND s.aula_id IN ($placeholders)");
        $st->bind_param($types, ...$params);
        $st->execute();
        $rPres = $st->get_result();
        while ($row = $rPres->fetch_assoc()) $presenteSet[(int)$row['aula_id']] = true;
        $st->close();
    }

    $triArr = [];
    $totalAulas = 0;
    $totalPresente = 0;
    foreach ($aulasPorTri as $tri => $aulas) {
        $detalhes = [];
        $pres = 0;
        foreach ($aulas as $a) {
            $presente = isset($presenteSet[$a['id']]);
            if ($presente) { $pres++; $totalPresente++; }
            $totalAulas++;
            $detalhes[] = ['id' => $a['id'], 'titulo' => $a['titulo'], 'data_aula' => $a['data_aula'], 'presente' => $presente];
        }
        $pctTri = count($aulas) > 0 ? round($pres / count($aulas) * 100) : null;
        $triArr[] = ['trimestre' => $tri, 'aulas' => $detalhes, 'total' => count($aulas), 'presencas' => $pres, 'pct' => $pctTri];
    }

    $pctGeral = $totalAulas > 0 ? round($totalPresente / $totalAulas * 100) : null;
    ok(['aluno' => $aluno, 'anos' => $anos, 'ano' => $ano, 'trimestres' => $triArr, 'pct_geral' => $pctGeral,
        'total_aulas' => $totalAulas, 'total_presencas' => $totalPresente]);
    exit;
}

/* ─── rel-risco: alunos abaixo do limiar ────────────────── */
if ($recurso === 'rel-risco') {
    $ano       = (int)($_GET['ano']       ?? date('Y'));
    $trimestre = (int)($_GET['trimestre'] ?? 0);
    $limiar    = (int)($_GET['limiar']    ?? 75);
    $turma_id  = (int)($_GET['turma_id'] ?? 0);

    $ano    = ($ano >= 2000 && $ano <= 2100) ? $ano : (int)date('Y');
    $limiar = max(1, min(100, $limiar));

    $rY = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
    $anos = [];
    while ($row = $rY->fetch_assoc()) $anos[] = (int)$row['ano'];
    if (!in_array((int)date('Y'), $anos)) array_unshift($anos, (int)date('Y'));

    $rTurmas = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
    $turmasList = [];
    while ($row = $rTurmas->fetch_assoc()) $turmasList[] = $row;

    /* Conta aulas com sessão por turma */
    $sqlAulas = 'SELECT t.turma_id, COUNT(a.id) AS total_aulas FROM tb_cad_aulas a INNER JOIN tb_cad_temas t ON t.id = a.tema_id INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id WHERE t.ano = ?';
    $types = 'i'; $params = [$ano];
    if ($trimestre > 0) { $sqlAulas .= ' AND t.trimestre = ?'; $types .= 'i'; $params[] = $trimestre; }
    if ($turma_id  > 0) { $sqlAulas .= ' AND t.turma_id = ?';  $types .= 'i'; $params[] = $turma_id; }
    $sqlAulas .= ' GROUP BY t.turma_id';
    $stA = $conexao->prepare($sqlAulas);
    $stA->bind_param($types, ...$params);
    $stA->execute();
    $rA = $stA->get_result();

    $aulasPorTurma = [];
    while ($row = $rA->fetch_assoc()) {
        $aulasPorTurma[(int)$row['turma_id']] = (int)$row['total_aulas'];
    }
    $stA->close();

    if (empty($aulasPorTurma)) {
        ok(['alunos' => [], 'has_sessions' => false, 'anos' => $anos, 'turmas' => $turmasList, 'ano' => $ano, 'trimestre' => $trimestre, 'limiar' => $limiar]);
        exit;
    }

    /* Busca alunos ativos nas turmas com aulas */
    $placeholders = implode(',', array_fill(0, count($aulasPorTurma), '?'));
    $turmaIds = array_keys($aulasPorTurma);
    $stAl = $conexao->prepare("SELECT al.id, al.nome, al.turma, al.telefone, tr.id AS turma_id FROM tb_cad_alunos al INNER JOIN tb_cad_turmas tr ON tr.nome_turma COLLATE utf8mb4_general_ci = al.turma COLLATE utf8mb4_general_ci WHERE tr.id IN ($placeholders) AND LOWER(al.status) = 'ativo' ORDER BY al.turma ASC, al.nome ASC");
    $stAl->bind_param(str_repeat('i', count($turmaIds)), ...$turmaIds);
    $stAl->execute();
    $rAl = $stAl->get_result();

    $alunos = [];
    while ($row = $rAl->fetch_assoc()) $alunos[] = $row;
    $stAl->close();

    /* Prepared statement para presenças reutilizável no loop */
    $sqlPres = 'SELECT COUNT(p.id) AS presencas FROM tb_aula_pratica_presenca p INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id INNER JOIN tb_cad_aulas a ON a.id = s.aula_id INNER JOIN tb_cad_temas t ON t.id = a.tema_id WHERE p.aluno_id = ? AND t.turma_id = ? AND t.ano = ?';
    if ($trimestre > 0) $sqlPres .= ' AND t.trimestre = ?';
    $stPres = $conexao->prepare($sqlPres);

    $emRisco = [];
    foreach ($alunos as $al) {
        $tid   = (int)$al['turma_id'];
        $total = $aulasPorTurma[$tid] ?? 0;
        if ($total <= 0) continue;

        $aid = (int)$al['id'];
        if ($trimestre > 0) { $stPres->bind_param('iiii', $aid, $tid, $ano, $trimestre); }
        else { $stPres->bind_param('iii', $aid, $tid, $ano); }
        $stPres->execute();
        $pres = (int)($stPres->get_result()->fetch_assoc()['presencas'] ?? 0);
        $pct  = (int)round($pres / $total * 100);

        if ($pct < $limiar) {
            $emRisco[] = [
                'id'          => $aid,
                'nome'        => $al['nome'],
                'turma'       => $al['turma'],
                'telefone'    => $al['telefone'],
                'presencas'   => $pres,
                'total_aulas' => $total,
                'pct'         => $pct,
            ];
        }
    }
    $stPres->close();

    usort($emRisco, function($a, $b) { return $a['pct'] - $b['pct']; });

    ok([
        'alunos'      => $emRisco,
        'has_sessions'=> true,
        'anos'        => $anos,
        'turmas'      => $turmasList,
        'ano'         => $ano,
        'trimestre'   => $trimestre,
        'limiar'      => $limiar,
    ]);
    exit;
}

/* ─── alunos de uma turma (para select do rel-aluno) ────── */
if ($recurso === 'alunos-turma') {
    $turma_id = (int)($_GET['turma_id'] ?? 0);
    if ($turma_id <= 0) { ok(['alunos' => []]); exit; }
    $st = $conexao->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ? LIMIT 1');
    $st->bind_param('i', $turma_id);
    $st->execute();
    $tRow = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$tRow) { ok(['alunos' => []]); exit; }
    $st2 = $conexao->prepare("SELECT id, nome FROM tb_cad_alunos WHERE turma COLLATE utf8mb4_general_ci = ? AND LOWER(status) = 'ativo' ORDER BY nome ASC");
    $st2->bind_param('s', $tRow['nome_turma']);
    $st2->execute();
    $r = $st2->get_result();
    $rows = [];
    while ($row = $r->fetch_assoc()) $rows[] = $row;
    $st2->close();
    ok(['alunos' => $rows]);
    exit;
}

/* ─── Relatório principal ────────────────────────────────── */
if ($recurso === 'relatorio') {
    $turma_id = (int)($_GET['turma_id'] ?? 0);
    $ano      = (int)($_GET['ano']      ?? date('Y'));

    if ($turma_id <= 0) { err('Selecione uma turma.'); exit; }

    $st = $conexao->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ? LIMIT 1');
    $st->bind_param('i', $turma_id);
    $st->execute();
    $turmaRow = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$turmaRow) { err('Turma não encontrada.'); exit; }
    $nome_turma = $turmaRow['nome_turma'];

    $st = $conexao->prepare("SELECT id, nome FROM tb_cad_alunos WHERE turma COLLATE utf8mb4_general_ci = ? AND LOWER(status) = 'ativo' ORDER BY nome ASC");
    $st->bind_param('s', $nome_turma);
    $st->execute();
    $ra = $st->get_result();
    $alunos = [];
    while ($row = $ra->fetch_assoc()) {
        $alunos[(int)$row['id']] = $row['nome'];
    }
    $st->close();

    $st = $conexao->prepare('
        SELECT a.id AS aula_id, a.titulo AS aula_titulo, a.data_aula, t.trimestre
        FROM tb_cad_aulas a
        INNER JOIN tb_cad_temas t ON t.id = a.tema_id
        INNER JOIN tb_aula_pratica_sessoes s ON s.aula_id = a.id
        WHERE t.turma_id = ? AND t.ano = ?
        ORDER BY t.trimestre ASC, a.data_aula ASC, a.ordem ASC, a.id ASC
    ');
    $st->bind_param('ii', $turma_id, $ano);
    $st->execute();
    $rAulas = $st->get_result();

    $aulasPorTrimestre = [];
    $todasAulas        = [];
    while ($row = $rAulas->fetch_assoc()) {
        $tri = (int)$row['trimestre'];
        $aid = (int)$row['aula_id'];
        $aulasPorTrimestre[$tri][$aid] = $row['aula_titulo'];
        $todasAulas[$aid] = $row['aula_titulo'];
    }
    $st->close();
    ksort($aulasPorTrimestre);

    if (empty($todasAulas)) {
        ok(['nome_turma' => $nome_turma, 'ano' => $ano, 'alunos' => [], 'aulasPorTrimestre' => [], 'presencas' => []]);
        exit;
    }

    $aula_ids = array_keys($todasAulas);
    $placeholders = implode(',', array_fill(0, count($aula_ids), '?'));
    $st = $conexao->prepare("SELECT id AS sessao_id, aula_id FROM tb_aula_pratica_sessoes WHERE aula_id IN ($placeholders)");
    $st->bind_param(str_repeat('i', count($aula_ids)), ...$aula_ids);
    $st->execute();
    $rSessoes = $st->get_result();
    $sessaoPorAula = [];
    $sessaoIds     = [];
    while ($row = $rSessoes->fetch_assoc()) {
        $aid = (int)$row['aula_id'];
        $sid = (int)$row['sessao_id'];
        $sessaoPorAula[$aid][] = $sid;
        $sessaoIds[] = $sid;
    }
    $st->close();

    $presencas = [];
    if (!empty($sessaoIds)) {
        $placeholders2 = implode(',', array_fill(0, count($sessaoIds), '?'));
        $st = $conexao->prepare("SELECT p.aluno_id, s.aula_id FROM tb_aula_pratica_presenca p INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id WHERE p.sessao_id IN ($placeholders2)");
        $st->bind_param(str_repeat('i', count($sessaoIds)), ...$sessaoIds);
        $st->execute();
        $rPres = $st->get_result();
        while ($row = $rPres->fetch_assoc()) {
            $presencas[(int)$row['aluno_id']][(int)$row['aula_id']] = true;
        }
        $st->close();
    }

    $alunosArr = [];
    foreach ($alunos as $aluno_id => $nome) {
        $totalGeral = 0;
        $porTrimestre = [];
        foreach ($aulasPorTrimestre as $tri => $aulas) {
            $totalTri = 0;
            $detalhe  = [];
            foreach ($aulas as $aid => $titulo) {
                $presente = isset($presencas[$aluno_id][$aid]);
                if ($presente) { $totalTri++; $totalGeral++; }
                $detalhe[$aid] = $presente;
            }
            $porTrimestre[$tri] = ['detalhe' => $detalhe, 'total' => $totalTri];
        }
        $alunosArr[] = [
            'id'           => $aluno_id,
            'nome'         => $nome,
            'porTrimestre' => $porTrimestre,
            'totalGeral'   => $totalGeral,
        ];
    }

    $triArr = [];
    foreach ($aulasPorTrimestre as $tri => $aulas) {
        $aulasArr = [];
        foreach ($aulas as $aid => $titulo) {
            $aulasArr[] = ['id' => $aid, 'titulo' => $titulo];
        }
        $triArr[] = ['trimestre' => $tri, 'aulas' => $aulasArr];
    }

    ok([
        'nome_turma' => $nome_turma,
        'ano'        => $ano,
        'alunos'     => $alunosArr,
        'trimestres' => $triArr,
    ]);
    exit;
}

err('Recurso desconhecido.');