<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

use WiQ\Sdk\Domain\Exceptions\SdkException;
use WiQ\Sdk\Domain\Exceptions\ValidationException;

final readonly class AuthStrategyManager
{
    private array $strategyClasses;

    public function __construct()
    {
        $this->strategyClasses = [
            OAuthStrategy::class,
        ];
    }

    /**
     * @throws SdkException
     */
    public function resolve(array $config): AuthStrategyInterface
    {
        foreach ($this->strategyClasses as $className) {
            if ($className::supports($config)) {
                return $className::create($config);
            }
        }
        throw new ValidationException(
            sprintf("No auth strategy found for method: '%s'", $config['method'] ?? 'unknown')
        );
    }

}
