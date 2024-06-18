<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Criteria\Criteria;

interface KeyforgeDeckUserDataRepository
{
    /** @return array<KeyforgeDeckUserData> */
    public function search(Criteria $criteria): array;

    public function searchOne(Criteria $criteria): ?KeyforgeDeckUserData;

    public function count(Criteria $criteria): int;

    public function save(KeyforgeDeckUserData $data): void;
}
