<?php

namespace WiQ\Tests\Unit\Domain\DTO;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Domain\DTO\Response\Product;

#[Group('unit')]
class ProductTest extends TestCase
{
    public function testProductMapping(): void
    {
        $data = [
            'id' => 84,
            'name' => 'chips'
        ];

        $product = Product::fromArray($data);

        $this->assertEquals(84, $product->id);
        $this->assertEquals('Chips', $product->name);
    }

    public function testProductConstructor(): void
    {
        $product = new Product(7, 'pizza');

        $this->assertEquals(7, $product->id);
        $this->assertEquals('Pizza', $product->name);
    }
}
