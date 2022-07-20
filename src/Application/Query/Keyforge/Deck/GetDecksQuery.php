<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use Assert\Assert;

final class GetDecksQuery
{
    private int $start;
    private int $length;
    private ?string $deck;
    private ?string $set;
    private ?string $house;
    private ?QueryOrder $order;

    public function __construct($start, $length, ?string $deck, ?string $set, ?string $house, ?QueryOrder $order)
    {
        Assert::lazy()
            ->that($start, 'start')->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($set, 'set')->nullOr()->string()->notBlank()
            ->that($house, 'house')->nullOr()->string()->notBlank();

        $this->start = (int) $start;
        $this->length = (int) $length;
        $this->deck= $deck;
        $this->set = $set;
        $this->house = $house;
        $this->order = $order;
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
}
