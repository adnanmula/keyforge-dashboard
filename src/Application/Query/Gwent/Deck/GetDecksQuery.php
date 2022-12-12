<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Gwent\Deck;

use Assert\Assert;

final class GetDecksQuery
{
    private ?int $start;
    private ?int $length;

    public function __construct($start, $length)
    {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->verifyNow();

        $this->start = null === $start ? null : (int) $start;
        $this->length = null === $length ? null : (int) $length;
    }

    public function start(): ?int
    {
        return $this->start;
    }

    public function length(): ?int
    {
        return $this->length;
    }
}
