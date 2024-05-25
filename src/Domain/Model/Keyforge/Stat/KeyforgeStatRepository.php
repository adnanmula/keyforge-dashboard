<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Stat;

use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeStatRepository
{
    public function by(KeyforgeStatCategory $category, ?Uuid $reference): ?KeyforgeStat;
    public function save(KeyforgeStat $stat): void;
    public function remove(KeyforgeStatCategory $category, ?Uuid $reference): void;
}
