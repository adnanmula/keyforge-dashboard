<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeDeckRepository
{
    public function all(int $start, int $length, ?QueryOrder $order): array;

    public function count(): int;

    public function byId(Uuid $id): ?KeyforgeDeck;

    /** @return array<KeyforgeDeck> */
    public function byIds(Uuid ...$id): array;

    /** @return array<KeyforgeDeck> */
    public function byNames(string ...$decks): array;

    public function save(KeyforgeDeck $deck): void;
}
