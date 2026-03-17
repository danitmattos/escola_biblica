<?php
/**
 * turmas_crud.php — Endpoint AJAX para o CRUD de turmas
 * Aceita: GET (listar/buscar/obter), POST (criar), PUT (editar), DELETE (excluir)
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ── Proteção: apenas usuários autenticados ─────────────────
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/libs/connection.php';

mysqli_report(MYSQLI_REPORT_OFF);

$method = $_SERVER['REQUEST_METHOD'];
// Override de método para suportar PUT via formulários
if ($method === 'POST' && !empty($_GET['_method'])) {
    $override = strtoupper($_GET['_method']);
    if (in_array($override, ['PUT', 'DELETE'], true)) $method = $override;
}

// Garante que a tabela existe
ensureTable($conexao);

switch ($method) {
    case 'GET':    handleGet($conexao);    break;
    case 'POST':   handlePost($conexao);   break;
    case 'PUT':    handlePut($conexao);    break;
    case 'DELETE': handleDelete($conexao); break;
    default:
        http_response_code(405);
        echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
}

// ══════════════════════════════════════════════════════════
// GET — listar todas / obter uma por id
// ══════════════════════════════════════════════════════════
function handleGet($db) {
    // Obter turma única
    if (!empty($_GET['id'])) {
        $id   = (int) $_GET['id'];
        $stmt = $db->prepare('SELECT id, nome_turma FROM tb_cad_turmas WHERE id = ?');
        if (!$stmt) { jsonError(500, $db->error); return; }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row) { jsonError(404, 'Turma não encontrada.'); return; }
        echo json_encode(['ok' => true, 'turma' => $row]);
        return;
    }

    // Listar / buscar
    $busca = trim($_GET['busca'] ?? '');
    if ($busca !== '') {
        $like = '%' . $busca . '%';
        $stmt = $db->prepare('SELECT id, nome_turma FROM tb_cad_turmas WHERE nome_turma LIKE ? ORDER BY nome_turma ASC');
        if (!$stmt) { jsonError(500, $db->error); return; }
        $stmt->bind_param('s', $like);
    } else {
        $stmt = $db->prepare('SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC');
        if (!$stmt) { jsonError(500, $db->error); return; }
    }
    $stmt->execute();
    $result  = $stmt->get_result();
    $turmas  = [];
    while ($row = $result->fetch_assoc()) $turmas[] = $row;
    $stmt->close();

    // Conta alunos por turma
    $counts = [];
    $res = $db->query('SELECT turma, COUNT(*) AS total FROM tb_cad_alunos GROUP BY turma');
    if ($res) {
        while ($r = $res->fetch_assoc()) $counts[$r['turma']] = (int)$r['total'];
    }
    foreach ($turmas as &$t) {
        $t['total_alunos'] = $counts[$t['nome_turma']] ?? 0;
    }
    unset($t);

    echo json_encode(['ok' => true, 'turmas' => $turmas, 'total' => count($turmas)]);
}

// ══════════════════════════════════════════════════════════
// POST — criar turma
// ══════════════════════════════════════════════════════════
function handlePost($db) {
    $data = !empty($_POST) ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

    $nome = mb_substr(trim($data['nome_turma'] ?? ''), 0, 50);
    if ($nome === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => ['nome_turma' => 'O nome da turma é obrigatório.']]);
        return;
    }

    // Verifica duplicata
    $stmt = $db->prepare('SELECT id FROM tb_cad_turmas WHERE nome_turma = ?');
    $stmt->bind_param('s', $nome);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => ['nome_turma' => 'Já existe uma turma com este nome.']]);
        return;
    }
    $stmt->close();

    $stmt = $db->prepare('INSERT INTO tb_cad_turmas (nome_turma) VALUES (?)');
    if (!$stmt) { jsonError(500, $db->error); return; }
    $stmt->bind_param('s', $nome);

    if ($stmt->execute()) {
        $id = $db->insert_id;
        $stmt->close();
        http_response_code(201);
        echo json_encode(['ok' => true, 'msg' => 'Turma cadastrada com sucesso.', 'id' => $id]);
    } else {
        $err = $db->error; $stmt->close();
        jsonError(500, 'Erro ao cadastrar turma: ' . $err);
    }
}

// ══════════════════════════════════════════════════════════
// PUT — editar turma
// ══════════════════════════════════════════════════════════
function handlePut($db) {
    $data = !empty($_POST) ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

    $id   = (int) ($data['id'] ?? 0);
    $nome = mb_substr(trim($data['nome_turma'] ?? ''), 0, 50);

    if (!$id)        { jsonError(400, 'ID inválido.');                              return; }
    if ($nome === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => ['nome_turma' => 'O nome da turma é obrigatório.']]);
        return;
    }

    // Verifica duplicata (exceto a própria)
    $stmt = $db->prepare('SELECT id FROM tb_cad_turmas WHERE nome_turma = ? AND id <> ?');
    $stmt->bind_param('si', $nome, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => ['nome_turma' => 'Já existe outra turma com este nome.']]);
        return;
    }
    $stmt->close();

    // Atualiza também o campo turma nos alunos já vinculados
    $stmtOld = $db->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ?');
    $stmtOld->bind_param('i', $id);
    $stmtOld->execute();
    $rowOld = $stmtOld->get_result()->fetch_assoc();
    $stmtOld->close();
    $nomeAntigo = $rowOld['nome_turma'] ?? '';

    $stmt = $db->prepare('UPDATE tb_cad_turmas SET nome_turma = ? WHERE id = ?');
    if (!$stmt) { jsonError(500, $db->error); return; }
    $stmt->bind_param('si', $nome, $id);

    if ($stmt->execute() && $stmt->affected_rows >= 0) {
        $stmt->close();
        // Propaga renomeação para tb_cad_alunos
        if ($nomeAntigo !== '' && $nomeAntigo !== $nome) {
            $stmtA = $db->prepare('UPDATE tb_cad_alunos SET turma = ? WHERE turma = ?');
            if ($stmtA) { $stmtA->bind_param('ss', $nome, $nomeAntigo); $stmtA->execute(); $stmtA->close(); }
        }
        echo json_encode(['ok' => true, 'msg' => 'Turma atualizada com sucesso.']);
    } else {
        $err = $db->error; $stmt->close();
        jsonError(500, 'Erro ao atualizar turma: ' . $err);
    }
}

// ══════════════════════════════════════════════════════════
// DELETE — excluir turma
// ══════════════════════════════════════════════════════════
function handleDelete($db) {
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) { jsonError(400, 'ID inválido.'); return; }

    // Impede exclusão se houver alunos vinculados
    $stmtT = $db->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ?');
    $stmtT->bind_param('i', $id);
    $stmtT->execute();
    $rowT = $stmtT->get_result()->fetch_assoc();
    $stmtT->close();

    if ($rowT) {
        $nomeTurma = $rowT['nome_turma'];
        $stmtC = $db->prepare('SELECT COUNT(*) AS c FROM tb_cad_alunos WHERE turma = ?');
        $stmtC->bind_param('s', $nomeTurma);
        $stmtC->execute();
        $count = (int) $stmtC->get_result()->fetch_assoc()['c'];
        $stmtC->close();
        if ($count > 0) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'msg' => "Não é possível excluir: existem {$count} aluno(s) nesta turma."]);
            return;
        }
    }

    $stmt = $db->prepare('DELETE FROM tb_cad_turmas WHERE id = ?');
    if (!$stmt) { jsonError(500, $db->error); return; }
    $stmt->bind_param('i', $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        echo json_encode(['ok' => true, 'msg' => 'Turma excluída com sucesso.']);
    } else {
        $stmt->close();
        jsonError(404, 'Turma não encontrada.');
    }
}

// ══════════════════════════════════════════════════════════
// Helpers
// ══════════════════════════════════════════════════════════
function jsonError(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
}

function ensureTable($db): void {
    $db->query("CREATE TABLE IF NOT EXISTS tb_cad_turmas (
        id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        nome_turma VARCHAR(50)  NULL,
        UNIQUE KEY uq_nome_turma (nome_turma)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
}
