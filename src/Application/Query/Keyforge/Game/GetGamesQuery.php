<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Criteria\Criteria;

final readonly class GetGamesQuery
{
    public function __construct(
        private(set) ?Criteria $criteria,
    ) {}
}
