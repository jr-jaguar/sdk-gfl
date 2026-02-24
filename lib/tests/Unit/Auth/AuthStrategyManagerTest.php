<?php

namespace WiQ\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use WiQ\Sdk\Auth\AuthStrategyManager;
use WiQ\Sdk\Auth\OAuthStrategy;
use WiQ\Sdk\Domain\Exceptions\ValidationException;

#[Group('unit')]
class AuthStrategyManagerTest extends TestCase
{
    private AuthStrategyManager $manager;

    protected function setUp(): void
    {
        $this->manager = new AuthStrategyManager();
    }

    public function testResolveReturnsOAuthStrategy(): void
    {
        $config = [
            'method'        => 'oauth',
            'client_id'     => 'test_id',
            'client_secret' => 'test_secret',
        ];

        $strategy = $this->manager->resolve($config);

        $this->assertInstanceOf(OAuthStrategy::class, $strategy);
    }

    public function testResolveThrowsExceptionForUnknownMethod(): void
    {
        $config = [
            'method' => 'unsupported_method',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("No auth strategy found for method: 'unsupported_method'");

        $this->manager->resolve($config);
    }

    public function testResolveThrowsExceptionWhenConfigIsMissingFields(): void
    {
        $config = [
            'method'    => 'oauth',
            'client_id' => 'only_id_no_secret',
        ];

        $this->expectException(ValidationException::class);
        $this->manager->resolve($config);
    }
}
