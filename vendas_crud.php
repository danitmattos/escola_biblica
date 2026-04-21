<?php
/**
 * vendas_crud.php
 * API para gestão de vendas de revistas (aluno R$10 / professor R$15).
 * Tabela: tb_vendas  (criada automaticamente se não existir)
 */

require_once __DIR__ . '/libs/helpers.php';
/** @var mysqli $conexao */
requireAuth();
csrfCheck();

// Removido: criação automática da tabela tb_vendas. Crie a tabela manualmente no banco de dados conforme o schema fornecido.

$method  = $_SERVER['REQUEST_METHOD'];
$recurso = strtolower(trim($_GET['recurso'] ?? ''));

/* ═══════════════════════════════════════════════════
   GET
   ═══════════════════════════════════════════════════ */
if ($method === 'GET') {

    /* ── Dashboard / resumo ── */
    if ($recurso === 'resumo') {
        $ano = (int)($_GET['ano'] ?? date('Y'));
        $tri = isset($_GET['trimestre']) && $_GET['trimestre'] !== '' ? (int)$_GET['trimestre'] : null;

        $where = 'WHERE v.ano = ?';
        $types = 'i';
        $vals  = [$ano];

        if ($tri) {
            $where .= ' AND v.trimestre = ?';
            $types .= 'i';
            $vals[] = $tri;
        }

        /* Totais */
        $sql = "SELECT
                    COUNT(*)                                         AS total_vendas,
                    COALESCE(SUM(v.valor), 0)                        AS total_valor,
                    COALESCE(SUM(CASE WHEN v.status_pgto='pago'  THEN v.valor ELSE 0 END), 0) AS total_pago,
                    COALESCE(SUM(CASE WHEN v.status_pgto='fiado' THEN v.valor ELSE 0 END), 0) AS total_debito,
                    SUM(v.tipo_revista='aluno')                      AS qtd_aluno,
                    SUM(v.tipo_revista='professor')                  AS qtd_professor
                FROM tb_vendas v $where";

        $st = $conexao->prepare($sql);
        $st->bind_param($types, ...$vals);
        $st->execute();
        $resumo = $st->get_result()->fetch_assoc();
        $st->close();

        ok(['resumo' => [
            'total_vendas'  => (int)$resumo['total_vendas'],
            'total_valor'   => number_format((float)$resumo['total_valor'], 2, '.', ''),
            'total_pago'    => number_format((float)$resumo['total_pago'], 2, '.', ''),
            'total_debito'  => number_format((float)$resumo['total_debito'], 2, '.', ''),
            'qtd_aluno'     => (int)$resumo['qtd_aluno'],
            'qtd_professor' => (int)$resumo['qtd_professor'],
        ]]);
        exit;
    }

    /* ── Histórico de vendas ── */
    if ($recurso === 'historico') {
        $ano    = (int)($_GET['ano'] ?? date('Y'));
        $tri    = isset($_GET['trimestre']) && $_GET['trimestre'] !== '' ? (int)$_GET['trimestre'] : null;
        $status = in_array($_GET['status'] ?? '', ['pago','fiado']) ? $_GET['status'] : null;

        $where = 'WHERE v.ano = ?';
        $types = 'i';
        $vals  = [$ano];

        if ($tri) {
            $where .= ' AND v.trimestre = ?';
            $types .= 'i';
            $vals[] = $tri;
        }
        if ($status) {
            $where .= ' AND v.status_pgto = ?';
            $types .= 's';
            $vals[] = $status;
        }

        $sql = "SELECT v.id, v.aluno_id, a.nome AS aluno_nome, a.docente,
                       v.tipo_revista, v.valor, v.forma_pagamento,
                       v.status_pgto, v.trimestre, v.ano,
                       v.observacao, v.criado_em, v.pago_em
                FROM tb_vendas v
                INNER JOIN tb_cad_alunos a ON a.id = v.aluno_id
                $where
                ORDER BY v.criado_em DESC";

        $st = $conexao->prepare($sql);
        $st->bind_param($types, ...$vals);
        $st->execute();
        $r = $st->get_result();
        $rows = [];
        while ($row = $r->fetch_assoc()) {
            $row['valor'] = number_format((float)$row['valor'], 2, '.', '');
            $rows[] = $row;
        }
        $st->close();

        ok(['vendas' => $rows]);
        exit;
    }

    /* ── Devedores ── */
    if ($recurso === 'devedores') {
        $sql = "SELECT a.id AS aluno_id, a.nome AS aluno_nome, a.docente,
                       COUNT(v.id) AS qtd_fiado,
                       SUM(v.valor) AS total_divida,
                       GROUP_CONCAT(CONCAT(v.trimestre,'ºT/',v.ano) SEPARATOR ', ') AS periodos
                FROM tb_vendas v
                INNER JOIN tb_cad_alunos a ON a.id = v.aluno_id
                WHERE v.status_pgto = 'fiado'
                GROUP BY a.id
                ORDER BY total_divida DESC, a.nome ASC";
        $r = $conexao->query($sql);
        $rows = [];
        while ($row = $r->fetch_assoc()) {
            $row['total_divida'] = number_format((float)$row['total_divida'], 2, '.', '');
            $rows[] = $row;
        }
        ok(['devedores' => $rows]);
        exit;
    }

    /* ── Alunos + professores para select ── */
    if ($recurso === 'pessoas') {
        $r = $conexao->query("SELECT id, nome, docente FROM tb_cad_alunos WHERE LOWER(status) = 'ativo' ORDER BY nome ASC");
        $rows = [];
        while ($row = $r->fetch_assoc()) $rows[] = $row;
        ok(['pessoas' => $rows]);
        exit;
    }

    /* ── Anos disponíveis ── */
    if ($recurso === 'anos') {
        $r = $conexao->query("SELECT DISTINCT ano FROM tb_vendas ORDER BY ano DESC");
        $anos = [];
        while ($row = $r->fetch_assoc()) $anos[] = (int)$row['ano'];
        if (!$anos) $anos[] = (int)date('Y');
        ok(['anos' => $anos]);
        exit;
    }

    err('Recurso GET não encontrado.', 404);
    exit;
}

