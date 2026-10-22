<?php
declare(strict_types=1);

$documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
$envPaths = [
    dirname($documentRoot) . DIRECTORY_SEPARATOR . '.env',
    $documentRoot . DIRECTORY_SEPARATOR . '.env',
];
$envPath = null;

foreach ($envPaths as $candidate) {
    if (is_file($candidate) && is_readable($candidate)) {
        $envPath = $candidate;
        break;
    }
}

if ($envPath === null) {
    throw new RuntimeException('Database configuration file is missing or unreadable.');
}

$settings = parse_ini_file($envPath, false, INI_SCANNER_RAW);
if ($settings === false) {
    throw new RuntimeException('Database configuration file could not be parsed.');
}

$databaseName = $settings['DB_NAME'] ?? $settings['DB__NAME'] ?? null;
$requiredSettings = [
    'DB_HOST' => $settings['DB_HOST'] ?? null,
    'DB_NAME' => $databaseName,
    'DB_USER' => $settings['DB_USER'] ?? null,
];

foreach ($requiredSettings as $name => $value) {
    if (!is_string($value) || $value === '') {
        throw new RuntimeException(sprintf('Required database setting %s is missing.', $name));
    }
}

$databasePassword = $settings['DB_PASS'] ?? null;
if (!is_string($databasePassword)) {
    throw new RuntimeException('Required database setting DB_PASS is missing.');
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $requiredSettings['DB_HOST'],
    $requiredSettings['DB_NAME']
);

return new PDO($dsn, $requiredSettings['DB_USER'], $databasePassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
