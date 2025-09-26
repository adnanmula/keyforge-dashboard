<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeGameLog implements \JsonSerializable
{
    public function __construct(
        public Uuid $id,
        public ?Uuid $gameId,
        public ?array $log,
        public ?Uuid $createdBy,
        public \DateTimeImmutable $createdAt,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'gameId' => $this->gameId?->value(),
            'log' => $this->log,
            'createdBy' => $this->createdBy?->value(),
            'createdAt' => $this->createdAt->format('Y-m-d'),
        ];
    }
}
