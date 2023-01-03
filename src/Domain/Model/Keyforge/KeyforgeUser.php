<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeUser implements \JsonSerializable
{
    private function __construct(
        private Uuid $id,
        private string $name,
        private bool $external,
    ) {}

    public static function create(Uuid $id, string $name, bool $external): self
    {
        return new self($id, $name, $external);
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function external(): bool
    {
        return $this->external;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name(),
            'external' => $this->external(),
        ];
    }
}
