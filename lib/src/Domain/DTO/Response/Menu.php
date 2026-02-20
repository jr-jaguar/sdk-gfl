<?php

declare(strict_types=1);

namespace WiQ\Sdk\Domain\DTO\Response;

readonly class Menu
{
    public function __construct(
        public int $id,
        public string $name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            name: (string)($data['name'] ?? '')
        );
    }
}