/* ═══════════════════════════════════════════════════
   POST
   ═══════════════════════════════════════════════════ */
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    /* ── Registrar venda ── */
    if ($recurso === 'venda') {
        $aluno_id  = (int)($body['aluno_id'] ?? 0);
        $tipo      = in_array($body['tipo_revista'] ?? '', ['aluno','professor']) ? $body['tipo_revista'] : 'aluno';
        $forma     = trim($body['forma_pagamento'] ?? 'dinheiro');
        $fiado     = !empty($body['fiado']);
        $trimestre = (int)($body['trimestre'] ?? 0);
        $ano       = (int)($body['ano'] ?? date('Y'));
        $obs       = trim($body['observacao'] ?? '');

        $formasValidas = ['dinheiro','pix','cartao','transferencia'];
        if (!in_array($forma, $formasValidas)) $forma = 'dinheiro';

        if ($aluno_id <= 0) { err('Selecione uma pessoa.'); exit; }
        if ($trimestre < 1 || $trimestre > 4) { err('Selecione um trimestre válido.'); exit; }

        /* Verifica se já comprou nesse trimestre */
        $chk = $conexao->prepare('SELECT id FROM tb_vendas WHERE aluno_id = ? AND trimestre = ? AND ano = ? LIMIT 1');
        $chk->bind_param('iii', $aluno_id, $trimestre, $ano);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $chk->close();
            err('Esta pessoa já possui uma revista registrada para este trimestre/ano.');
            exit;
        }
        $chk->close();

        $valor       = ($tipo === 'professor') ? 15.00 : 10.00;
        $status_pgto = $fiado ? 'fiado' : 'pago';
        $pago_em     = $fiado ? null : date('Y-m-d H:i:s');

        $st = $conexao->prepare(
            'INSERT INTO tb_vendas (aluno_id, tipo_revista, valor, forma_pagamento, status_pgto, trimestre, ano, observacao, pago_em)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $st->bind_param('isdssiiss', $aluno_id, $tipo, $valor, $forma, $status_pgto, $trimestre, $ano, $obs, $pago_em);
        if (!$st->execute()) { $st->close(); err('Erro ao registrar venda.'); exit; }
        $newId = (int)$conexao->insert_id;
        $st->close();

        ok(['id' => $newId, 'msg' => 'Venda registrada com sucesso! R$ ' . number_format($valor, 2, ',', '.')]);
        exit;
    }

    /* ── Marcar como pago (quitar fiado) ── */
    if ($recurso === 'quitar') {
        $venda_id = (int)($body['venda_id'] ?? 0);
        $forma    = trim($body['forma_pagamento'] ?? 'dinheiro');
        $formasValidas = ['dinheiro','pix','cartao','transferencia'];
        if (!in_array($forma, $formasValidas)) $forma = 'dinheiro';

        if ($venda_id <= 0) { err('Venda inválida.'); exit; }

        $st = $conexao->prepare("UPDATE tb_vendas SET status_pgto='pago', forma_pagamento=?, pago_em=NOW() WHERE id=? AND status_pgto='fiado'");
        $st->bind_param('si', $forma, $venda_id);
        $st->execute();
        if ($st->affected_rows === 0) { $st->close(); err('Venda não encontrada ou já paga.'); exit; }
        $st->close();

        ok(['msg' => 'Pagamento registrado com sucesso!']);
        exit;
    }

    err('Recurso POST não encontrado.', 404);
    exit;
}

/* ═══════════════════════════════════════════════════
   DELETE
   ═══════════════════════════════════════════════════ */
if ($method === 'DELETE') {
    if ($recurso === 'venda') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }

        $st = $conexao->prepare('DELETE FROM tb_vendas WHERE id = ?');
        $st->bind_param('i', $id);
        $st->execute();
        if ($st->affected_rows === 0) { $st->close(); err('Venda não encontrada.'); exit; }
        $st->close();

        ok(['msg' => 'Venda removida.']);
        exit;
    }

    err('Recurso DELETE não encontrado.', 404);
    exit;
}

err('Método não suportado.', 405);
