<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeTagRepository
{
    /** @return array<KeyforgeDeckTag> */
    public function search(Criteria $criteria): array;

    public function searchOne(Criteria $criteria): ?KeyforgeDeckTag;

    public function save(KeyforgeDeckTag $tag): void;

    public function remove(Uuid $id): void;
}
