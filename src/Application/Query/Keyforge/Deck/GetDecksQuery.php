<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class GetDecksQuery
{
    private int $start;
    private int $length;
    private ?string $deck;
    private ?string $set;
    private ?string $house;
    private ?QueryOrder $order;
    private ?Uuid $deckId;
    private ?Uuid $owner;

    public function __construct(
        $start,
        $length,
        ?string $deck,
        ?string $set,
        ?string $house,
        ?QueryOrder $order,
        ?string $deckId = null,
        ?string $owner = null,
    ) {
        Assert::lazy()
            ->that($start, 'start')->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($set, 'set')->nullOr()->string()->notBlank()
            ->that($house, 'house')->nullOr()->string()->notBlank()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid();

        $this->start = (int) $start;
        $this->length = (int) $length;
        $this->deck= $deck;
        $this->set = $set;
        $this->house = $house;
        $this->order = $order;
        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = null !== $owner ? Uuid::from($owner) : null;
    }

    public function start(): int
    {
        return $this->start;
    }

    public function length(): int
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

    public function order(): ?QueryOrder
    {
        return $this->order;
    }

    public function deckId(): ?Uuid
    {
        return $this->deckId;
    }

    public function owner(): ?Uuid
    {
        return $this->owner;
    }
}
