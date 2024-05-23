<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeDeckUserData implements \JsonSerializable
{
    private function __construct(
        public Uuid $id,
        public ?Uuid $owner,
        public int $wins,
        public int $losses,
        public string $notes,
        public array $tags = [],
    ) {}

    public static function from(Uuid $id, ?Uuid $owner, int $wins, int $losses, string $notes, array $tags = []): self
    {
        return new self($id, $owner, $wins, $losses, $notes, $tags);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'owner' => $this->owner?->value(),
            'wins' => $this->wins,
            'losses' => $this->losses,
            'notes' => $this->notes,
            'tags' => $this->tags,
        ];
    }
}
