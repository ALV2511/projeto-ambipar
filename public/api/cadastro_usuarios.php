<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST', true, 405);
    exit;
}

$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (
    !is_string($username)
    || !is_string($email)
    || !is_string($password)
    || trim($username) === ''
    || filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false
    || $password === ''
    || strlen(trim($username)) > 100
    || strlen(trim($email)) > 254
    || strlen($password) > 4096
) {
    header('Location: ../cadastro.html?erro=campos');
    exit;
}

try {
    $conexao = require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO usuarios (username, passwords, email) VALUES (:username, :password, :email)';
    $comando = $conexao->prepare($sql);
    $comando->execute([
        'username' => trim($username),
        'password' => $hashPassword,
        'email' => trim($email),
    ]);

    header('Location: ../cadastro-concluido.html');
    exit;
} catch (PDOException | RuntimeException $exception) {
    error_log('Registration failed: ' . $exception->getMessage());
    header('Location: ../cadastro.html?erro=cadastro');
    exit;
}
