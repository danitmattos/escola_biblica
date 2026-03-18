<?php
/**
 * aulas_temas_crud.php
 * CRUD de Temas de Aulas e Aulas
 * recurso=tema  → tb_cad_temas
 * recurso=aula  → tb_cad_aulas
 * (omitido)     → tb_cad_temas (listagem padrão)
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/libs/connection.php';
mysqli_report(MYSQLI_REPORT_OFF);

$method  = $_SERVER['REQUEST_METHOD'];
$recurso = strtolower(trim($_GET['recurso'] ?? ''));

/* ─── helpers ─────────────────────────────────────── */
function esc($db, $v) { return mysqli_real_escape_string($db, trim((string)($v ?? ''))); }
function ok($d = [])  { echo json_encode(array_merge(['ok' => true], $d)); }
function err($m)      { echo json_encode(['ok' => false, 'msg' => $m]); }

function validaData($s) {
    return $s !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);
}

/* ══════════════════════════════════════════════════
   GET
══════════════════════════════════════════════════ */
if ($method === 'GET') {

    /* ── Aula única ── */
    if ($recurso === 'aula') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }
        $r   = mysqli_query($conexao, "SELECT * FROM tb_cad_aulas WHERE id = $id LIMIT 1");
        $row = mysqli_fetch_assoc($r);
        $row ? ok(['aula' => $row]) : err('Aula não encontrada.');
        exit;
    }

    /* ── Cronograma: aulas agrupadas por turma ── */
    if ($recurso === 'cronograma') {
        $ano       = (int)($_GET['ano']       ?? date('Y'));
        $trimestre = (int)($_GET['trimestre'] ?? 0);
        $turma_id  = (int)($_GET['turma_id']  ?? 0);
        $ano       = max(2000, min(2100, $ano));

        $where = "WHERE tm.ano = $ano";
        if ($trimestre >= 1 && $trimestre <= 4) $where .= " AND tm.trimestre = $trimestre";
        if ($turma_id > 0)                      $where .= " AND tm.turma_id  = $turma_id";

        $r = mysqli_query($conexao, "
            SELECT a.id, a.titulo AS aula_titulo, a.data_aula, a.professor, a.descricao,
                   tm.id AS tema_id, tm.titulo AS tema_titulo, tm.trimestre,
                   tu.id AS turma_id, tu.nome_turma
            FROM tb_cad_aulas a
            INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
            LEFT  JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
            $where
            ORDER BY tu.nome_turma ASC, a.data_aula ASC, a.id ASC");

        $turmas = [];
        while ($row = mysqli_fetch_assoc($r)) {
            $tid  = $row['turma_id'] ?? 0;
            $nome = $row['nome_turma'] ?? 'Sem turma';
            if (!isset($turmas[$tid])) {
                $turmas[$tid] = ['nome_turma' => $nome, 'aulas' => []];
            }
            $turmas[$tid]['aulas'][] = [
                'id'          => $row['id'],
                'aula_titulo' => $row['aula_titulo'],
                'data_aula'   => $row['data_aula'],
                'professor'   => $row['professor'],
                'descricao'   => $row['descricao'],
                'tema_titulo' => $row['tema_titulo'],
                'trimestre'   => $row['trimestre'],
                'tema_id'     => $row['tema_id'],
            ];
        }

        // reindexar para array
        $resultado = array_values(array_map(function($t) {
            return $t;
        }, $turmas));

        ok(['turmas' => $resultado, 'total' => count($resultado)]);
        exit;
    }

    /* ── Estatísticas de aulas (mês atual vs anterior) ── */
    if ($recurso === 'aulas-stats') {
        $ano_atual = (int)date('Y');
        $mes_atual = (int)date('m');

        $mes_ant  = $mes_atual === 1 ? 12 : $mes_atual - 1;
        $ano_ant  = $mes_atual === 1 ? $ano_atual - 1 : $ano_atual;

        $r1  = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM tb_cad_aulas
                WHERE YEAR(data_aula) = $ano_atual AND MONTH(data_aula) = $mes_atual");
        $r2  = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM tb_cad_aulas
                WHERE YEAR(data_aula) = $ano_ant  AND MONTH(data_aula) = $mes_ant");
        $atual   = (int)(mysqli_fetch_assoc($r1)['total'] ?? 0);
        $anterior = (int)(mysqli_fetch_assoc($r2)['total'] ?? 0);

        ok(['atual' => $atual, 'anterior' => $anterior, 'diff' => $atual - $anterior]);
        exit;
    }

    /* ── Lista de aulas de um tema ── */
    if ($recurso === 'aulas') {
        $tema_id = (int)($_GET['tema_id'] ?? 0);
        if ($tema_id <= 0) { err('tema_id inválido.'); exit; }
        $r     = mysqli_query($conexao, "SELECT * FROM tb_cad_aulas WHERE tema_id = $tema_id ORDER BY data_aula ASC, id ASC");
        $aulas = [];
        while ($row = mysqli_fetch_assoc($r)) $aulas[] = $row;
        ok(['aulas' => $aulas, 'total' => count($aulas)]);
        exit;
    }

    /* ── Aulas por data (ex.: próximo domingo) ── */
    if ($recurso === 'aulas-data') {
        $data = esc($conexao, $_GET['data'] ?? '');
        if (!validaData($data)) { err('Data inválida.'); exit; }
        $r = mysqli_query($conexao, "
            SELECT a.id, a.titulo, a.descricao, a.data_aula, a.professor,
                   tm.titulo AS tema_titulo, tm.trimestre,
                   tu.nome_turma
            FROM tb_cad_aulas a
            INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
            LEFT  JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
            WHERE a.data_aula = '$data'
            ORDER BY tu.nome_turma ASC, a.id ASC");
        $aulas = [];
        while ($row = mysqli_fetch_assoc($r)) $aulas[] = $row;
        ok(['aulas' => $aulas, 'total' => count($aulas), 'data' => $data]);
        exit;
    }

    /* ── Perguntas de uma aula ── */
    if ($recurso === 'perguntas') {
        $aula_id = (int)($_GET['aula_id'] ?? 0);
        if ($aula_id <= 0) { err('aula_id inválido.'); exit; }
        $r = mysqli_query($conexao, "SELECT id, pergunta, resposta, ordem FROM tb_cad_perguntas WHERE aula_id = $aula_id ORDER BY ordem ASC, id ASC");
        $perguntas = [];
        while ($row = mysqli_fetch_assoc($r)) $perguntas[] = $row;
        ok(['perguntas' => $perguntas]);
        exit;
    }

    /* ── Tema único ── */
    if ($recurso === 'tema') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }
        $r = mysqli_query($conexao, "
            SELECT t.*, tu.nome_turma,
                   (SELECT COUNT(*) FROM tb_cad_aulas a WHERE a.tema_id = t.id) AS total_aulas
            FROM tb_cad_temas t
            LEFT JOIN tb_cad_turmas tu ON tu.id = t.turma_id
            WHERE t.id = $id LIMIT 1");
        $row = mysqli_fetch_assoc($r);
        $row ? ok(['tema' => $row]) : err('Tema não encontrado.');
        exit;
    }

    /* ── Lista de temas (filtrada) ── */
    $ano       = (int)($_GET['ano']       ?? date('Y'));
    $trimestre = (int)($_GET['trimestre'] ?? 0);
    $turma_id  = (int)($_GET['turma_id']  ?? 0);
    $ano       = max(2000, min(2100, $ano));

    $where = "WHERE t.ano = $ano";
    if ($trimestre >= 1 && $trimestre <= 4) $where .= " AND t.trimestre = $trimestre";
    if ($turma_id  > 0)                     $where .= " AND t.turma_id  = $turma_id";

    $r = mysqli_query($conexao, "
        SELECT t.*, tu.nome_turma,
               (SELECT COUNT(*) FROM tb_cad_aulas a WHERE a.tema_id = t.id) AS total_aulas
        FROM tb_cad_temas t
        LEFT JOIN tb_cad_turmas tu ON tu.id = t.turma_id
        $where
        ORDER BY t.trimestre ASC, t.id ASC");

    $temas = [];
    while ($row = mysqli_fetch_assoc($r)) $temas[] = $row;
    ok(['temas' => $temas, 'total' => count($temas)]);
    exit;
}

/* ══════════════════════════════════════════════════
   POST — criar
══════════════════════════════════════════════════ */
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    /* ── Nova aula ── */
    if ($recurso === 'aula') {
        $tema_id   = (int)($body['tema_id']   ?? 0);
        $titulo    = esc($conexao, $body['titulo']    ?? '');
        $descricao = esc($conexao, $body['descricao'] ?? '');
        $data      = esc($conexao, $body['data_aula'] ?? '');
        $professor = esc($conexao, $body['professor'] ?? '');
        $ordem     = (int)($body['ordem'] ?? 0);

        if ($tema_id <= 0 || $titulo === '') { err('Tema e título são obrigatórios.'); exit; }

        $chk = mysqli_query($conexao, "SELECT id FROM tb_cad_temas WHERE id = $tema_id LIMIT 1");
        if (!mysqli_fetch_assoc($chk)) { err('Tema não encontrado.'); exit; }

        $data_sql = validaData($data) ? "'$data'" : 'NULL';
        $ok = mysqli_query($conexao, "
            INSERT INTO tb_cad_aulas (tema_id, titulo, descricao, data_aula, professor, ordem)
            VALUES ($tema_id, '$titulo', '$descricao', $data_sql, '$professor', $ordem)");
        if (!$ok) { err('Erro ao criar aula: ' . mysqli_error($conexao)); exit; }

        $aula_id   = (int)mysqli_insert_id($conexao);
        $perguntas = $body['perguntas'] ?? [];
        if (is_array($perguntas)) {
            $n = min(5, count($perguntas));
            for ($i = 0; $i < $n; $i++) {
                $perg = esc($conexao, $perguntas[$i]['pergunta'] ?? '');
                $resp = esc($conexao, $perguntas[$i]['resposta'] ?? '');
                if ($perg === '') continue;
                $ord = $i + 1;
                mysqli_query($conexao, "INSERT INTO tb_cad_perguntas (aula_id, pergunta, resposta, ordem) VALUES ($aula_id, '$perg', '$resp', $ord)");
            }
        }
        ok(['msg' => 'Aula criada com sucesso.', 'id' => $aula_id]);
        exit;
    }

    /* ── Novo tema ── */
    $titulo    = esc($conexao, $body['titulo']    ?? '');
    $descricao = esc($conexao, $body['descricao'] ?? '');
    $trimestre = (int)($body['trimestre'] ?? 0);
    $turma_id  = (int)($body['turma_id']  ?? 0);
    $ano       = (int)($body['ano']       ?? date('Y'));

    if ($titulo === '' || $trimestre < 1 || $trimestre > 4) {
        err('Título e trimestre são obrigatórios.');
        exit;
    }
    $ano       = max(2000, min(2100, $ano));
    $turma_sql = $turma_id > 0 ? $turma_id : 'NULL';
    $criado_por = esc($conexao, $_SESSION['usuario']);

    $ok = mysqli_query($conexao, "
        INSERT INTO tb_cad_temas (titulo, descricao, trimestre, turma_id, ano, criado_por)
        VALUES ('$titulo', '$descricao', $trimestre, $turma_sql, $ano, '$criado_por')");
    $ok
        ? ok(['msg' => 'Tema criado com sucesso.', 'id' => (int)mysqli_insert_id($conexao)])
        : err('Erro ao criar tema: ' . mysqli_error($conexao));
    exit;
}

/* ══════════════════════════════════════════════════
   PUT — atualizar
══════════════════════════════════════════════════ */
if ($method === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $id   = (int)($body['id'] ?? 0);
    if ($id <= 0) { err('ID inválido.'); exit; }

    /* ── Editar aula ── */
    if ($recurso === 'aula') {
        $titulo    = esc($conexao, $body['titulo']    ?? '');
        $descricao = esc($conexao, $body['descricao'] ?? '');
        $data      = esc($conexao, $body['data_aula'] ?? '');
        $professor = esc($conexao, $body['professor'] ?? '');
        $ordem     = (int)($body['ordem'] ?? 0);
        if ($titulo === '') { err('Título é obrigatório.'); exit; }
        $data_sql = validaData($data) ? "'$data'" : 'NULL';
        $ok = mysqli_query($conexao, "
            UPDATE tb_cad_aulas
            SET titulo='$titulo', descricao='$descricao', data_aula=$data_sql,
                professor='$professor', ordem=$ordem
            WHERE id=$id");
        if (!($ok && mysqli_affected_rows($conexao) >= 0)) {
            err('Erro ao atualizar: ' . mysqli_error($conexao)); exit;
        }

        $perguntas = $body['perguntas'] ?? [];
        mysqli_query($conexao, "DELETE FROM tb_cad_perguntas WHERE aula_id = $id");
        if (is_array($perguntas)) {
            $n = min(5, count($perguntas));
            for ($i = 0; $i < $n; $i++) {
                $perg = esc($conexao, $perguntas[$i]['pergunta'] ?? '');
                $resp = esc($conexao, $perguntas[$i]['resposta'] ?? '');
                if ($perg === '') continue;
                $ord = $i + 1;
                mysqli_query($conexao, "INSERT INTO tb_cad_perguntas (aula_id, pergunta, resposta, ordem) VALUES ($id, '$perg', '$resp', $ord)");
            }
        }
        ok(['msg' => 'Aula atualizada.']);
        exit;
    }

    /* ── Editar tema ── */
    $titulo    = esc($conexao, $body['titulo']    ?? '');
    $descricao = esc($conexao, $body['descricao'] ?? '');
    $trimestre = (int)($body['trimestre'] ?? 0);
    $turma_id  = (int)($body['turma_id']  ?? 0);
    $ano       = (int)($body['ano']       ?? date('Y'));

    if ($titulo === '' || $trimestre < 1 || $trimestre > 4) {
        err('Título e trimestre são obrigatórios.');
        exit;
    }
    $ano       = max(2000, min(2100, $ano));
    $turma_sql = $turma_id > 0 ? $turma_id : 'NULL';

    $ok = mysqli_query($conexao, "
        UPDATE tb_cad_temas
        SET titulo='$titulo', descricao='$descricao', trimestre=$trimestre,
            turma_id=$turma_sql, ano=$ano
        WHERE id=$id");
    ($ok && mysqli_affected_rows($conexao) >= 0)
        ? ok(['msg' => 'Tema atualizado.'])
        : err('Erro ao atualizar: ' . mysqli_error($conexao));
    exit;
}

/* ══════════════════════════════════════════════════
   DELETE
══════════════════════════════════════════════════ */
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { err('ID inválido.'); exit; }

    if ($recurso === 'aula') {
        mysqli_query($conexao, "DELETE FROM tb_cad_perguntas WHERE aula_id = $id");
        $ok = mysqli_query($conexao, "DELETE FROM tb_cad_aulas WHERE id = $id");
        ($ok && mysqli_affected_rows($conexao) > 0)
            ? ok(['msg' => 'Aula excluída.'])
            : err('Aula não encontrada.');
        exit;
    }

    /* Exclui tema + todas as suas aulas */
    mysqli_query($conexao, "DELETE FROM tb_cad_aulas WHERE tema_id = $id");
    $ok = mysqli_query($conexao, "DELETE FROM tb_cad_temas WHERE id = $id");
    ($ok && mysqli_affected_rows($conexao) > 0)
        ? ok(['msg' => 'Tema e suas aulas foram excluídos.'])
        : err('Tema não encontrado.');
    exit;
}

err('Método não suportado.');
