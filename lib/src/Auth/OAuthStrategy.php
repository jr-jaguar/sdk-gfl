<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

use WiQ\Sdk\Client\ApiClientInterface;

class OAuthStrategy implements AuthStrategyInterface
{
    public private(set) ?string $accessToken = null;
    private ?int $expiresAt = null;

    public function __construct(
        private readonly Credentials $credentials
    ) {}

    public function authenticate(ApiClientInterface $client): void
    {
        if ($this->accessToken !== null && ($this->expiresAt > (time() + 10))) {
            return;
        }

        $response = $client->request('POST', '/auth_token', [
            'form_params' => [
                'client_id'     => $this->credentials->clientId,
                'client_secret' => $this->credentials->clientSecret,
                'grant_type'    => $this->credentials->grantType,
            ],
            'auth_bypass' => true
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Failed to obtain access token from API.');
        }

        $this->accessToken = $data['access_token'];
        $this->expiresAt = time() + ($data['expires_in'] ?? 3600);
    }

    public function getAuthHeaders(): array
    {
        return $this->accessToken
            ? ['Authorization' => "Bearer {$this->accessToken}"]
            : [];
    }

    public static function supports(array $config): bool
    {
        return ($config['method'] ?? '') === 'oauth'
            && isset($config['client_id'], $config['client_secret']);
    }

    public static function create(array $config): self
    {
        if (!self::supports($config)) {
            throw new \InvalidArgumentException("Invalid configuration for OAuthStrategy");
        }

        return new self(
            new Credentials(
                $config['client_id'],
                $config['client_secret']
            )
        );
    }
}
