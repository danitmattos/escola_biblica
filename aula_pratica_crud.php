<?php
/**
 * aula_pratica_crud.php
 * CRUD para a funcionalidade "Aula na Prática"
 *
 * Tabelas:
 *   tb_aula_pratica_sessoes  — sessões de perguntas
 *   tb_aula_pratica_respostas — respostas registradas por aluno
 *
 * Regras de pontuação:
 *   sem_leitura = 2 pontos
 *   com_leitura = 1 ponto
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

    /* ── Lista de turmas para o select ── */
    if ($recurso === 'turmas') {
        $r = $conexao->query("SELECT id, nome_turma FROM tb_cad_turmas ORDER BY nome_turma ASC");
        $rows = [];
        while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
        ok(['turmas' => $rows]);
        exit;
    }

    /* ── Docentes (alunos com docente = 'S') ── */
    if ($recurso === 'docentes') {
        $r = $conexao->query("SELECT id, nome FROM tb_cad_alunos WHERE LOWER(status) = 'ativo' AND docente = 'S' ORDER BY nome ASC");
        $rows = [];
        if ($r) while ($row = $r->fetch_assoc()) $rows[] = $row;
        ok(['docentes' => $rows]);
        exit;
    }

    /* ── Alunos de uma turma (ou todos) ── */
    if ($recurso === 'alunos') {
        $turma_id = (int)($_GET['turma_id'] ?? 0);
        $rows = [];
        if ($turma_id > 0) {
            $st = $conexao->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ? LIMIT 1');
            $st->bind_param('i', $turma_id);
            $st->execute();
            $row_t = $st->get_result()->fetch_assoc();
            $st->close();
            if ($row_t) {
                $nome_turma = $row_t['nome_turma'];
                $st2 = $conexao->prepare("SELECT id, nome FROM tb_cad_alunos WHERE turma = ? AND LOWER(status) = 'ativo' ORDER BY nome ASC");
                $st2->bind_param('s', $nome_turma);
                $st2->execute();
                $r = $st2->get_result();
                while ($row = $r->fetch_assoc()) $rows[] = $row;
                $st2->close();
            }
        } else {
            $r = $conexao->query("SELECT id, nome FROM tb_cad_alunos WHERE LOWER(status) = 'ativo' ORDER BY nome ASC");
            if ($r) while ($row = $r->fetch_assoc()) $rows[] = $row;
        }
        ok(['alunos' => $rows]);
        exit;
    }

    /* ── Aulas de uma turma (ou todas) para o select ── */
    if ($recurso === 'aulas') {
        $turma_id = (int)($_GET['turma_id'] ?? 0);
        $sql = 'SELECT a.id, a.titulo, a.data_aula, a.professor,
                   (SELECT COUNT(*) FROM tb_aula_pratica_sessoes WHERE aula_id = a.id) > 0 AS tem_sessao
            FROM tb_cad_aulas a
            INNER JOIN tb_cad_temas t ON t.id = a.tema_id';
        $types = ''; $params = [];
        if ($turma_id > 0) { $sql .= ' WHERE t.turma_id = ?'; $types .= 'i'; $params[] = $turma_id; }
        $sql .= ' ORDER BY a.data_aula DESC, a.id DESC';
        $st = $conexao->prepare($sql);
        if ($types) $st->bind_param($types, ...$params);
        $st->execute();
        $r = $st->get_result();
        $rows = [];
        while ($row = $r->fetch_assoc()) { $row['tem_sessao'] = (bool)$row['tem_sessao']; $rows[] = $row; }
        $st->close();
        ok(['aulas' => $rows]);
        exit;
    }

    /* ── Perguntas de uma aula ── */
    if ($recurso === 'perguntas') {
        $aula_id = (int)($_GET['aula_id'] ?? 0);
        if ($aula_id <= 0) { err('aula_id inválido.'); exit; }
        $st = $conexao->prepare('SELECT id, pergunta, resposta FROM tb_cad_perguntas WHERE aula_id = ? ORDER BY ordem ASC, id ASC');
        $st->bind_param('i', $aula_id);
        $st->execute();
        $r = $st->get_result();
        $rows = [];
        while ($row = $r->fetch_assoc()) $rows[] = $row;
        $st->close();
        ok(['perguntas' => $rows]);
        exit;
    }

    /* ── Ranking para o dashboard ── */
    if ($recurso === 'ranking-dashboard') {
        $ano   = (int)($_GET['ano'] ?? date('Y'));
        $ano   = ($ano >= 2000 && $ano <= 2100) ? $ano : (int)date('Y');
        $limit = (int)($_GET['limit'] ?? 10);
        $limit = max(3, min(20, $limit));
        $st = $conexao->prepare("
            SELECT a.id AS aluno_id, a.nome AS aluno_nome, a.turma,
                   SUM(r.pontos) AS total_pontos,
                   COUNT(r.id)   AS total_respostas,
                   SUM(CASE WHEN r.tipo='sem_leitura' THEN 1 ELSE 0 END) AS qtd_sem_leitura,
                   SUM(CASE WHEN r.tipo='com_leitura'  THEN 1 ELSE 0 END) AS qtd_com_leitura
            FROM tb_aula_pratica_respostas r
            INNER JOIN tb_cad_alunos a ON a.id = r.aluno_id
            INNER JOIN tb_aula_pratica_sessoes s ON s.id = r.sessao_id
            WHERE YEAR(s.data_sessao) = ?
            GROUP BY a.id
            ORDER BY total_pontos DESC, total_respostas DESC
            LIMIT ?
        ");
        $st->bind_param('ii', $ano, $limit);
        $st->execute();
        $r = $st->get_result();
        $rows = [];
        while ($row = $r->fetch_assoc()) $rows[] = $row;
        $st->close();

        /* Anos disponíveis */
        $ry = $conexao->query("
            SELECT DISTINCT YEAR(s.data_sessao) AS ano
            FROM tb_aula_pratica_sessoes s
            INNER JOIN tb_aula_pratica_respostas r ON r.sessao_id = s.id
            ORDER BY ano DESC
        ");
        $anos = [];
        if ($ry) while ($row = $ry->fetch_assoc()) $anos[] = (int)$row['ano'];
        if (!in_array($ano, $anos)) $anos[] = $ano;
        rsort($anos);

        ok(['ranking' => $rows, 'ano' => $ano, 'anos' => $anos]);
        exit;
    }

    /* ── Sessões (com total de pontos) ── */
    if ($recurso === 'sessoes' || $recurso === '') {
        $turma_id  = (int)($_GET['turma_id'] ?? 0);
        $encerrada = isset($_GET['encerrada']) ? (int)$_GET['encerrada'] : 0;
        $data_filtro = trim($_GET['data'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_filtro)) $data_filtro = '';
        $sql = 'SELECT s.id, s.titulo, s.data_sessao, s.descricao, s.encerrada,
                   s.professor_id, s.professor_substituto_id,
                   t.nome_turma,
                   COUNT(r.id) AS total_respostas,
                   COALESCE(SUM(r.pontos), 0) AS total_pontos
            FROM tb_aula_pratica_sessoes s
            LEFT JOIN tb_cad_turmas t ON t.id = s.turma_id
            LEFT JOIN tb_aula_pratica_respostas r ON r.sessao_id = s.id
            WHERE s.encerrada = ?';
        $types = 'i'; $params = [$encerrada];
        if ($turma_id > 0) { $sql .= ' AND s.turma_id = ?'; $types .= 'i'; $params[] = $turma_id; }
        if ($data_filtro !== '') { $sql .= ' AND s.data_sessao = ?'; $types .= 's'; $params[] = $data_filtro; }
        $sql .= ' GROUP BY s.id ORDER BY s.data_sessao DESC, s.id DESC';
        $st = $conexao->prepare($sql);
        $st->bind_param($types, ...$params);
        $st->execute();
        $r = $st->get_result();
        $rows = [];
        while ($row = $r->fetch_assoc()) $rows[] = $row;
        $st->close();
        ok(['sessoes' => $rows]);
        exit;
    }


    /* ── Detalhes de uma sessão (ranking de alunos) ── */
    if ($recurso === 'sessao') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }

        $st = $conexao->prepare('SELECT s.*, t.nome_turma FROM tb_aula_pratica_sessoes s LEFT JOIN tb_cad_turmas t ON t.id = s.turma_id WHERE s.id = ? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $sessao = $st->get_result()->fetch_assoc();
        $st->close();
        if (!$sessao) { err('Sessão não encontrada.'); exit; }

        /* Ranking por aluno */
        $st = $conexao->prepare("
            SELECT a.id AS aluno_id, a.nome AS aluno_nome,
                   SUM(r.pontos) AS total_pontos,
                   SUM(CASE WHEN r.tipo='sem_leitura' THEN 1 ELSE 0 END) AS qtd_sem_leitura,
                   SUM(CASE WHEN r.tipo='com_leitura'  THEN 1 ELSE 0 END) AS qtd_com_leitura,
                   COUNT(r.id) AS total_respostas
            FROM tb_aula_pratica_respostas r
            INNER JOIN tb_cad_alunos a ON a.id = r.aluno_id
            WHERE r.sessao_id = ?
            GROUP BY a.id
            ORDER BY total_pontos DESC, a.nome ASC
        ");
        $st->bind_param('i', $id);
        $st->execute();
        $rr = $st->get_result();
        $ranking = [];
        while ($row = $rr->fetch_assoc()) $ranking[] = $row;
        $st->close();

        /* Histórico de respostas */
        $st = $conexao->prepare("
            SELECT r.id, r.tipo, r.pontos, r.pergunta, r.criado_em,
                   a.nome AS aluno_nome
            FROM tb_aula_pratica_respostas r
            INNER JOIN tb_cad_alunos a ON a.id = r.aluno_id
            WHERE r.sessao_id = ?
            ORDER BY r.criado_em DESC
        ");
        $st->bind_param('i', $id);
        $st->execute();
        $rh = $st->get_result();
        $historico = [];
        while ($row = $rh->fetch_assoc()) $historico[] = $row;
        $st->close();

        /* Perguntas da aula vinculada */
        $perguntas = [];
        if (!empty($sessao['aula_id'])) {
            $aula_id_s = (int)$sessao['aula_id'];
            $st = $conexao->prepare('SELECT id, pergunta, resposta FROM tb_cad_perguntas WHERE aula_id = ? ORDER BY ordem ASC, id ASC');
            $st->bind_param('i', $aula_id_s);
            $st->execute();
            $rp = $st->get_result();
            while ($row = $rp->fetch_assoc()) $perguntas[] = $row;
            $st->close();
        }

        /* Alunos da turma vinculada à sessão */
        $alunos_sessao = [];
        if (!empty($sessao['turma_id'])) {
            $tid = (int)$sessao['turma_id'];
            $st = $conexao->prepare('SELECT nome_turma FROM tb_cad_turmas WHERE id = ? LIMIT 1');
            $st->bind_param('i', $tid);
            $st->execute();
            $row_t2 = $st->get_result()->fetch_assoc();
            $st->close();
            if ($row_t2) {
                $nt = $row_t2['nome_turma'];
                $st = $conexao->prepare("SELECT id, nome FROM tb_cad_alunos WHERE turma = ? AND LOWER(status) = 'ativo' ORDER BY nome ASC");
                $st->bind_param('s', $nt);
                $st->execute();
                $ra = $st->get_result();
                while ($row = $ra->fetch_assoc()) $alunos_sessao[] = $row;
                $st->close();
            }
        } else {
            $ra = $conexao->query("SELECT id, nome FROM tb_cad_alunos WHERE LOWER(status) = 'ativo' ORDER BY nome ASC");
            if ($ra) while ($row = $ra->fetch_assoc()) $alunos_sessao[] = $row;
        }

        /* IDs dos alunos marcados como presentes nesta sessão */
        $presentes = [];
        $st = $conexao->prepare('SELECT aluno_id FROM tb_aula_pratica_presenca WHERE sessao_id = ?');
        $st->bind_param('i', $id);
        $st->execute();
        $rpres = $st->get_result();
        while ($row = $rpres->fetch_assoc()) $presentes[] = (int)$row['aluno_id'];
        $st->close();

        ok([
            'sessao'    => $sessao,
            'ranking'   => $ranking,
            'historico' => $historico,
            'perguntas' => $perguntas,
            'alunos'    => $alunos_sessao,
            'presentes' => $presentes,
        ]);
        exit;
    }

    err('Recurso desconhecido.'); exit;
}

/* ══════════════════════════════════════════════════
   POST
══════════════════════════════════════════════════ */
if ($method === 'POST') {

    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    /* ── Criar sessão ── */
    if ($recurso === 'sessao') {
        $titulo       = trim($body['titulo']     ?? '');
        $turma_id     = (int)($body['turma_id']   ?? 0);
        $aula_id      = (int)($body['aula_id']    ?? 0);
        $professor_id = (int)($body['professor_id'] ?? 0);
        $professor_sub_id = (int)($body['professor_substituto_id'] ?? 0);
        $data_sessao  = trim($body['data_sessao'] ?? date('Y-m-d'));
        $descricao    = trim($body['descricao']  ?? '');

        if ($titulo === '') { err('Informe o título da sessão.'); exit; }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_sessao)) {
            $data_sessao = date('Y-m-d');
        }

        /* Impede duplicidade: cada aula só pode ter uma sessão */
        if ($aula_id > 0) {
            $chk = $conexao->prepare('SELECT id FROM tb_aula_pratica_sessoes WHERE aula_id = ? LIMIT 1');
            $chk->bind_param('i', $aula_id);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                $chk->close();
                err('Esta aula já possui uma sessão criada. Selecione outra aula ou deixe o campo em branco.');
                exit;
            }
            $chk->close();
        }

        /* Impede professor de dar sessão em mais de uma turma no mesmo dia */
        if ($professor_id > 0 && $professor_id === $professor_sub_id) {
            err('O titular e o substituto não podem ser a mesma pessoa.'); exit;
        }

        $checkProfSessao = function($pid) use ($conexao, $data_sessao) {
            if ($pid <= 0) return false;
            $chkProf = $conexao->prepare('SELECT id FROM tb_aula_pratica_sessoes WHERE data_sessao = ? AND (professor_id = ? OR professor_substituto_id = ?) LIMIT 1');
            if (!$chkProf) return false;
            $chkProf->bind_param('sii', $data_sessao, $pid, $pid);
            $chkProf->execute();
            $ret = $chkProf->get_result()->fetch_assoc();
            $chkProf->close();
            return $ret ? true : false;
        };

        if ($checkProfSessao($professor_id)) {
            err('O professor titular já está escalado para dar outra sessão neste mesmo dia.'); exit;
        }
        if ($checkProfSessao($professor_sub_id)) {
            err('O professor substituto já está escalado para dar outra sessão neste mesmo dia.'); exit;
        }

        $turma_val = $turma_id > 0 ? $turma_id : null;
        $aula_val  = $aula_id > 0 ? $aula_id : null;
        $prof_val  = $professor_id > 0 ? $professor_id : null;
        $prof_sub_val = $professor_sub_id > 0 ? $professor_sub_id : null;

        $st = $conexao->prepare('INSERT INTO tb_aula_pratica_sessoes (titulo, turma_id, aula_id, data_sessao, descricao, professor_id, professor_substituto_id) VALUES (?,?,?,?,?,?,?)');
        $st->bind_param('siissii', $titulo, $turma_val, $aula_val, $data_sessao, $descricao, $prof_val, $prof_sub_val);
        if (!$st->execute()) { $st->close(); err('Erro ao criar sessão.'); exit; }
        $newId = (int)$conexao->insert_id;
        $st->close();
        ok(['id' => $newId, 'msg' => 'Sessão criada com sucesso.']);
        exit;
    }

    /* ── Registrar resposta ── */
    if ($recurso === 'resposta') {
        $sessao_id = (int)($body['sessao_id'] ?? 0);
        $aluno_id  = (int)($body['aluno_id']  ?? 0);
        $tipo      = in_array($body['tipo'] ?? '', ['sem_leitura', 'com_leitura'])
                     ? $body['tipo'] : 'sem_leitura';
        $pontos    = $tipo === 'sem_leitura' ? 2 : 1;
        $pergunta  = trim($body['pergunta'] ?? '');

        if ($sessao_id <= 0) { err('Sessão inválida.'); exit; }
        if ($aluno_id  <= 0) { err('Selecione um aluno.'); exit; }

        /* Verifica se sessão existe */
        $cs = $conexao->prepare('SELECT id FROM tb_aula_pratica_sessoes WHERE id = ? LIMIT 1');
        $cs->bind_param('i', $sessao_id);
        $cs->execute();
        if (!$cs->get_result()->fetch_assoc()) { $cs->close(); err('Sessão não encontrada.'); exit; }
        $cs->close();

        /* Verifica se aluno existe */
        $ca = $conexao->prepare("SELECT id FROM tb_cad_alunos WHERE id = ? AND status = 'ativo' LIMIT 1");
        $ca->bind_param('i', $aluno_id);
        $ca->execute();
        if (!$ca->get_result()->fetch_assoc()) { $ca->close(); err('Aluno não encontrado.'); exit; }
        $ca->close();

        /* Verifica se aluno tem presença marcada na sessão */
        $cp = $conexao->prepare('SELECT id FROM tb_aula_pratica_presenca WHERE sessao_id = ? AND aluno_id = ? LIMIT 1');
        $cp->bind_param('ii', $sessao_id, $aluno_id);
        $cp->execute();
        if (!$cp->get_result()->fetch_assoc()) { $cp->close(); err('Este aluno não está com a presença marcada nesta sessão.'); exit; }
        $cp->close();

        $st = $conexao->prepare('INSERT INTO tb_aula_pratica_respostas (sessao_id, aluno_id, tipo, pontos, pergunta) VALUES (?,?,?,?,?)');
        $st->bind_param('iisis', $sessao_id, $aluno_id, $tipo, $pontos, $pergunta);
        if (!$st->execute()) { $st->close(); err('Erro ao registrar resposta.'); exit; }
        $respId = (int)$conexao->insert_id;
        $st->close();

        /* Retorna ranking atualizado da sessão */
        $stR = $conexao->prepare("
            SELECT a.id AS aluno_id, a.nome AS aluno_nome,
                   SUM(r.pontos) AS total_pontos,
                   SUM(CASE WHEN r.tipo='sem_leitura' THEN 1 ELSE 0 END) AS qtd_sem_leitura,
                   SUM(CASE WHEN r.tipo='com_leitura'  THEN 1 ELSE 0 END) AS qtd_com_leitura,
                   COUNT(r.id) AS total_respostas
            FROM tb_aula_pratica_respostas r
            INNER JOIN tb_cad_alunos a ON a.id = r.aluno_id
            WHERE r.sessao_id = ?
            GROUP BY a.id
            ORDER BY total_pontos DESC, a.nome ASC
        ");
        $stR->bind_param('i', $sessao_id);
        $stR->execute();
        $rr = $stR->get_result();
        $ranking = [];
        while ($row = $rr->fetch_assoc()) $ranking[] = $row;
        $stR->close();

        ok([
            'id'      => $respId,
            'pontos'  => $pontos,
            'tipo'    => $tipo,
            'msg'     => '+' . $pontos . ' ponto' . ($pontos > 1 ? 's' : '') . ' registrado' . ($pontos > 1 ? 's' : '') . '!',
            'ranking' => $ranking,
        ]);
        exit;
    }

    /* ── Encerrar / reabrir sessão ── */
    if ($recurso === 'encerrar') {
        $sessao_id = (int)($body['sessao_id'] ?? 0);
        $acao      = ($body['acao'] ?? 'encerrar');
        if ($sessao_id <= 0) { err('Sessão inválida.'); exit; }
        $novoStatus = ($acao === 'reabrir') ? 0 : 1;
        $st = $conexao->prepare('UPDATE tb_aula_pratica_sessoes SET encerrada = ? WHERE id = ?');
        $st->bind_param('ii', $novoStatus, $sessao_id);
        $st->execute();
        $st->close();
        ok(['msg' => ($novoStatus ? 'Sessão encerrada com sucesso.' : 'Sessão reaberta com sucesso.'), 'encerrada' => $novoStatus]);
        exit;
    }

    /* ── Marcar / desmarcar presença ── */
    if ($recurso === 'presenca') {
        $sessao_id = (int)($body['sessao_id'] ?? 0);
        $aluno_id  = (int)($body['aluno_id']  ?? 0);
        if ($sessao_id <= 0 || $aluno_id <= 0) { err('Dados inválidos.'); exit; }

        $st = $conexao->prepare('SELECT id FROM tb_aula_pratica_presenca WHERE sessao_id = ? AND aluno_id = ? LIMIT 1');
        $st->bind_param('ii', $sessao_id, $aluno_id);
        $st->execute();
        $rowCheck = $st->get_result()->fetch_assoc();
        $st->close();

        if ($rowCheck) {
            $stD = $conexao->prepare('DELETE FROM tb_aula_pratica_presenca WHERE id = ?');
            $stD->bind_param('i', $rowCheck['id']);
            $stD->execute();
            $stD->close();
            $presente = false;
        } else {
            $stI = $conexao->prepare('INSERT INTO tb_aula_pratica_presenca (sessao_id, aluno_id) VALUES (?,?)');
            $stI->bind_param('ii', $sessao_id, $aluno_id);
            $stI->execute();
            $stI->close();
            $presente = true;
        }

        $stC = $conexao->prepare('SELECT COUNT(*) AS total FROM tb_aula_pratica_presenca WHERE sessao_id = ?');
        $stC->bind_param('i', $sessao_id);
        $stC->execute();
        $total = (int)($stC->get_result()->fetch_assoc()['total'] ?? 0);
        $stC->close();

        ok(['presente' => $presente, 'total_presentes' => $total]);
        exit;
    }

    err('Recurso desconhecido.'); exit;
}

