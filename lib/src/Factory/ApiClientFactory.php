<?php

declare(strict_types=1);

namespace WiQ\Sdk\Factory;

use WiQ\Sdk\Auth\AuthStrategyManager;
use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Client\GuzzleApiClient;
use WiQ\Sdk\Domain\Exceptions\InternalException;
use WiQ\Sdk\Domain\Exceptions\SdkException;
use WiQ\Sdk\Domain\Exceptions\ValidationException;
use WiQ\Sdk\Infrastructure\Repository\GreatFoodRepository;
use WiQ\Sdk\GreatFoodSdk;

final readonly class ApiClientFactory
{
    /**
     * @throws SdkException
     */
    public static function create(
        string $baseUrl,
        array $config,
        array $httpConfig = []
    ): GreatFoodSdk {

        $clientClass = $config['client_class'] ?? GuzzleApiClient::class;

        if (!is_subclass_of($clientClass, ApiClientInterface::class)) {
            throw new ValidationException(
                sprintf("Class %s must implement ApiClientInterface", $clientClass)
            );
        }

        $client = self::initClient($clientClass, $baseUrl, $config, $httpConfig);

        $repository = new GreatFoodRepository($client);

        return new GreatFoodSdk(
            menus: $repository,
            products: $repository
        );
    }

    /**
     * @throws SdkException
     */
    private static function initClient(
        string $class,
        string $baseUrl,
        array $config,
        array $httpConfig
    ): ApiClientInterface {
        try {
        $authStrategy = new AuthStrategyManager()->resolve($config);

        return new $class(
            baseUrl: $baseUrl,
            authStrategy: $authStrategy,
            config: $httpConfig
        );
        } catch (\TypeError | \Error $e) {
            throw new ValidationException("Invalid client configuration for {$class}: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw new InternalException("An unexpected error occurred during SDK initialization: " . $e->getMessage(), 0, $e);
        }
    }
}
