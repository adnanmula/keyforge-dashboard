<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeck implements \JsonSerializable
{
    public function __construct(
        private readonly Uuid $id,
        private readonly int $dokId,
        private readonly string $name,
        private readonly KeyforgeSet $set,
        private readonly KeyforgeDeckHouses $houses,
        private readonly KeyforgeCards $cards,
        private KeyforgeDeckStats $stats,
        private array $tags = [],
        private array $owners = [],
        private ?KeyforgeDeckUserData $userData = null,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function dokId(): int
    {
        return $this->dokId;
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

    public function cards(): KeyforgeCards
    {
        return $this->cards;
    }

    public function stats(): KeyforgeDeckStats
    {
        return $this->stats;
    }

    public function setStats(KeyforgeDeckStats $stats): void
    {
        $this->stats = $stats;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function setTags(string ...$tags): void
    {
        $this->tags = $tags;
    }

    /** @return array<Uuid> */
    public function owners(): array
    {
        return $this->owners;
    }

    public function userData(): ?KeyforgeDeckUserData
    {
        return $this->userData;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'dok_id' => $this->dokId,
            'name' => $this->name,
            'set' => $this->set->jsonSerialize(),
            'houses' => $this->houses->jsonSerialize(),
            'cards' => $this->cards->jsonSerialize(),
            'stats' => $this->stats->jsonSerialize(),
            'tags' => $this->tags,
            'owners' => \array_map(static fn (Uuid $id): string => $id->value(), $this->owners),
            'userData' => $this->userData?->jsonSerialize(),
        ];
    }
}
