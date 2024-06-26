<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeUser implements \JsonSerializable
{
    private function __construct(
        private Uuid $id,
        private string $name,
        private ?Uuid $owner,
    ) {}

    public static function create(Uuid $id, string $name, ?Uuid $owner): self
    {
        return new self($id, $name, $owner);
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function owner(): ?Uuid
    {
        return $this->owner;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name(),
            'owner' => $this->owner?->value(),
        ];
    }
}
