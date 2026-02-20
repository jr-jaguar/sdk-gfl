<?php
declare(strict_types=1);

namespace WiQ\Sdk\Domain\Repository;

use WiQ\Sdk\Domain\DTO\Response\Product;

interface ProductRepositoryInterface
{
    /** @return Product[] */
    public function getProductsByMenuId(int $menuId): array;

    public function updateProduct(int $menuId, int $productId, array $payload): bool;
}
