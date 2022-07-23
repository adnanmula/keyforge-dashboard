<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

final class Pagination
{
    public function __construct(
        private int $start,
        private int $length,
    ) {}

    public function start(): int
    {
        return $this->start;
    }

    public function length(): int
    {
        return $this->length;
    }
}
