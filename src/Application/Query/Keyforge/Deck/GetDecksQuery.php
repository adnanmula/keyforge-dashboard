<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use Assert\Assert;

final class GetDecksQuery
{
    private int $start;
    private int $length;
    private ?QueryOrder $order;

    public function __construct($start, $length, ?QueryOrder $order)
    {
        Assert::lazy()
            ->that($start, 'start')->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->integerish()->greaterThan(0);

        $this->start = (int) $start;
        $this->length = (int) $length;
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

    public function order(): ?QueryOrder
    {
        return $this->order;
    }
}
