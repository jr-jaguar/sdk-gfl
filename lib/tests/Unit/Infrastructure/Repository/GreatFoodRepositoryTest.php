<?php

namespace WiQ\Tests\Unit\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Infrastructure\Repository\GreatFoodRepository;
use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Exceptions\ValidationException;
use WiQ\Sdk\Domain\DTO\Response\Menu;
use WiQ\Sdk\Domain\DTO\Response\Product;
use GuzzleHttp\Psr7\Response;

#[Group('unit')]
class GreatFoodRepositoryTest extends TestCase
{
    private $clientMock;
    private GreatFoodRepository $repository;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ApiClientInterface::class);
        $this->repository = new GreatFoodRepository($this->clientMock);
    }

    public function testGetMenusReturnsMappedDtos(): void
    {
        $json = json_encode([
            'data' => [
                ['id' => 1, 'name' => 'Takeaway'],
                ['id' => 2, 'name' => 'Dine In']
            ]
        ]);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', '/menus')
            ->willReturn(new Response(200, [], $json));

        $result = $this->repository->getMenus();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Menu::class, $result[0]);
        $this->assertEquals('Takeaway', $result[0]->name);
        $this->assertEquals(2, $result[1]->id);
    }

    public function testGetProductsByMenuIdUrlAndMapping(): void
    {
        $menuId = 7;
        $json = json_encode([
            'data' => [
                ['id' => 84, 'name' => 'chips']
            ]
        ]);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', "/menu/{$menuId}/products")
            ->willReturn(new Response(200, [], $json));

        $products = $this->repository->getProductsByMenuId($menuId);

        $this->assertCount(1, $products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals(84, $products[0]->id);
        $this->assertEquals('Chips', $products[0]->name);
    }

    public function testGetMenusThrowsValidationExceptionOnInvalidJson(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], 'not-a-json'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid JSON response format: Syntax error');

        $this->repository->getMenus();
    }

    public function testUpdateProductReturnsTrueOnSuccess(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], json_encode(['status' => 'success'])));

        $result = $this->repository->updateProduct(7, 84, ['name' => 'Chips']);

        $this->assertTrue($result);
    }
}
