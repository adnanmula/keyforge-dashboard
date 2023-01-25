<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeck implements \JsonSerializable
{
    public readonly KeyforgeDeckData $data;

    public function __construct(
        private Uuid $id,
        private string $name,
        private KeyforgeSet $set,
        private KeyforgeDeckHouses $houses,
        private int $sas,
        private int $wins,
        private int $losses,
        private array $extraData,
        private ?Uuid $owner,
        private string $notes,
        private array $tags = [],
    ) {
        $this->data = KeyforgeDeckData::fromDokData($extraData);
    }

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

    public function owner(): ?Uuid
    {
        return $this->owner;
    }

    public function notes(): string
    {
        return $this->notes;
    }

    public function tags(): array
    {
        return $this->tags;
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

    public function updateOwner(?Uuid $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function updateNotes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function updateTags(string ...$tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->value(),
            'name' => $this->name(),
            'set' => $this->set()->value,
            'houses' => $this->houses()->jsonSerialize(),
            'sas' => $this->sas(),
            'wins' => $this->wins(),
            'losses' => $this->losses(),
            'data' => $this->data->jsonSerialize(),
            'extraData' => $this->extraData(),
            'owner' => $this->owner()?->value(),
            'tags' => $this->tags(),
            'notes' => $this->notes(),
        ];
    }
}
