<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET', true, 405);
    echo json_encode(['error' => 'Método não permitido.']);
    exit;
}

if (!isset($_SESSION['usuario_id'], $_SESSION['username'], $_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'É necessário entrar na conta.']);
    exit;
}

try {
    $conexao = require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
    $comando = $conexao->prepare(
        'SELECT COUNT(*) FROM usuario_acessos WHERE usuario_id = :usuario_id'
    );
    $comando->execute(['usuario_id' => $_SESSION['usuario_id']]);
    $diasDeAcesso = (int) $comando->fetchColumn();

    $xp = 20 + ($diasDeAcesso * 10);
    $niveis = [
        ['nivel' => 1, 'nome' => 'Semente', 'inicio' => 0, 'proximo' => 50],
        ['nivel' => 2, 'nome' => 'Explorador', 'inicio' => 50, 'proximo' => 150],
        ['nivel' => 3, 'nome' => 'Protetor', 'inicio' => 150, 'proximo' => 300],
        ['nivel' => 4, 'nome' => 'Embaixador', 'inicio' => 300, 'proximo' => null],
    ];

    $nivelAtual = $niveis[0];
    foreach ($niveis as $nivel) {
        if ($xp < $nivel['inicio']) {
            break;
        }

        $nivelAtual = $nivel;
    }

    $progresso = $nivelAtual['proximo'] === null
        ? 100
        : (int) round(
            (($xp - $nivelAtual['inicio']) / ($nivelAtual['proximo'] - $nivelAtual['inicio'])) * 100
        );

    $insignias = [
        [
            'nome' => 'Primeiro passo',
            'descricao' => 'Criou sua conta na Ambipar.',
            'icone' => '🌱',
            'conquistada' => true,
        ],
        [
            'nome' => 'Boas-vindas',
            'descricao' => 'Fez seu primeiro acesso.',
            'icone' => '👋',
            'conquistada' => $diasDeAcesso >= 1,
        ],
        [
            'nome' => 'Presença constante',
            'descricao' => 'Acessou sua conta em 5 dias diferentes.',
            'icone' => '🌿',
            'conquistada' => $diasDeAcesso >= 5,
        ],
        [
            'nome' => 'Embaixador do planeta',
            'descricao' => 'Acessou sua conta em 10 dias diferentes.',
            'icone' => '🌎',
            'conquistada' => $diasDeAcesso >= 10,
        ],
    ];

    echo json_encode([
        'usuario' => [
            'nome' => $_SESSION['username'],
            'email' => $_SESSION['email'],
        ],
        'nivel' => [
            'numero' => $nivelAtual['nivel'],
            'nome' => $nivelAtual['nome'],
            'xp' => $xp,
            'proximo_nivel_xp' => $nivelAtual['proximo'],
            'progresso_percentual' => $progresso,
        ],
        'estatisticas' => [
            'dias_de_acesso' => $diasDeAcesso,
            'insignias_conquistadas' => count(array_filter(
                $insignias,
                static fn (array $insignia): bool => $insignia['conquistada']
            )),
            'total_insignias' => count($insignias),
        ],
        'insignias' => $insignias,
    ], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (PDOException | RuntimeException | JsonException $exception) {
    error_log('Dashboard data failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar o painel. Tente novamente mais tarde.']);
}
