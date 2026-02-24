<?php
declare(strict_types=1);

namespace WiQ\Sdk\Domain\Repository;

use WiQ\Sdk\Domain\DTO\Response\Menu;
use WiQ\Sdk\Domain\Exceptions\SdkException;

interface MenuRepositoryInterface
{
    /**
     * @return Menu[]
     * @throws SdkException
     */
    public function getMenus(): array;
}
