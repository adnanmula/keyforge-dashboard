<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeDeckRepository
{
    /** @return array<KeyforgeDeck> */
    public function all(int $page, int $pageSize): array;

    public function byId(Uuid $id): ?KeyforgeDeck;

    /** @return array<KeyforgeDeck> */
    public function byIds(Uuid ...$id): array;

    /** @return array<KeyforgeDeck> */
    public function byNames(string ...$decks): array;

    public function save(KeyforgeDeck $deck): void;
}
