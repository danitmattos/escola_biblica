<?php
/**
 * libs/helpers.php — Helpers compartilhados entre todos os endpoints
 *
 * Inclui: autenticação de sessão, respostas JSON, CSRF, validação de data.
 * Uso:  require_once __DIR__ . '/libs/helpers.php';
 *       — já faz session_start(), header JSON e verifica sessão.
 */

// Evita dupla inclusão
if (defined('HELPERS_LOADED')) return;
define('HELPERS_LOADED', true);

session_start();
header('Content-Type: application/json; charset=utf-8');

/* ── Autenticação ─────────────────────────────────────────── */
function requireAuth(): void {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
        exit;
    }
}

/* ── CSRF ──────────────────────────────────────────────────── */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfCheck(): void {
    $method = $_SERVER['REQUEST_METHOD'];
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) return;

    $token = $_SERVER['HTTP_X_CSRF_TOKEN']
          ?? $_POST['csrf_token']
          ?? '';

    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido. Recarregue a página.']);
        exit;
    }
}

/* ── Respostas JSON ───────────────────────────────────────── */
function ok(array $extra = []): void {
    echo json_encode(array_merge(['ok' => true], $extra));
}

function err(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
}

/* ── Validação ────────────────────────────────────────────── */
function validaData(string $s): bool {
    return $s !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);
}

/* ── Conexão (inclui .env) ────────────────────────────────── */
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/connection.php';

// Desativa exceções do mysqli (erros tratados manualmente)
mysqli_report(MYSQLI_REPORT_OFF);
