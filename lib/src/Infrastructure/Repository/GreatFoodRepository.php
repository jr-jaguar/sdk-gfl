<?php

declare(strict_types=1);

namespace WiQ\Sdk\Infrastructure\Repository;

use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Repository\MenuRepositoryInterface;
use WiQ\Sdk\Domain\Repository\ProductRepositoryInterface;
use WiQ\Sdk\Domain\DTO\Response\Menu;
use WiQ\Sdk\Domain\DTO\Response\Product;
use WiQ\Sdk\Domain\Exceptions\SdkException;

readonly class GreatFoodRepository implements MenuRepositoryInterface, ProductRepositoryInterface
{
    public function __construct(
        private ApiClientInterface $client
    ) {}

    /** @throws SdkException */
    public function getMenus(): array
    {
        $response = $this->client->request('GET', '/menus');

        try {
            $data = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new SdkException("Invalid JSON response from API: " . $e->getMessage());
        }

        return array_map(
            static fn(array $item) => Menu::fromArray($item),
            $data['data'] ?? []
        );
    }

    /** @throws SdkException */
    public function getProductsByMenuId(int $menuId): array
    {
        $response = $this->client->request('GET', "/menu/{$menuId}/products");

        try {
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new SdkException("Invalid JSON response from API: " . $e->getMessage());
        }

        return array_map(
            static fn(array $item) => Product::fromArray($item),
            $data['data'] ?? []
        );
    }

    /** @throws SdkException */
    public function updateProduct(int $menuId, int $productId, array $payload): bool
    {
        $response = $this->client->request('PUT', "/menu/{$menuId}/product/{$productId}", [
            'json' => $payload
        ]);

        return $response->getStatusCode() === 200;
    }
}
