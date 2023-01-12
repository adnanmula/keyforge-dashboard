<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Criteria\Criteria;

final class GetGamesQuery
{
    public function __construct(
        private ?Criteria $criteria,
    ) {}

    public function criteria(): ?Criteria
    {
        return $this->criteria;
    }
}
