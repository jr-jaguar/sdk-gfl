<?php

declare(strict_types=1);

namespace WiQ\Sdk\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use WiQ\Sdk\Auth\AuthStrategyInterface;
use WiQ\Sdk\Domain\Exceptions\NetworkException;
use WiQ\Sdk\Domain\Exceptions\SdkException;

class GuzzleApiClient implements ApiClientInterface
{
    private Guzzle $guzzle;

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?AuthStrategyInterface $authStrategy = null,
        array $config = []
    ) {
        $this->guzzle = new Guzzle(array_merge([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10.0,
        ], $config));
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        if ($this->authStrategy && !($options['auth_bypass'] ?? false)) {

            $this->authStrategy->authenticate($this);

            $options['headers'] = array_merge(
                $options['headers'] ?? [],
                $this->authStrategy->getAuthHeaders()
            );
        }

        try {
            return $this->guzzle->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            throw new NetworkException(
                message: "API Request failed: " . $e->getMessage(),
                code: $e->getCode(),
                previous: $e
            );
        }
    }
}
