<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit();
}

require_once 'libs/connection.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET ────────────────────────────────────────────────────
if ($method === 'GET') {

    // Buscar evento único
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
            exit();
        }
        $res = mysqli_query($conexao, "SELECT * FROM tb_cad_compromissos WHERE id = $id LIMIT 1");
        $ev  = mysqli_fetch_assoc($res);
        echo $ev
            ? json_encode(['ok' => true, 'evento' => $ev])
            : json_encode(['ok' => false, 'msg' => 'Evento não encontrado.']);
        exit();
    }

    // Listar eventos de um mês — ou próximos N dias para lembretes
    if (isset($_GET['proximos'])) {
        $dias = max(1, min(30, (int)($_GET['proximos'] ?? 7)));
        $hoje = date('Y-m-d');
        $limite = date('Y-m-d', strtotime("+$dias days"));
        $res = mysqli_query($conexao, "
            SELECT * FROM tb_cad_compromissos
            WHERE data_evento BETWEEN '$hoje' AND '$limite'
            ORDER BY data_evento ASC, hora_inicio ASC
        ");
        $eventos = [];
        while ($row = mysqli_fetch_assoc($res)) $eventos[] = $row;
        echo json_encode(['ok' => true, 'eventos' => $eventos]);
        exit();
    }

    // Listar por mês/ano
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');
    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
    $mes = max(1, min(12, $mes));
    $ano = max(2000, min(2100, $ano));

    $res = mysqli_query($conexao, "
        SELECT * FROM tb_cad_compromissos
        WHERE YEAR(data_evento) = $ano AND MONTH(data_evento) = $mes
        ORDER BY data_evento ASC, hora_inicio ASC
    ");
    $eventos = [];
    while ($row = mysqli_fetch_assoc($res)) $eventos[] = $row;
    echo json_encode(['ok' => true, 'eventos' => $eventos]);
    exit();
}

// ── POST — criar ───────────────────────────────────────────
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    $titulo      = trim($body['titulo'] ?? '');
    $descricao   = trim($body['descricao'] ?? '');
    $data_evento = trim($body['data_evento'] ?? '');
    $hora_inicio = trim($body['hora_inicio'] ?? '');
    $hora_fim    = trim($body['hora_fim'] ?? '');
    $categoria   = trim($body['categoria'] ?? 'geral');
    $lembrete    = (int)($body['lembrete_minutos'] ?? 30);

    if ($titulo === '' || $data_evento === '') {
        echo json_encode(['ok' => false, 'msg' => 'Título e data são obrigatórios.']);
        exit();
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de data inválido.']);
        exit();
    }
    $categorias_validas = ['geral', 'aula', 'evento', 'reuniao', 'urgente'];
    if (!in_array($categoria, $categorias_validas, true)) $categoria = 'geral';

    $titulo      = mysqli_real_escape_string($conexao, $titulo);
    $descricao   = mysqli_real_escape_string($conexao, $descricao);
    $data_evento = mysqli_real_escape_string($conexao, $data_evento);
    $categoria   = mysqli_real_escape_string($conexao, $categoria);
    $criado_por  = mysqli_real_escape_string($conexao, $_SESSION['usuario']);

    $hi_sql = ($hora_inicio !== '') ? "'" . mysqli_real_escape_string($conexao, $hora_inicio) . "'" : 'NULL';
    $hf_sql = ($hora_fim    !== '') ? "'" . mysqli_real_escape_string($conexao, $hora_fim)    . "'" : 'NULL';

    $ok = mysqli_query($conexao, "
        INSERT INTO tb_cad_compromissos (titulo, descricao, data_evento, hora_inicio, hora_fim, categoria, lembrete_minutos, criado_por)
        VALUES ('$titulo', '$descricao', '$data_evento', $hi_sql, $hf_sql, '$categoria', $lembrete, '$criado_por')
    ");

    if ($ok) {
        echo json_encode(['ok' => true, 'msg' => 'Compromisso criado com sucesso.', 'id' => (int)mysqli_insert_id($conexao)]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Erro ao salvar: ' . mysqli_error($conexao)]);
    }
    exit();
}

// ── PUT — atualizar ────────────────────────────────────────
if ($method === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id   = (int)($body['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit();
    }

    $titulo      = trim($body['titulo'] ?? '');
    $descricao   = trim($body['descricao'] ?? '');
    $data_evento = trim($body['data_evento'] ?? '');
    $hora_inicio = trim($body['hora_inicio'] ?? '');
    $hora_fim    = trim($body['hora_fim'] ?? '');
    $categoria   = trim($body['categoria'] ?? 'geral');
    $lembrete    = (int)($body['lembrete_minutos'] ?? 30);

    if ($titulo === '' || $data_evento === '') {
        echo json_encode(['ok' => false, 'msg' => 'Título e data são obrigatórios.']);
        exit();
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato de data inválido.']);
        exit();
    }
    $categorias_validas = ['geral', 'aula', 'evento', 'reuniao', 'urgente'];
    if (!in_array($categoria, $categorias_validas, true)) $categoria = 'geral';

    $titulo      = mysqli_real_escape_string($conexao, $titulo);
    $descricao   = mysqli_real_escape_string($conexao, $descricao);
    $data_evento = mysqli_real_escape_string($conexao, $data_evento);
    $categoria   = mysqli_real_escape_string($conexao, $categoria);

    $hi_sql = ($hora_inicio !== '') ? "'" . mysqli_real_escape_string($conexao, $hora_inicio) . "'" : 'NULL';
    $hf_sql = ($hora_fim    !== '') ? "'" . mysqli_real_escape_string($conexao, $hora_fim)    . "'" : 'NULL';

    $ok = mysqli_query($conexao, "
        UPDATE tb_cad_compromissos SET
            titulo = '$titulo',
            descricao = '$descricao',
            data_evento = '$data_evento',
            hora_inicio = $hi_sql,
            hora_fim = $hf_sql,
            categoria = '$categoria',
            lembrete_minutos = $lembrete
        WHERE id = $id
    ");

    if ($ok && mysqli_affected_rows($conexao) >= 0) {
        echo json_encode(['ok' => true, 'msg' => 'Compromisso atualizado.']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Erro ao atualizar: ' . mysqli_error($conexao)]);
    }
    exit();
}

// ── DELETE ─────────────────────────────────────────────────
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit();
    }
    $ok = mysqli_query($conexao, "DELETE FROM tb_cad_compromissos WHERE id = $id");
    if ($ok && mysqli_affected_rows($conexao) > 0) {
        echo json_encode(['ok' => true, 'msg' => 'Compromisso excluído.']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Compromisso não encontrado.']);
    }
    exit();
}

echo json_encode(['ok' => false, 'msg' => 'Método não suportado.']);