/* ══════════════════════════════════════════════════
   DELETE
══════════════════════════════════════════════════ */
if ($method === 'DELETE') {

    /* ── Excluir resposta ── */
    if ($recurso === 'resposta') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }
        $st = $conexao->prepare('DELETE FROM tb_aula_pratica_respostas WHERE id = ?');
        $st->bind_param('i', $id);
        $st->execute();
        $st->close();
        ok(['msg' => 'Resposta removida.']);
        exit;
    }

    /* ── Excluir sessão (e todas as respostas + presenças) ── */
    if ($recurso === 'sessao') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { err('ID inválido.'); exit; }

        $st1 = $conexao->prepare('DELETE FROM tb_aula_pratica_respostas WHERE sessao_id = ?');
        $st1->bind_param('i', $id);
        $st1->execute();
        $st1->close();

        $st2 = $conexao->prepare('DELETE FROM tb_aula_pratica_presenca WHERE sessao_id = ?');
        $st2->bind_param('i', $id);
        $st2->execute();
        $st2->close();

        $st3 = $conexao->prepare('DELETE FROM tb_aula_pratica_sessoes WHERE id = ?');
        $st3->bind_param('i', $id);
        $st3->execute();
        $st3->close();

        ok(['msg' => 'Sessão excluída.']);
        exit;
    }

    err('Recurso desconhecido.'); exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
