<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;
use Assert\Assert;

final class GetDecksQuery
{
    private ?int $start;
    private ?int $length;
    private ?string $deck;
    private ?string $set;
    private ?string $houseFilterType;
    private ?array $houses;
    private ?Sorting $sorting;
    private ?Uuid $deckId;
    private ?Uuid $owner;
    private bool $onlyOwned;
    private array $tags;

    public function __construct(
        $start,
        $length,
        ?string $deck,
        ?string $set,
        ?string $houseFilterType,
        ?array $houses,
        ?Sorting $sorting,
        ?string $deckId = null,
        ?string $owner = null,
        bool $onlyOwned = false,
        array $tags = [],
    ) {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($set, 'set')->nullOr()->string()->notBlank()
            ->that($houseFilterType, 'houseFilterType')->nullOr()->string()->inArray(['all', 'any'])
            ->that($houses, 'house')->nullOr()->all()->inArray(KeyforgeHouse::allowedValues())
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid()
            ->that($onlyOwned, 'onlyOwned')->boolean()
            ->that($tags, 'tags')->all()->uuid()
            ->verifyNow();

        $this->start = null === $start ? null : (int) $start;
        $this->length = null === $length ? null : (int) $length;
        $this->deck= $deck;
        $this->set = $set;
        $this->houseFilterType = $houseFilterType;
        $this->houses = $houses;
        $this->sorting = $sorting;
        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = null !== $owner ? Uuid::from($owner) : null;
        $this->onlyOwned = $onlyOwned;
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

    public function set(): ?string
    {
        return $this->set;
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

    public function tags(): array
    {
        return $this->tags;
    }
}
