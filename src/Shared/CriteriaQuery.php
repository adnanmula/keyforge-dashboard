<?php declare(strict_types=1);

namespace AdnanMula\Cards\Shared;

use AdnanMula\Criteria\Criteria;

abstract readonly class CriteriaQuery
{
    public function __construct(
        private(set) Criteria $criteria,
    ) {}
}
