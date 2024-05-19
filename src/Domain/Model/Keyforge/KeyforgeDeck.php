<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeck implements \JsonSerializable
{
    public function __construct(
        private readonly Uuid $id,
        private KeyforgeDeckData $data,
        private ?Uuid $owner,
        private int $wins,
        private int $losses,
        private string $notes,
        private array $tags = [],
        private ?int $prevSas = null,
        private ?int $newSas = null,
    ) {
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function data(): KeyforgeDeckData
    {
        return $this->data;
    }

    public function prevSas(): ?int
    {
        return $this->prevSas;
    }

    public function newSas(): ?int
    {
        return $this->newSas;
    }

    public function wins(): int
    {
        return $this->wins;
    }

    public function losses(): int
    {
        return $this->losses;
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

    public function updatePrevSas(?int $prevSas): self
    {
        $this->prevSas = $prevSas;

        return $this;
    }

    public function updateNewSas(?int $newSas): self
    {
        $this->newSas = $newSas;

        return $this;
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
            'data' => $this->data->jsonSerialize(),
            'prevSas' => $this->prevSas(),
            'newSas' => $this->newSas(),
            'wins' => $this->wins(),
            'losses' => $this->losses(),
            'owner' => $this->owner()?->value(),
            'tags' => $this->tags(),
            'notes' => $this->notes(),
        ];
    }
}
