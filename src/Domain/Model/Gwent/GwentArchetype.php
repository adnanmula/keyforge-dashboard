<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentFaction;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class GwentArchetype implements \JsonSerializable
{
    public function __construct(
        private Uuid $id,
        private GwentFaction $faction,
        private string $name,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function faction(): GwentFaction
    {
        return $this->faction;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'faction' => $this->faction(),
            'name' => $this->name(),
        ];
    }
}
