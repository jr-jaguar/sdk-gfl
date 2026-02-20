<?php

declare(strict_types=1);

namespace WiQ\Sdk\Client;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use WiQ\Sdk\Auth\AuthStrategyInterface;

readonly class FileMockApiClient implements ApiClientInterface
{
    public function __construct(
        private string $baseUrl = '',
        private ?AuthStrategyInterface $authStrategy = null,
        private array $config = [],
        private string $responsesDir = __DIR__ . '/../../../responses/'
    ) {}

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $path = parse_url($uri, PHP_URL_PATH);

        $file = match (true) {
            $path === '/auth_token' => 'token.json',
            $path === '/menus' => 'menus.json',
            str_contains($path, '/products') => 'menu-products.json',
            str_contains($path, '/product/') && $method === 'PUT' => null,
            default => throw new \Exception("Fixture not found for path: $path"),
        };

        if ($file === null) {
            return new Response(200, [], json_encode(['status' => 'updated']));
        }

        $filePath = $this->responsesDir . $file;

        if (!file_exists($filePath)) {
            throw new \Exception("Fixture file not found: $filePath");
        }

        return new Response(200, [], file_get_contents($filePath));
    }
}
