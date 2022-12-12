<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria;

interface CriteriaAdapter
{
    public function execute(Criteria $criteria): void;
}
