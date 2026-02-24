<?php

namespace WiQ\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Auth\OAuthStrategy;
use WiQ\Sdk\Auth\Credentials;
use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Exceptions\AuthException;
use GuzzleHttp\Psr7\Response;

#[Group('unit')]
class OAuthStrategyTest extends TestCase
{
    private Credentials $credentials;

    protected function setUp(): void
    {
        $this->credentials = new Credentials('client_id', 'client_secret');
    }

    public function testAuthenticateSuccess(): void
    {
        $clientMock = $this->createMock(ApiClientInterface::class);
        $strategy = new OAuthStrategy($this->credentials);

        $jsonResponse = json_encode([
            'access_token' => 'test_token_123',
            'expires_in' => 3600
        ]);

        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', '/auth_token')
            ->willReturn(new Response(200, [], $jsonResponse));

        $strategy->authenticate($clientMock);

        $this->assertEquals('test_token_123', $strategy->accessToken);
        $this->assertArrayHasKey('Authorization', $strategy->getAuthHeaders());
        $this->assertEquals('Bearer test_token_123', $strategy->getAuthHeaders()['Authorization']);
    }

    public function testAuthenticateCachesToken(): void
    {
        $clientMock = $this->createMock(ApiClientInterface::class);
        $strategy = new OAuthStrategy($this->credentials);

        $jsonResponse = json_encode([
            'access_token' => 'cached_token',
            'expires_in' => 3600
        ]);

        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], $jsonResponse));

        $strategy->authenticate($clientMock);
        $strategy->authenticate($clientMock);

        $this->assertEquals('cached_token', $strategy->accessToken);
    }

    public function testAuthenticateThrowsExceptionIfTokenMissing(): void
    {
        $clientMock = $this->createMock(ApiClientInterface::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], json_encode(['error' => 'invalid_client'])));

        $strategy = new OAuthStrategy($this->credentials);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Failed to obtain access token from API.');

        $strategy->authenticate($clientMock);
    }

    public function testAuthenticateThrowsExceptionOnInvalidJson(): void
    {
        $clientMock = $this->createMock(ApiClientInterface::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], 'invalid-non-json-string'));

        $strategy = new OAuthStrategy($this->credentials);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Failed to parse auth response');

        $strategy->authenticate($clientMock);
    }
}
