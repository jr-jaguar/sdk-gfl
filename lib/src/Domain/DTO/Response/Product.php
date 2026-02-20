<?php

declare(strict_types=1);

namespace WiQ\Sdk\Domain\DTO\Response;

class Product
{
    public function __construct(
        public readonly int $id,
        public string $name {
            get => ucfirst($this->name);
        }
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            name: (string)($data['name'] ?? '')
        );
    }
}
