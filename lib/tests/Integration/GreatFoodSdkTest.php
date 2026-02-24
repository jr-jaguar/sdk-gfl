<?php

namespace WiQ\Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use WiQ\Sdk\GreatFoodSdk;
use WiQ\Sdk\Client\GuzzleApiClient;
use WiQ\Sdk\Infrastructure\Repository\GreatFoodRepository;
use WiQ\Sdk\Auth\OAuthStrategy;
use WiQ\Sdk\Auth\Credentials;

#[Group('integration')]
class GreatFoodSdkTest extends TestCase
{
    private MockHandler $mock;
    private GreatFoodSdk $sdk;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();
        $client = new GuzzleApiClient(
            'https://api.test',
            new OAuthStrategy(new Credentials('id', 'secret')),
            ['handler' => HandlerStack::create($this->mock)]
        );
        $repo = new GreatFoodRepository($client);
        $this->sdk = new GreatFoodSdk($repo, $repo);
    }

    public function testListMenusIntegration(): void
    {
        $this->mock->append(
            new Response(200, [], json_encode(['access_token' => 't'])),
            new Response(200, [], json_encode(['data' => [['id' => 1, 'name' => 'Menu 1']]]))
        );

        $menus = $this->sdk->listMenus();

        $this->assertCount(1, $menus);
        $this->assertEquals('Menu 1', $menus[0]->name);
    }

    public function testListProductsIntegration(): void
    {
        $this->mock->append(
            new Response(200, [], json_encode(['access_token' => 't'])),
            new Response(200, [], json_encode(['data' => [['id' => 10, 'name' => 'Pizza']]]))
        );

        $products = $this->sdk->listProducts(1);

        $this->assertCount(1, $products);
        $this->assertEquals('Pizza', $products[0]->name);
    }
}
