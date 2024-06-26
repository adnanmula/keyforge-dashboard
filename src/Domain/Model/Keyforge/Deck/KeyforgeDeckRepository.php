<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeDeckRepository
{
    /** @return array<KeyforgeDeck> */
    public function search(Criteria $criteria): array;
    public function searchOne(Criteria $criteria): ?KeyforgeDeck;
    public function count(Criteria $criteria): int;
    public function addOwner(Uuid $deckId, Uuid $userId): void;
    public function removeOwner(Uuid $deckId, Uuid $userId): void;
    /** @return array<array{deck_id: string, user_id: string, notes: string}> */
    public function ownersOf(Uuid $deckId): array;
    /** @return array<array{deck_id: string, user_id: string, notes: string}> */
    public function ownedBy(Uuid $userId): array;
    public function updateNotes(Uuid $userId, Uuid $deckId, string $notes): void;
    public function save(KeyforgeDeck $deck): void;
}
