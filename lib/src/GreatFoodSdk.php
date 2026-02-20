<?php

declare(strict_types=1);

namespace WiQ\Sdk;

use WiQ\Sdk\Domain\Repository\MenuRepositoryInterface;
use WiQ\Sdk\Domain\Repository\ProductRepositoryInterface;
use WiQ\Sdk\Domain\DTO\Request\UpdateProductRequest;

readonly class GreatFoodSdk
{
    public function __construct(
        private MenuRepositoryInterface $menus,
        private ProductRepositoryInterface $products
    ) {}

    public function listMenus(): array
    {
        return $this->menus->getMenus();
    }

    public function listProducts(int $menuId): array
    {
        return $this->products->getProductsByMenuId($menuId);
    }

    public function updateProduct(int $menuId, int $productId, UpdateProductRequest $request): bool
    {
        return $this->products->updateProduct(
            $menuId,
            $productId,
            $request->toArray()
        );
    }
}
