<?php

declare(strict_types=1);

namespace WiQ\Sdk\Infrastructure\Repository;

use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Exceptions\NetworkException;
use WiQ\Sdk\Domain\Exceptions\ValidationException;
use WiQ\Sdk\Domain\Repository\MenuRepositoryInterface;
use WiQ\Sdk\Domain\Repository\ProductRepositoryInterface;
use WiQ\Sdk\Domain\DTO\Response\Menu;
use WiQ\Sdk\Domain\DTO\Response\Product;
use WiQ\Sdk\Domain\Exceptions\SdkException;

readonly class GreatFoodRepository implements MenuRepositoryInterface, ProductRepositoryInterface
{
    public function __construct(
        private ApiClientInterface $client
    ) {
    }

    /** @throws SdkException */
    public function getMenus(): array
    {
        try {
            $response = $this->client->request('GET', '/menus');

            $data = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            return array_map(
                static fn(array $item) => Menu::fromArray($item),
                $data['data'] ?? []
            );
        } catch (\JsonException $e) {
            throw new ValidationException("Invalid JSON response format: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw new NetworkException("Network error while fetching menus: " . $e->getMessage(), 0, $e);
        }
    }

    /** @throws SdkException */
    public function getProductsByMenuId(int $menuId): array
    {
        try {
            $response = $this->client->request('GET', "/menu/{$menuId}/products");

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return array_map(
                static fn(array $item) => Product::fromArray($item),
                $data['data'] ?? []
            );
        } catch (\JsonException $e) {
            throw new ValidationException("Invalid products JSON for menu {$menuId}: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw new NetworkException("Connection failed for menu {$menuId}: " . $e->getMessage(), 0, $e);
        }
    }

    /** @throws SdkException */
    public function updateProduct(int $menuId, int $productId, array $payload): bool
    {
        try {
            $response = $this->client->request('PUT', "/menu/{$menuId}/product/{$productId}", [
                'json' => $payload
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            throw new NetworkException("Failed to update product {$productId}: " . $e->getMessage(), 0, $e);
        }
    }
}
