<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

interface KeyforgeRepository
{
    /** @return array<KeyforgeDeck> */
    public function all(int $page, int $pageSize): array;
    public function byId(UuidValueObject $id): ?KeyforgeDeck;
    public function save(KeyforgeDeck $deck): void;
}
