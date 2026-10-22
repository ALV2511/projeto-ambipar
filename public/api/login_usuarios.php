<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST', true, 405);
    exit;
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (
    !is_string($email)
    || !is_string($password)
    || filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false
    || $password === ''
    || strlen($password) > 4096
) {
    header('Location: ../login.html?erro=campos');
    exit;
}

try {
    $conexao = require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
    $comando = $conexao->prepare(
        'SELECT id, username, passwords, email FROM usuarios WHERE email = :email LIMIT 1'
    );
    $comando->execute(['email' => trim($email)]);
    $usuario = $comando->fetch();

    if (!$usuario || !password_verify($password, $usuario['passwords'])) {
        header('Location: ../login.html?erro=credenciais');
        exit;
    }

    $registroAcesso = $conexao->prepare(
        'INSERT IGNORE INTO usuario_acessos (usuario_id, data_acesso) VALUES (:usuario_id, CURRENT_DATE)'
    );
    $registroAcesso->execute(['usuario_id' => $usuario['id']]);

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['email'] = $usuario['email'];

    header('Location: ../painel.html');
    exit;
} catch (PDOException | RuntimeException $exception) {
    error_log('Login failed: ' . $exception->getMessage());
    header('Location: ../login.html?erro=indisponivel');
    exit;
}
