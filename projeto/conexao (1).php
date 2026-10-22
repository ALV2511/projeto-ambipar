<?php
$env = parse_ini_file(__DIR__ . '/.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];

try {
    $conexao = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: ". $e ->getMessage());
}
?>
