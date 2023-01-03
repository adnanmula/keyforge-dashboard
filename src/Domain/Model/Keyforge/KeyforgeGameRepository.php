<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface KeyforgeGameRepository
{
    /** @return array<KeyforgeGame> */
    public function search(Criteria $criteria): array;

    /** @return array<KeyforgeGame> */
    public function all(?Pagination $pagination): array;

    public function count(Criteria $criteria): int;

    public function save(KeyforgeGame $game): void;
}
