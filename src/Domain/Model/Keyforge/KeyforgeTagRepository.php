<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface KeyforgeTagRepository
{
    /** @return array<KeyforgeTag> */
    public function search(Criteria $criteria): array;

    public function save(KeyforgeTag $tag): void;
}
