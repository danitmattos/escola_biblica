<?php
/**
 * alunos_crud.php — Endpoint AJAX para o CRUD de alunos
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

// Desativa exceções mysqli — erros tratados manualmente
mysqli_report(MYSQLI_REPORT_OFF);

$method = $_SERVER['REQUEST_METHOD'];
// Suporta override de método via ?_method=PUT para formulários multipart/form-data
if ($method === 'POST' && !empty($_GET['_method'])) {
    $override = strtoupper($_GET['_method']);
    if (in_array($override, ['PUT', 'DELETE'], true)) $method = $override;
}

// Garante que a coluna foto existe na tabela
ensureFotoColumn($conexao);

// ── Roteamento ─────────────────────────────────────────────
switch ($method) {
    case 'GET':
        handleGet($conexao);
        break;
    case 'POST':
        handlePost($conexao);
        break;
    case 'PUT':
        handlePut($conexao);
        break;
    case 'DELETE':
        handleDelete($conexao);
        break;
    default:
        http_response_code(405);
        echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
}

// ══════════════════════════════════════════════════════════
// GET — listar todos / buscar / obter um por id
// ══════════════════════════════════════════════════════════
function handleGet($db) {
    // Stats para o dashboard
    if (!empty($_GET['stats'])) {
        $r = $db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'ativo') AS ativos,
                SUM(status = 'pendente') AS pendentes,
                SUM(DATE_FORMAT(data_matricula,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')) AS novos_mes,
                SUM(DATE_FORMAT(data_matricula,'%Y-%m') = DATE_FORMAT(NOW() - INTERVAL 1 MONTH,'%Y-%m')) AS novos_mes_anterior,
                SUM(docente = 'S') AS docentes
            FROM tb_cad_alunos"
        );
        if (!$r) { http_response_code(500); echo json_encode(['ok'=>false,'msg'=>$db->error]); return; }
        $row = $r->fetch_assoc();
        echo json_encode(['ok' => true,
            'total'              => (int)$row['total'],
            'ativos'             => (int)$row['ativos'],
            'pendentes'          => (int)$row['pendentes'],
            'novos_mes'          => (int)$row['novos_mes'],
            'novos_mes_anterior' => (int)$row['novos_mes_anterior'],
            'docentes'           => (int)$row['docentes'],
        ]);
        return;
    }

    // Últimas matrículas para o dashboard
    if (isset($_GET['recentes'])) {
        $limit = max(1, min(50, (int)($_GET['recentes'] ?: 5)));
        $stmt  = $db->prepare(
            'SELECT id, nome, usuario_email, turma, data_matricula, status, foto
             FROM tb_cad_alunos
             ORDER BY id DESC
             LIMIT ?'
        );
        if (!$stmt) { http_response_code(500); echo json_encode(['ok'=>false,'msg'=>$db->error]); return; }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(['ok' => true, 'alunos' => $rows]);
        return;
    }

    // Aniversariantes do mês
    if (isset($_GET['aniversariantes'])) {
        $mes  = (int) date('m');
        $stmt = $db->prepare(
            'SELECT id, nome, data_nascimento, foto, turma
             FROM tb_cad_alunos
             WHERE MONTH(data_nascimento) = ?
               AND data_nascimento IS NOT NULL
             ORDER BY DAY(data_nascimento)'
        );
        if (!$stmt) { http_response_code(500); echo json_encode(['ok'=>false,'msg'=>$db->error]); return; }
        $stmt->bind_param('i', $mes);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(['ok' => true, 'alunos' => $rows]);
        return;
    }

    // Obter aluno único
    if (!empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        $stmt = $db->prepare(
            'SELECT id, nome, sexo, cpf, estado_civil, profissao,
                    telefone, usuario_email,
                    cep, logradouro, numero_endereco, complemento_endereco,
                    bairro, cidade, UF,
                    data_nascimento, data_matricula, turma, observacoes, status, foto, docente
             FROM tb_cad_alunos WHERE id = ?'
        );
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'msg' => 'Erro na consulta: ' . $db->error]);
            return;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'msg' => 'Aluno não encontrado.']);
            return;
        }
        echo json_encode(['ok' => true, 'aluno' => $row]);
        return;
    }

    // Listar / buscar
    $busca   = trim($_GET['busca']   ?? '');
    $status  = trim($_GET['status']  ?? '');
    $turma   = trim($_GET['turma']   ?? '');
    $docente = trim($_GET['docente'] ?? '');

    $sql    = 'SELECT id, nome, sexo, telefone, usuario_email,
                      turma, data_matricula, status, foto, docente
               FROM tb_cad_alunos WHERE 1=1';
    $types  = '';
    $values = [];

    if ($busca !== '') {
        $like     = '%' . $busca . '%';
        $sql     .= ' AND (nome LIKE ? OR usuario_email LIKE ?)';
        $types   .= 'ss';
        $values[] = $like;
        $values[] = $like;
    }
    if ($status !== '') {
        $sql     .= ' AND status = ?';
        $types   .= 's';
        $values[] = $status;
    }
    if ($turma !== '') {
        $sql     .= ' AND turma = ?';
        $types   .= 's';
        $values[] = $turma;
    }
    if ($docente !== '') {
        $sql     .= ' AND docente = ?';
        $types   .= 's';
        $values[] = $docente;
    }

    $sql .= ' ORDER BY nome ASC';

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro na consulta: ' . $db->error]);
        return;
    }
    if (!empty($values)) {
        // call_user_func_array com referências — compatível com PHP 7 e PHP 8
        $bindArgs = [$types];
        foreach ($values as &$v) $bindArgs[] = &$v;
        unset($v);
        call_user_func_array([$stmt, 'bind_param'], $bindArgs);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $alunos = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
    $stmt->close();

    echo json_encode(['ok' => true, 'alunos' => $alunos, 'total' => count($alunos)]);
}

// ══════════════════════════════════════════════════════════
// POST — criar aluno
// ══════════════════════════════════════════════════════════
function handlePost($db) {
    // Aceita multipart/form-data (com arquivo) ou application/json
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    $erros = validar($data, $db, null);
    if ($erros) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => $erros]);
        return;
    }

    $d = sanitize($data);

    // Processa upload de foto (opcional)
    $fotoPath = '';
    if (!empty($_FILES['foto']['tmp_name'])) {
        $up = uploadFoto($_FILES['foto']);
        if ($up === false) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'msg' => 'Foto inválida (use JPG, PNG ou WebP — máx. 2 MB).']);
            return;
        }
        $fotoPath = $up;
    }

    $stmt = $db->prepare(
        'INSERT INTO tb_cad_alunos
            (nome, sexo, cpf, estado_civil, profissao,
             telefone, usuario_email,
             cep, logradouro, numero_endereco, complemento_endereco,
             bairro, cidade, UF,
             data_matricula, turma, observacoes, status, foto, docente)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar inserção: ' . $db->error]);
        return;
    }

    $stmt->bind_param(
        'ssssssssssssssssssss',
        $d['nome'], $d['sexo'], $d['cpf'], $d['estado_civil'], $d['profissao'],
        $d['telefone'], $d['email'],
        $d['cep'], $d['logradouro'], $d['numero_endereco'], $d['complemento'],
        $d['bairro'], $d['cidade'], $d['UF'],
        $d['data_matricula'], $d['turma'], $d['observacoes'], $d['status'],
        $fotoPath,
        $d['docente']
    );

    if ($stmt->execute()) {
        $newId = $db->insert_id;
        $stmt->close();
        // data_nascimento em statement separado: evita problema de string vazia em coluna DATE
        if ($d['data_nascimento'] !== '') {
            $s2 = $db->prepare('UPDATE tb_cad_alunos SET data_nascimento=? WHERE id=?');
            if ($s2) { $s2->bind_param('si', $d['data_nascimento'], $newId); $s2->execute(); $s2->close(); }
        }
        http_response_code(201);
        echo json_encode(['ok' => true, 'msg' => 'Aluno cadastrado com sucesso.', 'id' => $newId]);
    } else {
        $erro = $db->error;
        $stmt->close();
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao cadastrar aluno: ' . $erro]);
    }
}

// ══════════════════════════════════════════════════════════
// PUT — editar aluno
// ══════════════════════════════════════════════════════════
function handlePut($db) {
    // Aceita multipart/form-data (com arquivo) ou application/json
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    $id = (int) ($data['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        return;
    }

    $erros = validar($data, $db, $id);
    if ($erros) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => $erros]);
        return;
    }

    $d = sanitize($data);

    // Processa upload de nova foto (opcional — mantém a existente se não enviada)
    if (!empty($_FILES['foto']['tmp_name'])) {
        $up = uploadFoto($_FILES['foto']);
        if ($up === false) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'msg' => 'Foto inválida (use JPG, PNG ou WebP — máx. 2 MB).']);
            return;
        }
        $fotoPath = $up;
        // Remove foto anterior se existir
        $stmtF = $db->prepare('SELECT foto FROM tb_cad_alunos WHERE id = ?');
        if ($stmtF) {
            $stmtF->bind_param('i', $id);
            $stmtF->execute();
            $rowF = $stmtF->get_result()->fetch_assoc();
            $stmtF->close();
            if (!empty($rowF['foto'])) {
                $old = __DIR__ . '/' . $rowF['foto'];
                if (is_file($old)) @unlink($old);
            }
        }
    } elseif (!empty($_POST['foto_remover']) && $_POST['foto_remover'] === '1') {
        // Usuário pediu para remover a foto sem enviar nova
        $stmtF = $db->prepare('SELECT foto FROM tb_cad_alunos WHERE id = ?');
        if ($stmtF) {
            $stmtF->bind_param('i', $id);
            $stmtF->execute();
            $rowF = $stmtF->get_result()->fetch_assoc();
            $stmtF->close();
            if (!empty($rowF['foto'])) {
                $old = __DIR__ . '/' . $rowF['foto'];
                if (is_file($old)) @unlink($old);
            }
        }
        $fotoPath = '';
    } else {
        // Nenhum novo arquivo — recupera o caminho atual do banco
        $stmtF = $db->prepare('SELECT foto FROM tb_cad_alunos WHERE id = ?');
        $fotoPath = '';
        if ($stmtF) {
            $stmtF->bind_param('i', $id);
            $stmtF->execute();
            $rowF = $stmtF->get_result()->fetch_assoc();
            $stmtF->close();
            $fotoPath = $rowF['foto'] ?? '';
        }
    }

    $stmt = $db->prepare(
        'UPDATE tb_cad_alunos SET
            nome=?, sexo=?, cpf=?, estado_civil=?, profissao=?,
            telefone=?, usuario_email=?,
            cep=?, logradouro=?, numero_endereco=?, complemento_endereco=?,
            bairro=?, cidade=?, UF=?,
            data_matricula=?, turma=?, observacoes=?, status=?, foto=?, docente=?
         WHERE id=?'
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar atualização: ' . $db->error]);
        return;
    }

    $stmt->bind_param(
        'ssssssssssssssssssssi',
        $d['nome'], $d['sexo'], $d['cpf'], $d['estado_civil'], $d['profissao'],
        $d['telefone'], $d['email'],
        $d['cep'], $d['logradouro'], $d['numero_endereco'], $d['complemento'],
        $d['bairro'], $d['cidade'], $d['UF'],
        $d['data_matricula'], $d['turma'], $d['observacoes'], $d['status'],
        $fotoPath,
        $d['docente'],
        $id
    );

    if ($stmt->execute()) {
        $stmt->close();
        // data_nascimento em statement separado: evita problema de string vazia em coluna DATE
        if ($d['data_nascimento'] !== '') {
            $s2 = $db->prepare('UPDATE tb_cad_alunos SET data_nascimento=? WHERE id=?');
            if ($s2) { $s2->bind_param('si', $d['data_nascimento'], $id); $s2->execute(); $s2->close(); }
        } else {
            $db->query("UPDATE tb_cad_alunos SET data_nascimento=NULL WHERE id=$id");
        }
        echo json_encode(['ok' => true, 'msg' => 'Aluno atualizado com sucesso.']);
    } else {
        $erro = $db->error;
        $stmt->close();
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao atualizar aluno: ' . $erro]);
    }
}

// ══════════════════════════════════════════════════════════
// DELETE — excluir aluno
// ══════════════════════════════════════════════════════════
function handleDelete($db) {
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        return;
    }

    $stmt = $db->prepare('DELETE FROM tb_cad_alunos WHERE id = ?');
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar exclusão: ' . $db->error]);
        return;
    }
    $stmt->bind_param('i', $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        echo json_encode(['ok' => true, 'msg' => 'Aluno excluído com sucesso.']);
    } else {
        $stmt->close();
        http_response_code(404);
        echo json_encode(['ok' => false, 'msg' => 'Aluno não encontrado.']);
    }
}

// ══════════════════════════════════════════════════════════
// Helpers
// ══════════════════════════════════════════════════════════
function validar(array $d, $db, ?int $editId): array {
    $erros = [];

    $nome = trim($d['nome'] ?? '');
    if ($nome === '') {
        $erros['nome'] = 'O nome é obrigatório.';
    } elseif (str_word_count($nome) < 2) {
        $erros['nome'] = 'Informe o nome completo.';
    }

    if (empty($d['data_nascimento'])) {
        // campo opcional — permite nulo
    }

    $sexo = $d['sexo'] ?? '';
    if (!in_array($sexo, ['M', 'F'], true)) {
        $erros['sexo'] = 'Sexo inválido.';
    }

    $cpf = preg_replace('/\D/', '', $d['cpf'] ?? '');
    if ($cpf !== '' && (strlen($cpf) !== 11 || !cpfValido($cpf))) {
        $erros['cpf'] = 'CPF inválido.';
    }

    $tel = preg_replace('/\D/', '', $d['telefone'] ?? '');
    if ($tel === '') {
        $erros['telefone'] = 'O telefone é obrigatório.';
    } elseif (strlen($tel) < 10) {
        $erros['telefone'] = 'Telefone incompleto.';
    }

    $email = trim($d['email'] ?? '');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'E-mail inválido.';
    }

    if (empty(trim($d['turma'] ?? ''))) {
        $erros['turma'] = 'Selecione uma turma.';
    }

    if (empty($d['data_matricula'])) {
        $erros['data_matricula'] = 'A data de matrícula é obrigatória.';
    }

    return $erros;
}

function sanitize(array $d): array {
    $cpf = preg_replace('/\D/', '', $d['cpf'] ?? '');
    $tel = preg_replace('/\D/', '', $d['telefone'] ?? '');
    $cep = preg_replace('/\D/', '', $d['cep'] ?? '');
    $num = preg_replace('/\D/', '', $d['numero'] ?? '');

    // ── CORREÇÃO: data_nascimento retorna null se vazia, evitando erro no MySQL ──
    $dataNasc = trim($d['data_nascimento'] ?? '');
    $dataNasc = $dataNasc !== '' ? $dataNasc : null;

    return [
        'nome'            => mb_substr(trim($d['nome'] ?? ''), 0, 100),
        'sexo'            => in_array($d['sexo'] ?? '', ['M','F']) ? $d['sexo'] : 'M',
        'cpf'             => $cpf,
        'estado_civil'    => mb_substr(trim($d['estado_civil'] ?? ''), 0, 20),
        'profissao'       => mb_substr(trim($d['profissao'] ?? ''), 0, 50),
        'telefone'        => $tel,
        'email'           => mb_substr(trim($d['email'] ?? ''), 0, 50),
        'cep'             => $cep,
        'logradouro'      => mb_substr(trim($d['logradouro'] ?? ''), 0, 100),
        'numero_endereco' => (int) $num,
        'complemento'     => mb_substr(trim($d['complemento'] ?? ''), 0, 50),
        'bairro'          => mb_substr(trim($d['bairro'] ?? ''), 0, 60),
        'cidade'          => mb_substr(trim($d['cidade'] ?? ''), 0, 70),
        'UF'              => mb_strtoupper(mb_substr(trim($d['estado'] ?? $d['UF'] ?? ''), 0, 5)),
        'data_nascimento' => $dataNasc,   // null ou 'Y-m-d'
        'data_matricula'  => $d['data_matricula'] ?? date('Y-m-d'),
        'turma'           => mb_substr(trim($d['turma'] ?? ''), 0, 80),
        'observacoes'     => mb_substr(trim($d['observacoes'] ?? ''), 0, 500),
        'status'          => in_array($d['status'] ?? '', ['ativo','pendente','inativo']) ? $d['status'] : 'ativo',
        'docente'         => ($d['docente'] ?? 'N') === 'S' ? 'S' : 'N',
    ];
}

// ══════════════════════════════════════════════════════════
// Upload de foto do aluno
// ══════════════════════════════════════════════════════════
function uploadFoto(array $file): string|false {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    // Valida MIME type pelo conteúdo real do arquivo (não pelo que o browser informa)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) return false;
    if ($file['size'] > 2 * 1024 * 1024) return false;

    $ext      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dir      = __DIR__ . '/uploads/fotos/';

    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $dest = $dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return 'uploads/fotos/' . $filename;
}

// ══════════════════════════════════════════════════════════
// Migração: garante que a coluna foto existe na tabela
// ══════════════════════════════════════════════════════════
function ensureFotoColumn($db): void {
    $res = $db->query("SHOW COLUMNS FROM tb_cad_alunos LIKE 'foto'");
    if ($res && $res->num_rows === 0) {
        $db->query("ALTER TABLE tb_cad_alunos ADD COLUMN foto VARCHAR(255) NOT NULL DEFAULT ''");
    }
    $res2 = $db->query("SHOW COLUMNS FROM tb_cad_alunos LIKE 'docente'");
    if ($res2 && $res2->num_rows === 0) {
        $db->query("ALTER TABLE tb_cad_alunos ADD COLUMN docente CHAR(1) NOT NULL DEFAULT 'N'");
    }
    $res3 = $db->query("SHOW COLUMNS FROM tb_cad_alunos LIKE 'data_nascimento'");
    if ($res3 && $res3->num_rows === 0) {
        $db->query("ALTER TABLE tb_cad_alunos ADD COLUMN data_nascimento DATE NULL DEFAULT NULL");
    }
}

function cpfValido(string $cpf): bool {
    if (preg_match('/^(\d)\1+$/', $cpf)) return false;
    $soma = 0;
    for ($i = 0; $i < 9; $i++) $soma += (int)$cpf[$i] * (10 - $i);
    $r = ($soma * 10) % 11;
    if ($r === 10 || $r === 11) $r = 0;
    if ($r !== (int)$cpf[9]) return false;
    $soma = 0;
    for ($i = 0; $i < 10; $i++) $soma += (int)$cpf[$i] * (11 - $i);
    $r = ($soma * 10) % 11;
    if ($r === 10 || $r === 11) $r = 0;
    return $r === (int)$cpf[10];
}