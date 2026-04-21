<?php
require_once __DIR__ . '/libs/helpers.php';
/** @var mysqli $conexao */
requireAuth();
csrfCheck();

$method = $_SERVER['REQUEST_METHOD'];

// ── GET ────────────────────────────────────────────────────
if ($method === 'GET') {

    // Buscar evento único
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if ($id <= 0) { err('ID inválido.'); exit(); }
        $st = $conexao->prepare('SELECT * FROM tb_cad_compromissos WHERE id = ? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $ev = $st->get_result()->fetch_assoc();
        $st->close();
        $ev ? ok(['evento' => $ev]) : err('Evento não encontrado.');
        exit();
    }

    // Próximos N dias para lembretes
    if (isset($_GET['proximos'])) {
        $dias = max(1, min(30, (int)($_GET['proximos'] ?? 7)));
        $hoje = date('Y-m-d');
        $limite = date('Y-m-d', strtotime("+$dias days"));
        $st = $conexao->prepare('SELECT * FROM tb_cad_compromissos WHERE data_evento BETWEEN ? AND ? ORDER BY data_evento ASC, hora_inicio ASC');
        $st->bind_param('ss', $hoje, $limite);
        $st->execute();
        $r = $st->get_result();
        $eventos = [];
        while ($row = $r->fetch_assoc()) $eventos[] = $row;
        $st->close();
        ok(['eventos' => $eventos]);
        exit();
    }

    // Listar por mês/ano
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');
    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
    $mes = max(1, min(12, $mes));
    $ano = max(2000, min(2100, $ano));

    $st = $conexao->prepare('SELECT * FROM tb_cad_compromissos WHERE YEAR(data_evento) = ? AND MONTH(data_evento) = ? ORDER BY data_evento ASC, hora_inicio ASC');
    $st->bind_param('ii', $ano, $mes);
    $st->execute();
    $r = $st->get_result();
    $eventos = [];
    while ($row = $r->fetch_assoc()) $eventos[] = $row;
    $st->close();
    ok(['eventos' => $eventos]);
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

    if ($titulo === '' || $data_evento === '') { err('Título e data são obrigatórios.'); exit(); }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) { err('Formato de data inválido.'); exit(); }

    $categorias_validas = ['geral', 'aula', 'evento', 'reuniao', 'urgente'];
    if (!in_array($categoria, $categorias_validas, true)) $categoria = 'geral';

    $hi_val = $hora_inicio !== '' ? $hora_inicio : null;
    $hf_val = $hora_fim    !== '' ? $hora_fim    : null;
    $criado_por = $_SESSION['usuario'];

    $st = $conexao->prepare('INSERT INTO tb_cad_compromissos (titulo, descricao, data_evento, hora_inicio, hora_fim, categoria, lembrete_minutos, criado_por) VALUES (?,?,?,?,?,?,?,?)');
    $st->bind_param('ssssssis', $titulo, $descricao, $data_evento, $hi_val, $hf_val, $categoria, $lembrete, $criado_por);
    if ($st->execute()) {
        $newId = (int)$conexao->insert_id;
        $st->close();
        ok(['msg' => 'Compromisso criado com sucesso.', 'id' => $newId]);
    } else {
        $st->close();
        err('Erro ao salvar compromisso.');
    }
    exit();
}

// ── PUT — atualizar ────────────────────────────────────────
if ($method === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id   = (int)($body['id'] ?? 0);
    if ($id <= 0) { err('ID inválido.'); exit(); }

    $titulo      = trim($body['titulo'] ?? '');
    $descricao   = trim($body['descricao'] ?? '');
    $data_evento = trim($body['data_evento'] ?? '');
    $hora_inicio = trim($body['hora_inicio'] ?? '');
    $hora_fim    = trim($body['hora_fim'] ?? '');
    $categoria   = trim($body['categoria'] ?? 'geral');
    $lembrete    = (int)($body['lembrete_minutos'] ?? 30);

    if ($titulo === '' || $data_evento === '') { err('Título e data são obrigatórios.'); exit(); }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) { err('Formato de data inválido.'); exit(); }

    $categorias_validas = ['geral', 'aula', 'evento', 'reuniao', 'urgente'];
    if (!in_array($categoria, $categorias_validas, true)) $categoria = 'geral';

    $hi_val = $hora_inicio !== '' ? $hora_inicio : null;
    $hf_val = $hora_fim    !== '' ? $hora_fim    : null;

    $st = $conexao->prepare('UPDATE tb_cad_compromissos SET titulo=?, descricao=?, data_evento=?, hora_inicio=?, hora_fim=?, categoria=?, lembrete_minutos=? WHERE id=?');
    $st->bind_param('ssssssii', $titulo, $descricao, $data_evento, $hi_val, $hf_val, $categoria, $lembrete, $id);
    if ($st->execute()) {
        $st->close();
        ok(['msg' => 'Compromisso atualizado.']);
    } else {
        $st->close();
        err('Erro ao atualizar compromisso.');
    }
    exit();
}

// ── DELETE ─────────────────────────────────────────────────
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { err('ID inválido.'); exit(); }
    $st = $conexao->prepare('DELETE FROM tb_cad_compromissos WHERE id = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $st->affected_rows > 0
        ? ok(['msg' => 'Compromisso excluído.'])
        : err('Compromisso não encontrado.');
    $st->close();
    exit();
}

err('Método não suportado.');
