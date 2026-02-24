<?php

namespace WiQ\Tests\Unit\Domain\DTO\Request;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Domain\DTO\Request\UpdateProductRequest;
use WiQ\Sdk\Domain\Exceptions\ValidationException;

#[Group('unit')]
class UpdateProductRequestTest extends TestCase
{
    public function testToArrayContainsCorrectData(): void
    {
        $name = 'Classic Burger';
        $request = new UpdateProductRequest($name);

        $expected = ['name' => $name];

        $this->assertEquals($expected, $request->toArray());
    }

    public function testEmptyNameThrowsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        new UpdateProductRequest('');
    }

    public function testNameWithOnlySpacesThrowsValidationException(): void
    {
        $this->expectException(ValidationException::class);

        new UpdateProductRequest('   ');
    }
}
