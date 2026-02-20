<?php

declare(strict_types=1);

namespace WiQ\Sdk\Client;

use Psr\Http\Message\ResponseInterface;
use WiQ\Sdk\Domain\Exceptions\SdkException;

interface ApiClientInterface
{
    /**
     * @throws SdkException
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface;
}
