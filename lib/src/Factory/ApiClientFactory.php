<?php

declare(strict_types=1);

namespace WiQ\Sdk\Factory;

use WiQ\Sdk\Auth\AuthStrategyManager;
use WiQ\Sdk\Client\ApiClientInterface;
use WiQ\Sdk\Client\GuzzleApiClient;
use WiQ\Sdk\Infrastructure\Repository\GreatFoodRepository;
use WiQ\Sdk\GreatFoodSdk;

final readonly class ApiClientFactory
{
    public static function create(
        string $baseUrl,
        array $config,
        array $httpConfig = []
    ): GreatFoodSdk {

        $clientClass = $config['client_class'] ?? GuzzleApiClient::class;

        if (!is_subclass_of($clientClass, ApiClientInterface::class)) {
            throw new \InvalidArgumentException(
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

    private static function initClient(
        string $class,
        string $baseUrl,
        array $config,
        array $httpConfig
    ): ApiClientInterface {

        $authStrategy = new AuthStrategyManager()->resolve($config);

        return new $class(
            baseUrl: $baseUrl,
            authStrategy: $authStrategy,
            config: $httpConfig
        );
    }
}
