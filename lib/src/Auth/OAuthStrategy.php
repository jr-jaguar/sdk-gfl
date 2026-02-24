<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Domain\Exceptions\AuthException;
use WiQ\Sdk\Domain\Exceptions\NetworkException;
use WiQ\Sdk\Domain\Exceptions\SdkException;
use WiQ\Sdk\Domain\Exceptions\ValidationException;

class OAuthStrategy implements AuthStrategyInterface
{
    public private(set) ?string $accessToken = null;
    private ?int $expiresAt = null;

    public function __construct(
        private readonly Credentials $credentials
    ) {
    }

    /**
     * @throws SdkException
     */
    public function authenticate(ApiClientInterface $client): void
    {
        if ($this->accessToken !== null && ($this->expiresAt > (time() + 10))) {
            return;
        }
        try {
            $response = $client->request('POST', '/auth_token', [
                'form_params' => [
                    'client_id' => $this->credentials->clientId,
                    'client_secret' => $this->credentials->clientSecret,
                    'grant_type' => $this->credentials->grantType,
                ],
                'auth_bypass' => true
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['access_token'])) {
                throw new AuthException('Failed to obtain access token from API.');
            }

            $this->accessToken = $data['access_token'];
            $this->expiresAt = time() + ($data['expires_in'] ?? 3600);

        } catch (\JsonException $e) {
            throw new AuthException("Failed to parse auth response: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            if ($e instanceof AuthException) {
                throw $e;
            }
            throw new NetworkException("Auth request failed: " . $e->getMessage(), 0, $e);
        }
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

    /**
     * @throws SdkException
     */
    public static function create(array $config): self
    {
        if (!self::supports($config)) {
            throw new ValidationException("Invalid configuration fields for OAuthStrategy (client_id/secret required)");
        }

        return new self(
            new Credentials(
                $config['client_id'],
                $config['client_secret']
            )
        );
    }
}
