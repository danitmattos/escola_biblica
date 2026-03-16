<?php
session_start();
include 'libs/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_email = trim($_POST['usuario_email']);
    $senha = $_POST['usuario_senha'];

    // Usando prepared statement para maior segurança
    $stmt = $conexao->prepare("SELECT * FROM tb_usuarios WHERE usuario_email = ? LIMIT 1");
    $stmt->bind_param("s", $usuario_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['usuario_senha'])) {
            $_SESSION['usuario']       = trim($user['nome'] . ' ' . $user['sobrenome']);
            $_SESSION['usuario_email'] = $usuario_email;
            $_SESSION['usuario_id']    = $user['id'];
            header('Location: index.php');
            exit();
        } else {
            header('Location: login.php?erro=' . urlencode('Senha incorreta. Tente novamente.'));
            exit();
        }
    } else {
        header('Location: login.php?erro=' . urlencode('Usuário não encontrado.'));
        exit();
    }
    $stmt->close();
} else {
    header('Location: login.php');
    exit();
}
