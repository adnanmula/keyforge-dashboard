<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;
use Assert\Assert;

final class GetDecksQuery
{
    private ?int $start;
    private ?int $length;
    private ?string $deck;
    private ?string $set;
    private ?string $house;
    private ?Sorting $sorting;
    private ?Uuid $deckId;
    private ?Uuid $owner;
    private bool $onlyOwned;

    public function __construct(
        $start,
        $length,
        ?string $deck,
        ?string $set,
        ?string $house,
        ?Sorting $sorting,
        ?string $deckId = null,
        ?string $owner = null,
        bool $onlyOwned = false,
    ) {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($set, 'set')->nullOr()->string()->notBlank()
            ->that($house, 'house')->nullOr()->string()->notBlank()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid()
            ->that($onlyOwned, 'onlyOwned')->boolean();

        $this->start = null === $start ? null : (int) $start;
        $this->length = null === $length ? null : (int) $length;
        $this->deck= $deck;
        $this->set = $set;
        $this->house = $house;
        $this->sorting = $sorting;
        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = null !== $owner ? Uuid::from($owner) : null;
        $this->onlyOwned = $onlyOwned;
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

    public function house(): ?string
    {
        return $this->house;
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
}
