<?php
/**
 * aulas_temas_crud.php
 * CRUD de Temas de Aulas e Aulas
 * recurso=tema  → tb_cad_temas
 * recurso=aula  → tb_cad_aulas
 * (omitido)     → tb_cad_temas (listagem padrão)
 */

require_once __DIR__ . '/libs/helpers.php';
/** @var mysqli $conexao */
requireAuth();
csrfCheck();

$method  = $_SERVER['REQUEST_METHOD'];
$recurso = strtolower(trim($_GET['recurso'] ?? ''));

/* ══════════════════════════════════════════════════
   GET
══════════════════════════════════════════════════ */
if ($method === 'GET') {

    /* ── Aula única ── */
    if ($recurso === 'aula') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }
        $st = $conexao->prepare('SELECT * FROM tb_cad_aulas WHERE id = ? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        $st->close();
        $row ? ok(['aula' => $row]) : err('Aula não encontrada.');
        exit;
    }

    /* ── Cronograma: aulas agrupadas por turma ── */
    if ($recurso === 'cronograma') {
        $ano       = (int)($_GET['ano']       ?? date('Y'));
        $trimestre = (int)($_GET['trimestre'] ?? 0);
        $turma_id  = (int)($_GET['turma_id']  ?? 0);
        $ano       = max(2000, min(2100, $ano));

        $sql = "SELECT a.id, a.titulo AS aula_titulo, a.data_aula, a.professor, a.professor_substituto, a.descricao,
                       tm.id AS tema_id, tm.titulo AS tema_titulo, tm.trimestre,
                       tu.id AS turma_id, tu.nome_turma
                FROM tb_cad_aulas a
                INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
                LEFT  JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
                WHERE tm.ano = ?";
        $types  = 'i';
        $params = [$ano];

        if ($trimestre >= 1 && $trimestre <= 4) { $sql .= ' AND tm.trimestre = ?'; $types .= 'i'; $params[] = $trimestre; }
        if ($turma_id > 0)                      { $sql .= ' AND tm.turma_id  = ?'; $types .= 'i'; $params[] = $turma_id; }

        $sql .= ' ORDER BY tu.nome_turma ASC, a.data_aula ASC, a.id ASC';

        $st = $conexao->prepare($sql);
        $st->bind_param($types, ...$params);
        $st->execute();
        $r = $st->get_result();

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
                'professor_substituto' => $row['professor_substituto'],
                'descricao'   => $row['descricao'],
                'tema_titulo' => $row['tema_titulo'],
                'trimestre'   => $row['trimestre'],
                'tema_id'     => $row['tema_id'],
            ];
        }

        $resultado = array_values($turmas);

        $st->close();
        ok(['turmas' => $resultado, 'total' => count($resultado)]);
        exit;
    }

    /* ── Estatísticas de aulas (mês atual vs anterior) ── */
    if ($recurso === 'aulas-stats') {
        $ano_atual = (int)date('Y');
        $mes_atual = (int)date('m');

        $mes_ant  = $mes_atual === 1 ? 12 : $mes_atual - 1;
        $ano_ant  = $mes_atual === 1 ? $ano_atual - 1 : $ano_atual;

        $st1 = $conexao->prepare('SELECT COUNT(*) AS total FROM tb_cad_aulas WHERE YEAR(data_aula) = ? AND MONTH(data_aula) = ?');
        $st1->bind_param('ii', $ano_atual, $mes_atual);
        $st1->execute();
        $atual = (int)($st1->get_result()->fetch_assoc()['total'] ?? 0);
        $st1->close();

        $st2 = $conexao->prepare('SELECT COUNT(*) AS total FROM tb_cad_aulas WHERE YEAR(data_aula) = ? AND MONTH(data_aula) = ?');
        $st2->bind_param('ii', $ano_ant, $mes_ant);
        $st2->execute();
        $anterior = (int)($st2->get_result()->fetch_assoc()['total'] ?? 0);
        $st2->close();

        ok(['atual' => $atual, 'anterior' => $anterior, 'diff' => $atual - $anterior]);
        exit;
    }

    /* ── Próximas aulas do aluno logado ── */
    if ($recurso === 'proximas-aulas') {
        $uid = (int)($_SESSION['usuario_id'] ?? 0);
        if (!$uid) { err('Sem sessão'); exit; }

        // Descobre a turma do aluno (nome) e encontra o turma_id correspondente
        $stA = $conexao->prepare('SELECT turma FROM tb_cad_alunos WHERE id = ?');
        $stA->bind_param('i', $uid);
        $stA->execute();
        $nomeTurma = $stA->get_result()->fetch_assoc()['turma'] ?? '';
        $stA->close();

        $limit = max(1, min(10, (int)($_GET['limit'] ?? 3)));

        if ($nomeTurma !== '') {
            // Busca próximas aulas da turma do aluno
            $stB = $conexao->prepare("
                SELECT a.id, a.titulo, a.data_aula, a.professor, a.professor_substituto,
                       tm.titulo AS tema_titulo, tu.nome_turma
                FROM tb_cad_aulas a
                INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
                INNER JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
                WHERE tu.nome_turma = ? AND a.data_aula >= CURDATE()
                ORDER BY a.data_aula ASC, a.id ASC
                LIMIT ?
            ");
            $stB->bind_param('si', $nomeTurma, $limit);
        } else {
            // Sem turma — mostra qualquer próxima aula
            $stB = $conexao->prepare("
                SELECT a.id, a.titulo, a.data_aula, a.professor, a.professor_substituto,
                       tm.titulo AS tema_titulo, tu.nome_turma
                FROM tb_cad_aulas a
                INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
                LEFT  JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
                WHERE a.data_aula >= CURDATE()
                ORDER BY a.data_aula ASC, a.id ASC
                LIMIT ?
            ");
            $stB->bind_param('i', $limit);
        }

        $stB->execute();
        $rows = $stB->get_result()->fetch_all(MYSQLI_ASSOC);
        $stB->close();

        ok(['aulas' => $rows, 'turma' => $nomeTurma ?: null]);
        exit;
    }

    /* ── Lista de aulas de um tema ── */
    if ($recurso === 'aulas') {
        $tema_id = (int)($_GET['tema_id'] ?? 0);
        if ($tema_id <= 0) { err('tema_id inválido.'); exit; }
        $st = $conexao->prepare('SELECT * FROM tb_cad_aulas WHERE tema_id = ? ORDER BY data_aula ASC, id ASC');
        $st->bind_param('i', $tema_id);
        $st->execute();
        $aulas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        ok(['aulas' => $aulas, 'total' => count($aulas)]);
        exit;
    }

    /* ── Aulas por data (ex.: próximo domingo) ── */
    if ($recurso === 'aulas-data') {
        $data = trim($_GET['data'] ?? '');
        if (!validaData($data)) { err('Data inválida.'); exit; }
        $st = $conexao->prepare("
            SELECT a.id, a.titulo, a.descricao, a.data_aula, a.professor, a.professor_substituto,
                   tm.titulo AS tema_titulo, tm.trimestre,
                   tu.nome_turma
            FROM tb_cad_aulas a
            INNER JOIN tb_cad_temas tm ON tm.id = a.tema_id
            LEFT  JOIN tb_cad_turmas tu ON tu.id = tm.turma_id
            WHERE a.data_aula = ?
            ORDER BY tu.nome_turma ASC, a.id ASC");
        $st->bind_param('s', $data);
        $st->execute();
        $aulas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        ok(['aulas' => $aulas, 'total' => count($aulas), 'data' => $data]);
        exit;
    }

    /* ── Perguntas de uma aula ── */
    if ($recurso === 'perguntas') {
        $aula_id = (int)($_GET['aula_id'] ?? 0);
        if ($aula_id <= 0) { err('aula_id inválido.'); exit; }
        $st = $conexao->prepare('SELECT id, pergunta, resposta, ordem FROM tb_cad_perguntas WHERE aula_id = ? ORDER BY ordem ASC, id ASC');
        $st->bind_param('i', $aula_id);
        $st->execute();
        $perguntas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        ok(['perguntas' => $perguntas]);
        exit;
    }

    /* ── Tema único ── */
    if ($recurso === 'tema') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }
        $st = $conexao->prepare("
            SELECT t.*, tu.nome_turma,
                   (SELECT COUNT(*) FROM tb_cad_aulas a WHERE a.tema_id = t.id) AS total_aulas
            FROM tb_cad_temas t
            LEFT JOIN tb_cad_turmas tu ON tu.id = t.turma_id
            WHERE t.id = ? LIMIT 1");
        $st->bind_param('i', $id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        $st->close();
        $row ? ok(['tema' => $row]) : err('Tema não encontrado.');
        exit;
    }

    /* ── Lista de temas (filtrada) ── */
    $ano       = (int)($_GET['ano']       ?? date('Y'));
    $trimestre = (int)($_GET['trimestre'] ?? 0);
    $turma_id  = (int)($_GET['turma_id']  ?? 0);
    $ano       = max(2000, min(2100, $ano));

    $sql = "SELECT t.*, tu.nome_turma,
                   (SELECT COUNT(*) FROM tb_cad_aulas a WHERE a.tema_id = t.id) AS total_aulas
            FROM tb_cad_temas t
            LEFT JOIN tb_cad_turmas tu ON tu.id = t.turma_id
            WHERE t.ano = ?";
    $types  = 'i';
    $params = [$ano];

    if ($trimestre >= 1 && $trimestre <= 4) { $sql .= ' AND t.trimestre = ?'; $types .= 'i'; $params[] = $trimestre; }
    if ($turma_id  > 0)                     { $sql .= ' AND t.turma_id  = ?'; $types .= 'i'; $params[] = $turma_id; }

    $sql .= ' ORDER BY t.trimestre ASC, t.id ASC';

    $st = $conexao->prepare($sql);
    $st->bind_param($types, ...$params);
    $st->execute();
    $temas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
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
        $titulo    = trim($body['titulo']    ?? '');
        $descricao = trim($body['descricao'] ?? '');
        $data      = trim($body['data_aula'] ?? '');
        $professor = trim($body['professor'] ?? '');
        $professor_substituto = trim($body['professor_substituto'] ?? '');
        $ordem     = (int)($body['ordem'] ?? 0);

        if ($tema_id <= 0 || $titulo === '') { err('Tema e título são obrigatórios.'); exit; }

        $chk = $conexao->prepare('SELECT id FROM tb_cad_temas WHERE id = ? LIMIT 1');
        $chk->bind_param('i', $tema_id);
        $chk->execute();
        if (!$chk->get_result()->fetch_assoc()) { $chk->close(); err('Tema não encontrado.'); exit; }
        $chk->close();

        $data_val = validaData($data) ? $data : null;

        if ($data_val) {
            if ($professor !== '' && $professor === $professor_substituto) {
                err('O titular e o substituto não podem ser a mesma pessoa.'); exit;
            }
            $checkProf = function($p) use ($conexao, $data_val) {
                if ($p === '') return false;
                $st = $conexao->prepare('SELECT id FROM tb_cad_aulas WHERE data_aula = ? AND (professor = ? OR professor_substituto = ?) LIMIT 1');
                if (!$st) return false;
                $st->bind_param('sss', $data_val, $p, $p);
                $st->execute();
                $ret = $st->get_result()->fetch_assoc();
                $st->close();
                return $ret ? true : false;
            };

            if ($checkProf($professor)) {
                err('O professor titular já está escalado para outra aula na mesma data.'); exit;
            }
            if ($checkProf($professor_substituto)) {
                err('O professor substituto já está escalado para outra aula na mesma data.'); exit;
            }
        }

        $st = $conexao->prepare('INSERT INTO tb_cad_aulas (tema_id, titulo, descricao, data_aula, professor, professor_substituto, ordem) VALUES (?,?,?,?,?,?,?)');
        if (!$st) { err('Erro ao criar aula.'); exit; }
        $st->bind_param('isssssi', $tema_id, $titulo, $descricao, $data_val, $professor, $professor_substituto, $ordem);
        if (!$st->execute()) { $st->close(); err('Erro ao criar aula.'); exit; }
        $aula_id = (int)$conexao->insert_id;
        $st->close();

        $perguntas = $body['perguntas'] ?? [];
        if (is_array($perguntas)) {
            $stP = $conexao->prepare('INSERT INTO tb_cad_perguntas (aula_id, pergunta, resposta, ordem) VALUES (?,?,?,?)');
            $n = min(5, count($perguntas));
            for ($i = 0; $i < $n; $i++) {
                $perg = trim($perguntas[$i]['pergunta'] ?? '');
                $resp = trim($perguntas[$i]['resposta'] ?? '');
                if ($perg === '') continue;
                $ord = $i + 1;
                $stP->bind_param('issi', $aula_id, $perg, $resp, $ord);
                $stP->execute();
            }
            $stP->close();
        }
        ok(['msg' => 'Aula criada com sucesso.', 'id' => $aula_id]);
        exit;
    }

    /* ── Novo tema ── */
    $titulo    = trim($body['titulo']    ?? '');
    $descricao = trim($body['descricao'] ?? '');
    $trimestre = (int)($body['trimestre'] ?? 0);
    $turma_id  = (int)($body['turma_id']  ?? 0);
    $ano       = (int)($body['ano']       ?? date('Y'));

    if ($titulo === '' || $trimestre < 1 || $trimestre > 4) {
        err('Título e trimestre são obrigatórios.');
        exit;
    }
    $ano       = max(2000, min(2100, $ano));
    $turma_val = $turma_id > 0 ? $turma_id : null;
    $criado_por = $_SESSION['usuario'];

    $st = $conexao->prepare('INSERT INTO tb_cad_temas (titulo, descricao, trimestre, turma_id, ano, criado_por) VALUES (?,?,?,?,?,?)');
    if (!$st) { err('Erro ao criar tema.'); exit; }
    $st->bind_param('ssiiss', $titulo, $descricao, $trimestre, $turma_val, $ano, $criado_por);
    if ($st->execute()) {
        $newId = (int)$conexao->insert_id;
        $st->close();
        ok(['msg' => 'Tema criado com sucesso.', 'id' => $newId]);
    } else {
        $st->close();
        err('Erro ao criar tema.');
    }
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
        $titulo    = trim($body['titulo']    ?? '');
        $descricao = trim($body['descricao'] ?? '');
        $data      = trim($body['data_aula'] ?? '');
        $professor = trim($body['professor'] ?? '');
        $professor_substituto = trim($body['professor_substituto'] ?? '');
        $ordem     = (int)($body['ordem'] ?? 0);
        if ($titulo === '') { err('Título é obrigatório.'); exit; }
        $data_val = validaData($data) ? $data : null;

        if ($data_val) {
            if ($professor !== '' && $professor === $professor_substituto) {
                err('O titular e o substituto não podem ser a mesma pessoa.'); exit;
            }
            $checkProf = function($p) use ($conexao, $data_val, $id) {
                if ($p === '') return false;
                $st = $conexao->prepare('SELECT id FROM tb_cad_aulas WHERE data_aula = ? AND (professor = ? OR professor_substituto = ?) AND id != ? LIMIT 1');
                if (!$st) return false;
                $st->bind_param('sssi', $data_val, $p, $p, $id);
                $st->execute();
                $ret = $st->get_result()->fetch_assoc();
                $st->close();
                return $ret ? true : false;
            };

            if ($checkProf($professor)) {
                err('O professor titular já está escalado para outra aula na mesma data.'); exit;
            }
            if ($checkProf($professor_substituto)) {
                err('O professor substituto já está escalado para outra aula na mesma data.'); exit;
            }
        }

        $st = $conexao->prepare('UPDATE tb_cad_aulas SET titulo=?, descricao=?, data_aula=?, professor=?, professor_substituto=?, ordem=? WHERE id=?');
        $st->bind_param('sssssii', $titulo, $descricao, $data_val, $professor, $professor_substituto, $ordem, $id);
        if (!$st->execute()) { $st->close(); err('Erro ao atualizar aula.'); exit; }
        $st->close();

        /* Recriar perguntas */
        $stDel = $conexao->prepare('DELETE FROM tb_cad_perguntas WHERE aula_id = ?');
        $stDel->bind_param('i', $id);
        $stDel->execute();
        $stDel->close();

        $perguntas = $body['perguntas'] ?? [];
        if (is_array($perguntas)) {
            $stP = $conexao->prepare('INSERT INTO tb_cad_perguntas (aula_id, pergunta, resposta, ordem) VALUES (?,?,?,?)');
            $n = min(5, count($perguntas));
            for ($i = 0; $i < $n; $i++) {
                $perg = trim($perguntas[$i]['pergunta'] ?? '');
                $resp = trim($perguntas[$i]['resposta'] ?? '');
                if ($perg === '') continue;
                $ord = $i + 1;
                $stP->bind_param('issi', $id, $perg, $resp, $ord);
                $stP->execute();
            }
            $stP->close();
        }
        ok(['msg' => 'Aula atualizada.']);
        exit;
    }

    /* ── Editar tema ── */
    $titulo    = trim($body['titulo']    ?? '');
    $descricao = trim($body['descricao'] ?? '');
    $trimestre = (int)($body['trimestre'] ?? 0);
    $turma_id  = (int)($body['turma_id']  ?? 0);
    $ano       = (int)($body['ano']       ?? date('Y'));

    if ($titulo === '' || $trimestre < 1 || $trimestre > 4) {
        err('Título e trimestre são obrigatórios.');
        exit;
    }
    $ano       = max(2000, min(2100, $ano));
    $turma_val = $turma_id > 0 ? $turma_id : null;

    $st = $conexao->prepare('UPDATE tb_cad_temas SET titulo=?, descricao=?, trimestre=?, turma_id=?, ano=? WHERE id=?');
    $st->bind_param('ssiiii', $titulo, $descricao, $trimestre, $turma_val, $ano, $id);
    if ($st->execute()) {
        $st->close();
        ok(['msg' => 'Tema atualizado.']);
    } else {
        $st->close();
        err('Erro ao atualizar tema.');
    }
    exit;
}

