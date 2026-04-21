<?php
/**
 * certificados_crud.php
 * API para emissão de certificados trimestrais e anuais.
 * Consulta presença, pontuação (Aula na Prática) e temas estudados.
 */

require_once __DIR__ . '/libs/helpers.php';
/** @var mysqli $conexao */
requireAuth();
csrfCheck();

$recurso = strtolower(trim($_GET['recurso'] ?? ''));

/* ─── GET: turmas ──────────────────────────────────── */
if ($recurso === 'turmas') {
    $r = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
    $rows = [];
    while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
    ok(['turmas' => $rows]);
    exit;
}

/* ─── GET: anos ────────────────────────────────────── */
if ($recurso === 'anos') {
    $rY = $conexao->query("SELECT DISTINCT ano FROM tb_cad_temas ORDER BY ano DESC");
    $anos = [];
    while ($row = $rY->fetch_assoc()) $anos[] = (int)$row['ano'];
    if (!$anos) $anos[] = (int)date('Y');
    ok(['anos' => $anos]);
    exit;
}

/* ─── GET: dados do certificado ────────────────────── */
if ($recurso === 'dados') {
    $turma_id  = (int)($_GET['turma_id'] ?? 0);
    $ano       = (int)($_GET['ano']      ?? date('Y'));
    $tipo      = in_array($_GET['tipo'] ?? '', ['trimestral', 'anual']) ? $_GET['tipo'] : 'anual';
    $trimestre = (int)($_GET['trimestre'] ?? 0);

    if (!$turma_id) { err('Selecione uma turma.'); exit; }
    if ($tipo === 'trimestral' && ($trimestre < 1 || $trimestre > 4)) {
        err('Selecione um trimestre válido.');
        exit;
    }

    /* Nome da turma */
    $stT = $conexao->prepare("SELECT nome_turma FROM tb_cad_turmas WHERE id = ?");
    $stT->bind_param('i', $turma_id);
    $stT->execute();
    $nomeTurma = $stT->get_result()->fetch_assoc()['nome_turma'] ?? '';
    $stT->close();
    if (!$nomeTurma) { err('Turma não encontrada.'); exit; }

    /* Alunos da turma */
    $stAl = $conexao->prepare(
        "SELECT id, nome FROM tb_cad_alunos
         WHERE turma COLLATE utf8mb4_general_ci = ? COLLATE utf8mb4_general_ci
           AND status = 'ativo'
         ORDER BY nome ASC"
    );
    $stAl->bind_param('s', $nomeTurma);
    $stAl->execute();
    $rAl = $stAl->get_result();
    $alunos = [];
    while ($a = $rAl->fetch_assoc()) $alunos[] = $a;
    $stAl->close();

    if (!$alunos) { err('Nenhum aluno ativo nesta turma.'); exit; }

    /* ── Trimestres a consultar ── */
    $trimestres = ($tipo === 'trimestral') ? [$trimestre] : [1, 2, 3, 4];

    /* ── Total de aulas possíveis (por trimestre) ── */
    $sqlAulas = "SELECT COUNT(a.id) AS c
                 FROM tb_cad_aulas a
                 INNER JOIN tb_cad_temas t ON t.id = a.tema_id
                 WHERE t.turma_id = ? AND t.ano = ? AND t.trimestre = ?";
    $stAulas = $conexao->prepare($sqlAulas);

    $totalAulasPorTri = [];
    foreach ($trimestres as $tri) {
        $stAulas->bind_param('iii', $turma_id, $ano, $tri);
        $stAulas->execute();
        $totalAulasPorTri[$tri] = (int)($stAulas->get_result()->fetch_assoc()['c'] ?? 0);
    }
    $stAulas->close();

    $totalAulasGeral = array_sum($totalAulasPorTri);

    /* ── Presença por aluno (sessões de Aula Prática vinculadas) ── */
    $triWhere = count($trimestres) === 1
        ? "AND t.trimestre = " . (int)$trimestres[0]
        : "AND t.trimestre BETWEEN 1 AND 4";

    $sqlPresenca = "
        SELECT p.aluno_id,
               COUNT(DISTINCT s.id) AS presencas
        FROM tb_aula_pratica_presenca p
        INNER JOIN tb_aula_pratica_sessoes s ON s.id = p.sessao_id
        INNER JOIN tb_cad_aulas a ON a.id = s.aula_id
        INNER JOIN tb_cad_temas t ON t.id = a.tema_id
        WHERE t.turma_id = ? AND t.ano = ? $triWhere
        GROUP BY p.aluno_id";
    $stPres = $conexao->prepare($sqlPresenca);
    $stPres->bind_param('ii', $turma_id, $ano);
    $stPres->execute();
    $rPres = $stPres->get_result();
    $presMap = [];
    while ($r = $rPres->fetch_assoc()) $presMap[(int)$r['aluno_id']] = (int)$r['presencas'];
    $stPres->close();

    /* ── Pontuação (Aula na Prática) por aluno ── */
    $sqlPontos = "
        SELECT r.aluno_id,
               SUM(r.pontos) AS total_pontos,
               COUNT(r.id) AS total_respostas,
               SUM(CASE WHEN r.tipo='sem_leitura' THEN 1 ELSE 0 END) AS sem_leitura,
               SUM(CASE WHEN r.tipo='com_leitura' THEN 1 ELSE 0 END) AS com_leitura
        FROM tb_aula_pratica_respostas r
        INNER JOIN tb_aula_pratica_sessoes s ON s.id = r.sessao_id
        INNER JOIN tb_cad_aulas a ON a.id = s.aula_id
        INNER JOIN tb_cad_temas t ON t.id = a.tema_id
        WHERE t.turma_id = ? AND t.ano = ? $triWhere
        GROUP BY r.aluno_id";
    $stPon = $conexao->prepare($sqlPontos);
    $stPon->bind_param('ii', $turma_id, $ano);
    $stPon->execute();
    $rPon = $stPon->get_result();
    $pontMap = [];
    while ($r = $rPon->fetch_assoc()) $pontMap[(int)$r['aluno_id']] = $r;
    $stPon->close();

    /* ── Temas estudados ── */
    $sqlTemas = "SELECT t.trimestre, t.titulo, COUNT(a.id) AS total_aulas
                 FROM tb_cad_temas t
                 LEFT JOIN tb_cad_aulas a ON a.tema_id = t.id
                 WHERE t.turma_id = ? AND t.ano = ? $triWhere
                 GROUP BY t.id
                 ORDER BY t.trimestre, t.titulo";
    $stTe = $conexao->prepare($sqlTemas);
    $stTe->bind_param('ii', $turma_id, $ano);
    $stTe->execute();
    $rTe = $stTe->get_result();
    $temas = [];
    while ($r = $rTe->fetch_assoc()) $temas[] = $r;
    $stTe->close();

    /* ── Montar resultado por aluno ── */
    $resultado = [];
    foreach ($alunos as $al) {
        $aid = (int)$al['id'];
        $presencas = $presMap[$aid] ?? 0;
        $pctFreq   = $totalAulasGeral > 0 ? round(($presencas / $totalAulasGeral) * 100) : null;

        $pont = $pontMap[$aid] ?? null;
        $totalPontos = $pont ? (int)$pont['total_pontos'] : 0;
        $totalResp   = $pont ? (int)$pont['total_respostas'] : 0;
        $semLeitura  = $pont ? (int)$pont['sem_leitura'] : 0;
        $comLeitura  = $pont ? (int)$pont['com_leitura'] : 0;

        /* Conceito baseado em frequência + pontuação */
        $nota = calcConceito($pctFreq, $totalPontos, $totalResp);

        $resultado[] = [
            'aluno_id'       => $aid,
            'aluno_nome'     => $al['nome'],
            'presencas'      => $presencas,
            'total_aulas'    => $totalAulasGeral,
            'pct_freq'       => $pctFreq,
            'total_pontos'   => $totalPontos,
            'total_respostas'=> $totalResp,
            'sem_leitura'    => $semLeitura,
            'com_leitura'    => $comLeitura,
            'conceito'       => $nota['conceito'],
            'desempenho'     => $nota['desempenho'],
        ];
    }

    /* Ordena por conceito (A > B > C > D) e depois por nome */
    usort($resultado, function ($a, $b) {
        $ordem = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
        $oa = $ordem[$a['conceito']] ?? 5;
        $ob = $ordem[$b['conceito']] ?? 5;
        return $oa !== $ob ? $oa - $ob : strcmp($a['aluno_nome'], $b['aluno_nome']);
    });

    $triLabels = ['1' => '1º Trimestre', '2' => '2º Trimestre', '3' => '3º Trimestre', '4' => '4º Trimestre'];
    $periodo = ($tipo === 'trimestral')
        ? $triLabels[$trimestre] . ' de ' . $ano
        : 'Ano Letivo ' . $ano;

    ok([
        'turma'       => $nomeTurma,
        'periodo'     => $periodo,
        'tipo'        => $tipo,
        'ano'         => $ano,
        'trimestre'   => $trimestre,
        'total_aulas' => $totalAulasGeral,
        'temas'       => $temas,
        'alunos'      => $resultado,
    ]);
    exit;
}

err('Recurso não encontrado.', 404);

/* ═══════════════════════════════════════════════════════════
   Funções auxiliares
   ═══════════════════════════════════════════════════════════ */

/**
 * Calcula conceito e descrição de desempenho baseado em:
 *   - Frequência (peso 60%)
 *   - Participação: pontuação e respostas (peso 40%)
 */
function calcConceito(?int $pctFreq, int $pontos, int $respostas): array {
    $freqScore = $pctFreq ?? 0;

    /* Normaliza participação: cada resposta sem leitura vale 2, com leitura 1.
       Referência ideal: ≥10 pontos = 100% de participação */
    $partScore = min(100, ($pontos / 10) * 100);

    $score = ($freqScore * 0.6) + ($partScore * 0.4);

    if ($score >= 85) return ['conceito' => 'A', 'desempenho' => 'Excelente'];
    if ($score >= 70) return ['conceito' => 'B', 'desempenho' => 'Bom'];
    if ($score >= 50) return ['conceito' => 'C', 'desempenho' => 'Regular'];
    return ['conceito' => 'D', 'desempenho' => 'Insuficiente'];
}
