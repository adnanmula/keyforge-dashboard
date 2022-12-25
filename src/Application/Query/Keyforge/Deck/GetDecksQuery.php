<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;
use Assert\Assert;

final class GetDecksQuery
{
    private ?int $start;
    private ?int $length;
    private ?string $deck;
    private ?array $sets;
    private ?string $houseFilterType;
    private ?array $houses;
    private ?Sorting $sorting;
    private ?Uuid $deckId;
    private ?Uuid $owner;
    private bool $onlyOwned;
    private ?string $tagFilterType;
    private array $tags;

    public function __construct(
        $start,
        $length,
        ?string $deck,
        ?array $sets,
        ?string $houseFilterType,
        ?array $houses,
        ?Sorting $sorting,
        ?string $deckId = null,
        ?string $owner = null,
        bool $onlyOwned = false,
        ?string $tagFilterType = null,
        array $tags = [],
    ) {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($sets, 'set')->nullOr()->all()->inArray(KeyforgeSet::allowedValues())
            ->that($houseFilterType, 'houseFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($houses, 'house')->nullOr()->all()->inArray(KeyforgeHouse::allowedValues())
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid()
            ->that($onlyOwned, 'onlyOwned')->boolean()
            ->that($tagFilterType, 'tagFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($tags, 'tags')->all()->uuid()
            ->verifyNow();

        $this->start = null === $start ? null : (int) $start;
        $this->length = null === $length ? null : (int) $length;
        $this->deck= $deck;
        $this->sets = $sets;
        $this->houseFilterType = $houseFilterType;
        $this->houses = $houses;
        $this->sorting = $sorting;
        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = null !== $owner ? Uuid::from($owner) : null;
        $this->onlyOwned = $onlyOwned;
        $this->tagFilterType = $tagFilterType;
        $this->tags = \array_map(static fn (string $id): Uuid => Uuid::from($id), $tags);
    }

    public function start(): ?int
    {
        return $this->start;
    }

    public function length(): ?int
    {
        return $this->length;
    }

    public function deck(): ?string
    {
        return $this->deck;
    }

    public function sets(): ?array
    {
        return $this->sets;
    }

    public function houseFilterType(): ?string
    {
        return $this->houseFilterType;
    }

    public function houses(): ?array
    {
        return $this->houses;
    }

    public function sorting(): ?Sorting
    {
        return $this->sorting;
    }

    public function deckId(): ?Uuid
    {
        return $this->deckId;
    }

    public function owner(): ?Uuid
    {
        return $this->owner;
    }

    public function onlyOwned(): bool
    {
        return $this->onlyOwned;
    }

    public function tagFilterType(): ?string
    {
        return $this->tagFilterType;
    }

    public function tags(): array
    {
        return $this->tags;
    }
}