/* ══════════════════════════════════════════════════
   DELETE
══════════════════════════════════════════════════ */
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { err('ID inválido.'); exit; }

    if ($recurso === 'aula') {
        $st1 = $conexao->prepare('DELETE FROM tb_cad_perguntas WHERE aula_id = ?');
        $st1->bind_param('i', $id);
        $st1->execute();
        $st1->close();

        $st2 = $conexao->prepare('DELETE FROM tb_cad_aulas WHERE id = ?');
        $st2->bind_param('i', $id);
        $st2->execute();
        $st2->affected_rows > 0
            ? ok(['msg' => 'Aula excluída.'])
            : err('Aula não encontrada.');
        $st2->close();
        exit;
    }

    /* Exclui tema + todas as suas aulas + perguntas */
    $st1 = $conexao->prepare('DELETE p FROM tb_cad_perguntas p INNER JOIN tb_cad_aulas a ON a.id = p.aula_id WHERE a.tema_id = ?');
    $st1->bind_param('i', $id);
    $st1->execute();
    $st1->close();

    $st2 = $conexao->prepare('DELETE FROM tb_cad_aulas WHERE tema_id = ?');
    $st2->bind_param('i', $id);
    $st2->execute();
    $st2->close();

    $st3 = $conexao->prepare('DELETE FROM tb_cad_temas WHERE id = ?');
    $st3->bind_param('i', $id);
    $st3->execute();
    $st3->affected_rows > 0
        ? ok(['msg' => 'Tema e suas aulas foram excluídos.'])
        : err('Tema não encontrado.');
    $st3->close();
    exit;
}

err('Método não suportado.');
