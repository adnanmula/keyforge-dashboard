<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentDeckType;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentFaction;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class GwentDeck implements \JsonSerializable
{
    public function __construct(
        private Uuid $id,
        private GwentFaction $faction,
        private ?Uuid $archetype,
        private GwentDeckType $type,
        private string $name,
        private int $wins,
        private int $losses,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function faction(): GwentFaction
    {
        return $this->faction;
    }

    public function archetype(): ?Uuid
    {
        return $this->archetype;
    }

    public function type(): GwentDeckType
    {
        return $this->type;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function wins(): int
    {
        return $this->wins;
    }

    public function losses(): int
    {
        return $this->losses;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'faction' => $this->faction(),
            'archetype' => $this->archetype(),
            'type' => $this->type(),
            'name' => $this->name(),
            'wins' => $this->wins(),
            'losses' => $this->losses(),
        ];
    }
}
