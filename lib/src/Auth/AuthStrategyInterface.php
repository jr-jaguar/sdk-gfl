<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

use WiQ\Sdk\Client\ApiClientInterface;

interface AuthStrategyInterface
{
    public function authenticate(ApiClientInterface $client): void;

    public function getAuthHeaders(): array;

    public static function supports(array $config): bool;

    public static function create(array $config): self;
}
