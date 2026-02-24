<?php

declare(strict_types=1);

namespace App\Tests\Scenario;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use WiQ\Sdk\GreatFoodSdk;
use WiQ\Sdk\Client\GuzzleApiClient;
use WiQ\Sdk\Auth\OAuthStrategy;
use WiQ\Sdk\Auth\Credentials;
use WiQ\Sdk\Infrastructure\Repository\GreatFoodRepository;
use WiQ\Sdk\Domain\DTO\Request\UpdateProductRequest;

#[Group('scenario')]
class BusinessRequirementsTest extends TestCase
{
    private MockHandler $mock;
    private GreatFoodSdk $sdk;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);

        $client = new GuzzleApiClient(
            'https://api.test',
            new OAuthStrategy(new Credentials('id', 'secret')),
            ['handler' => $handlerStack]
        );
        $repo = new GreatFoodRepository($client);
        $this->sdk = new GreatFoodSdk($repo, $repo);
    }

    public function testCollectTakeawayMenuData(): void
    {
        $this->mock->append(
            new Response(200, [], json_encode(['access_token' => 't'])),
            new Response(200, [], json_encode(['data' => [['id' => 3, 'name' => 'Takeaway']]])),
            new Response(200, [], json_encode(['data' => [
                ['id' => 4, 'name' => 'Burger'],
                ['id' => 5, 'name' => 'Chips'],
                ['id' => 99, 'name' => 'Lasagna']
            ]]))
        );

        $menus = $this->sdk->listMenus();
        $takeaway = current(array_filter($menus, fn($m) => $m->name === 'Takeaway'));

        $this->assertNotFalse($takeaway);
        $products = $this->sdk->listProducts($takeaway->id);

        $this->assertCount(3, $products);
        $this->assertEquals(4, $products[0]->id);
        $this->assertEquals('Burger', $products[0]->name);
    }

    public function testUpdateProductCorrection(): void
    {
        $this->mock->append(
            new Response(200, [], json_encode(['access_token' => 't'])),
            new Response(200, [], json_encode(['status' => 'ok']))
        );

        $request = new UpdateProductRequest('Chips');
        $result = $this->sdk->updateProduct(7, 84, $request);

        $this->assertTrue($result);

        $lastRequest = $this->mock->getLastRequest();
        $this->assertEquals('PUT', $lastRequest->getMethod());
        $this->assertStringContainsString('/menu/7/product/84', (string)$lastRequest->getUri());

        $payload = json_decode((string)$lastRequest->getBody(), true);
        $this->assertEquals('Chips', $payload['name']);
    }
}
