<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeDeckRepository
{
    /** @return array<KeyforgeDeck> */
    public function search(Criteria $criteria): array;

    public function searchWithOwnerUserData(Criteria $criteria, Uuid $owner): array;

    public function searchWithAggregatedOwnerUserData(Criteria $criteria): array;

    public function count(Criteria $criteria): int;
    public function countWithOwnerUserData(Criteria $criteria, Uuid $owner): int;
    public function countWithAggregatedOwnerUserData(Criteria $criteria): int;

    public function save(KeyforgeDeck $deck): void;
}
