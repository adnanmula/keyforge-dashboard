<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckStatHistory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeDeckStatHistoryRepository
{
    /** @return array<string, array<KeyforgeDeckStatHistory>> */
    public function byDeckIds(Uuid ...$ids): array;

    public function save(KeyforgeDeckStatHistory $data): void;
}
