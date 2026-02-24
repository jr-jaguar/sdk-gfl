<?php

declare(strict_types=1);

namespace WiQ\Sdk\Domain\Repository;

use WiQ\Sdk\Domain\DTO\Response\Product;
use WiQ\Sdk\Domain\Exceptions\SdkException;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     * @throws SdkException
     */
    public function getProductsByMenuId(int $menuId): array;

    /**
     * @throws SdkException
     */
    public function updateProduct(int $menuId, int $productId, array $payload): bool;
}
