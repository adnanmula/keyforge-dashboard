<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeck
{
    public function __construct(
        private Uuid $id,
        private string $name,
        private KeyforgeSet $set,
        private KeyforgeDeckHouses $houses,
        private int $sas,
        private int $wins,
        private int $losses,
        private array $extraData
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function set(): KeyforgeSet
    {
        return $this->set;
    }

    public function houses(): KeyforgeDeckHouses
    {
        return $this->houses;
    }

    public function sas(): int
    {
        return $this->sas;
    }

    public function wins(): int
    {
        return $this->wins;
    }

    public function losses(): int
    {
        return $this->losses;
    }

    public function extraData(): array
    {
        return $this->extraData;
    }

    public function updateWins(int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function updateLosses(int $losses): self
    {
        $this->losses = $losses;

        return $this;
    }
}
