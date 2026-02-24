<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Exceptions\SdkException;

interface AuthStrategyInterface
{
    /** @throws SdkException */
    public function authenticate(ApiClientInterface $client): void;

    public function getAuthHeaders(): array;

    public static function supports(array $config): bool;

    /** @throws SdkException */
    public static function create(array $config): self;
}
