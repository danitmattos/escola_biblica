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
    // Obter aluno único
    if (!empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        $stmt = $db->prepare(
            'SELECT id, nome, sexo, cpf, estado_civil, profissao,
                    telefone, usuario_email,
                    cep, logradouro, numero_endereco, complemento_endereco,
                    bairro, cidade, UF,
                    data_matricula, turma, observacoes, status
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
    $busca  = trim($_GET['busca']  ?? '');
    $status = trim($_GET['status'] ?? '');
    $turma  = trim($_GET['turma']  ?? '');

    $sql    = 'SELECT id, nome, sexo, telefone, usuario_email,
                      turma, data_matricula, status
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
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $erros = validar($data, $db, null);
    if ($erros) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erros' => $erros]);
        return;
    }

    $d = sanitize($data);

    $stmt = $db->prepare(
        'INSERT INTO tb_cad_alunos
            (nome, sexo, cpf, estado_civil, profissao,
             telefone, usuario_email,
             cep, logradouro, numero_endereco, complemento_endereco,
             bairro, cidade, UF,
             data_matricula, turma, observacoes, status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar inserção: ' . $db->error]);
        return;
    }
    $stmt->bind_param(
        'ssssssssssssssssss',
        $d['nome'], $d['sexo'], $d['cpf'], $d['estado_civil'], $d['profissao'],
        $d['telefone'], $d['email'],
        $d['cep'], $d['logradouro'], $d['numero_endereco'], $d['complemento'],
        $d['bairro'], $d['cidade'], $d['UF'],
        $d['data_matricula'], $d['turma'], $d['observacoes'], $d['status']
    );

    if ($stmt->execute()) {
        $id = $db->insert_id;
        $stmt->close();
        http_response_code(201);
        echo json_encode(['ok' => true, 'msg' => 'Aluno cadastrado com sucesso.', 'id' => $id]);
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
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        parse_str(file_get_contents('php://input'), $data);
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

    $stmt = $db->prepare(
        'UPDATE tb_cad_alunos SET
            nome=?, sexo=?, cpf=?, estado_civil=?, profissao=?,
            telefone=?, usuario_email=?,
            cep=?, logradouro=?, numero_endereco=?, complemento_endereco=?,
            bairro=?, cidade=?, UF=?,
            data_matricula=?, turma=?, observacoes=?, status=?
         WHERE id=?'
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar atualização: ' . $db->error]);
        return;
    }
    $stmt->bind_param(
        'ssssssssssssssssssi',
        $d['nome'], $d['sexo'], $d['cpf'], $d['estado_civil'], $d['profissao'],
        $d['telefone'], $d['email'],
        $d['cep'], $d['logradouro'], $d['numero_endereco'], $d['complemento'],
        $d['bairro'], $d['cidade'], $d['UF'],
        $d['data_matricula'], $d['turma'], $d['observacoes'], $d['status'],
        $id
    );

    if ($stmt->execute()) {
        $stmt->close();
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
        // data_nascimento não está na tabela, ignoramos silenciosamente
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

    return [
        'nome'           => mb_substr(trim($d['nome'] ?? ''), 0, 100),
        'sexo'           => in_array($d['sexo'] ?? '', ['M','F']) ? $d['sexo'] : 'M',
        'cpf'            => $cpf,                  // VARCHAR — preserva zeros à esquerda
        'estado_civil'   => mb_substr(trim($d['estado_civil'] ?? ''), 0, 20),
        'profissao'      => mb_substr(trim($d['profissao'] ?? ''), 0, 50),
        'telefone'       => $tel,                  // VARCHAR — 11 dígitos excedem INT
        'email'          => mb_substr(trim($d['email'] ?? ''), 0, 50),
        'cep'            => $cep,                  // VARCHAR — preserva zeros à esquerda
        'logradouro'     => mb_substr(trim($d['logradouro'] ?? ''), 0, 100),
        'numero_endereco'=> (int) $num,
        'complemento'    => mb_substr(trim($d['complemento'] ?? ''), 0, 50),
        'bairro'         => mb_substr(trim($d['bairro'] ?? ''), 0, 60),
        'cidade'         => mb_substr(trim($d['cidade'] ?? ''), 0, 70),
        'UF'             => mb_strtoupper(mb_substr(trim($d['estado'] ?? $d['UF'] ?? ''), 0, 5)),
        'data_matricula' => $d['data_matricula'] ?? date('Y-m-d'),
        'turma'          => mb_substr(trim($d['turma'] ?? ''), 0, 80),
        'observacoes'    => mb_substr(trim($d['observacoes'] ?? ''), 0, 500),
        'status'         => in_array($d['status'] ?? '', ['ativo','pendente','inativo']) ? $d['status'] : 'ativo',
    ];
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
