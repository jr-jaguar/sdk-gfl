<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use WiQ\Sdk\Domain\Exceptions\NetworkException;
use WiQ\Sdk\Domain\Exceptions\ValidationException;
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

} catch (ValidationException $e) {
    echo "[Config Error]: Check your .env file or SDK configuration: " . $e->getMessage() . PHP_EOL;
} catch (NetworkException $e) {
    echo "[Connection Error]: Could not reach the API. Please try again later. " . $e->getMessage() . PHP_EOL;
} catch (SdkException $e) {
    echo "[SDK Error]: A general error occurred: " . $e->getMessage() . PHP_EOL;
} catch (\Throwable $e) {
    echo "[Critical Error]: " . $e->getMessage() . PHP_EOL;
}
