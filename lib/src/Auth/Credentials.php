<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

final readonly class Credentials
{
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $grantType = 'client_credentials'
    ) {}
}
