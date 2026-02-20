<?php

declare(strict_types=1);

namespace WiQ\Sdk\Domain\DTO\Request;

readonly class UpdateProductRequest
{
    public function __construct(
        public string $name
    ) {
        if (empty(trim($this->name))) {
            throw new \InvalidArgumentException("Product name cannot be empty.");
        }
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
