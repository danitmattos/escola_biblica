<?php
if (!session_start()) {
  echo json_encode([
    'ok'=>false,
    'msg'=>'Falha ao iniciar sessão',
    'session_id'=>session_id(),
    'debug'=>['headers_sent'=>headers_sent()]
  ]);
  exit;
}

require_once __DIR__ . '/libs/connection.php';

$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
if (!$usuario_id) {
  http_response_code(401);
  echo json_encode([
    'ok'=>false,
    'msg'=>'Usuário não autenticado',
    'debug_session'=>$_SESSION,
    'session_id'=>session_id()
  ]);
  exit;
}



$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // Se pedir todos os comentados do usuário
  if (isset($_GET['todos_comentados'])) {
    $capitulo = $_GET['capitulo'] ?? '';
    $livro = $_GET['livro'] ?? '';
    $ids = [];
    if ($capitulo && $livro) {
      $prefix = $livro.'-'.$capitulo.':';
      $stmt = $conexao->prepare('SELECT versiculo_id FROM tb_biblia_comentarios WHERE usuario_id=? AND versiculo_id LIKE CONCAT(?, "%")');
      $stmt->bind_param('is', $usuario_id, $prefix);
      $stmt->execute();
      $versiculo_id_result = null;
      $stmt->bind_result($versiculo_id_result);
      while ($stmt->fetch()) $ids[] = $versiculo_id_result;
      $stmt->close();
    }
    echo json_encode(['ok'=>true,'comentados'=>$ids]);
    exit;
  }
  $versiculo_id = $_GET['versiculo_id'] ?? '';
  if (!$versiculo_id) {
    echo json_encode(['ok'=>true,'comentario'=>'']);
    exit;
  }
  $stmt = $conexao->prepare('SELECT comentario FROM tb_biblia_comentarios WHERE usuario_id=? AND versiculo_id=? LIMIT 1');
  $stmt->bind_param('is', $usuario_id, $versiculo_id);
  $stmt->execute();
  $coment = '';
  $stmt->bind_result($coment);
  $stmt->fetch();
  $stmt->close();
  echo json_encode(['ok'=>true,'comentario'=>$coment ?: '']);
  exit;
}

if ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  $versiculo_id = $input['versiculo_id'] ?? '';
  $comentario = $input['comentario'] ?? '';
  if (!$versiculo_id) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'Versículo inválido','debug'=>'versiculo_id vazio','input'=>$input]);
    exit;
  }
  // Upsert
  $stmt = $conexao->prepare('INSERT INTO tb_biblia_comentarios (usuario_id, versiculo_id, comentario, data_atualizacao) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE comentario=VALUES(comentario), data_atualizacao=NOW()');
  if (!$stmt) {
    http_response_code(500);
    echo json_encode([
      'ok'=>false,
      'msg'=>'Erro ao preparar statement.',
      'debug'=>mysqli_error($conexao),
      'usuario_id'=>$usuario_id,
      'versiculo_id'=>$versiculo_id,
      'comentario'=>$comentario
    ]);
    exit;
  }
  $stmt->bind_param('iss', $usuario_id, $versiculo_id, $comentario);
  $ok = $stmt->execute();
  $debug = $stmt->error;
  $stmt->close();
  if($ok) echo json_encode(['ok'=>true]);
  else {
    http_response_code(500);
    echo json_encode([
      'ok'=>false,
      'msg'=>'Erro ao salvar comentário.',
      'debug'=>$debug,
      'usuario_id'=>$usuario_id,
      'versiculo_id'=>$versiculo_id,
      'comentario'=>$comentario
    ]);
  }
  exit;
}

http_response_code(405);
echo json_encode(['ok'=>false,'msg'=>'Método não permitido']);
