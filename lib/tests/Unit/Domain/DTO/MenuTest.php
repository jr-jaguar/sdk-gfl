<?php

namespace WiQ\Tests\Unit\Domain\DTO;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Domain\DTO\Response\Menu;

#[Group('unit')]
class MenuTest extends TestCase
{
    public function testMenuMapping(): void
    {
        $data = [
            'id' => 3,
            'name' => 'Takeaway'
        ];

        $menu = Menu::fromArray($data);

        $this->assertEquals(3, $menu->id);
        $this->assertEquals('Takeaway', $menu->name);
    }
}
