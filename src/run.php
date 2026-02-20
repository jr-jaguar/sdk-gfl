<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use WiQ\Sdk\Factory\ApiClientFactory;
use App\Service\MenuService;
use WiQ\Sdk\Domain\Exceptions\SdkException;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$config = [
    'client_class'  => $_ENV['API_CLIENT_IMPLEMENTATION'] ?? null,
    'method'        => $_ENV['API_AUTH_METHOD'] ?? 'oauth',
    'client_id'     => $_ENV['API_CLIENT_ID'] ?? '',
    'client_secret' => $_ENV['API_CLIENT_SECRET'] ?? '',
];

$baseUrl = $_ENV['API_BASE_URL'] ?? 'https://api.greatfood.ltd';

try {
    echo "--- Initializing SDK from Environment ---\n";

    $sdk = ApiClientFactory::create(
        baseUrl: $baseUrl,
        config: $config
    );

    $orchestrator = new MenuService($sdk);

    $orchestrator->processTakeawayMenu();
    $orchestrator->updateSpecificProduct(3, 84, 'Chips');

} catch (SdkException $e) {
    echo "[SDK Error]: " . $e->getMessage() . PHP_EOL;
} catch (Throwable $e) {
    echo "[Critical Error]: " . $e->getMessage() . PHP_EOL;
}
