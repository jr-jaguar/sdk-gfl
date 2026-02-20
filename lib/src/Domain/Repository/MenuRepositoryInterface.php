<?php
declare(strict_types=1);

namespace WiQ\Sdk\Domain\Repository;

use WiQ\Sdk\Domain\DTO\Response\Menu;

interface MenuRepositoryInterface
{
    /** @return Menu[] */
    public function getMenus(): array;
}
