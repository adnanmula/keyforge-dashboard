<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;

interface KeyforgeGameRepository
{
    public function search(SearchTerms $terms, ?Pagination $pagination, ?QueryOrder $order): array;

    /** @return array<KeyforgeGame> */
    public function all(?Pagination $pagination): array;

    public function count(?SearchTerms $search = null): int;

    public function save(KeyforgeGame $game): void;
}
