<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface GwentGameRepository
{
    public function search(Criteria $criteria): array;

    public function count(Criteria $criteria): int;

    public function save(GwentGame $game): void;
}
