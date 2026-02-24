<?php

namespace WiQ\Tests\Integration\Client;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use WiQ\Sdk\Client\GuzzleApiClient;
use WiQ\Sdk\Auth\AuthStrategyInterface;
use WiQ\Sdk\Domain\Exceptions\NetworkException;

#[Group('integration')]
class GuzzleApiClientTest extends TestCase
{
    private MockHandler $mockHandler;
    private $authMock;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->authMock = $this->createStub(AuthStrategyInterface::class);
    }

    public function testRequestIncludesAuthHeaders(): void
    {
        $this->authMock->method('getAuthHeaders')
            ->willReturn(['Authorization' => 'Bearer secret_token']);

        $this->mockHandler->append(new Response(200, [], json_encode(['data' => 'ok'])));

        $client = new GuzzleApiClient(
            'https://api.test',
            $this->authMock,
            ['handler' => HandlerStack::create($this->mockHandler)]
        );

        $client->request('GET', '/test');

        $lastRequest = $this->mockHandler->getLastRequest();
        $this->assertEquals('Bearer secret_token', $lastRequest->getHeaderLine('Authorization'));
    }

    public function testRequestWrapsGuzzleException(): void
    {
        $this->mockHandler->append(new Response(500, [], 'Server Error'));

        $client = new GuzzleApiClient(
            'https://api.test',
            null,
            ['handler' => HandlerStack::create($this->mockHandler)]
        );

        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('API Request failed');
        $this->expectExceptionCode(500);

        $client->request('GET', '/fail');
    }

    public function testRequestSendsJsonBody(): void
    {
        $this->mockHandler->append(new Response(200));

        $client = new GuzzleApiClient(
            'https://api.test',
            null,
            ['handler' => HandlerStack::create($this->mockHandler)]
        );

        $payload = ['name' => 'Test Product'];
        $client->request('POST', '/products', ['json' => $payload]);

        $lastRequest = $this->mockHandler->getLastRequest();
        $this->assertEquals('application/json', $lastRequest->getHeaderLine('Content-Type'));
        $this->assertEquals(json_encode($payload), (string)$lastRequest->getBody());
    }
}
