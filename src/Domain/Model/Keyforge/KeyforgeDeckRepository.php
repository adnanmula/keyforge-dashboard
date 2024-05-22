<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckStatHistory;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeDeckRepository
{
    /** @return array<KeyforgeDeck> */
    public function search(Criteria $criteria): array;

    public function count(Criteria $criteria): int;

    public function byId(Uuid $id): ?KeyforgeDeck;

    /** @return array<KeyforgeDeck> */
    public function byIds(Uuid ...$id): array;

    /** @return array<KeyforgeDeck> */
    public function byNames(string ...$decks): array;

    public function save(KeyforgeDeck $deck, bool $updateUserData = false): void;

    public function saveDeckData(KeyforgeDeckData $data): void;

    public function saveDeckUserData(KeyforgeDeckUserData $data): void;

    public function saveDeckWins(Uuid $id, int $wins, int $losses): void;

    public function saveDeckDataHistory(KeyforgeDeckStatHistory $data): void;
}
