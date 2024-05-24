<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Card;

use AdnanMula\Criteria\Criteria;

interface KeyforgeCardRepository
{
    /** @return array<KeyforgeCard> */
    public function search(Criteria $criteria): array;
    public function count(Criteria $criteria): int;
    public function save(KeyforgeCard $card): void;
}
