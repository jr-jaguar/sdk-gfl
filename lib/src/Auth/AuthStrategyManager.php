<?php

declare(strict_types=1);

namespace WiQ\Sdk\Auth;

final readonly class AuthStrategyManager
{
    private array $strategyClasses;

    public function __construct()
    {
        $this->strategyClasses = [
            OAuthStrategy::class,
        ];
    }

    public function resolve(array $config): AuthStrategyInterface
    {
        foreach ($this->strategyClasses as $className) {
            if ($className::supports($config)) {
                return $className::create($config); // Больше никаких match!
            }
        }
        throw new \InvalidArgumentException("No strategy found.");
    }

}
