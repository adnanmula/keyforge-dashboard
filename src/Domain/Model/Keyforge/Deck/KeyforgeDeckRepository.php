<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Shared\Repository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeDeckRepository extends Repository
{
    /** @return array<KeyforgeDeck> */
    public function search(Criteria $criteria, bool $isMyDecks = false): array;
    public function searchOne(Criteria $criteria): ?KeyforgeDeck;
    public function count(Criteria $criteria): int;
    public function addOwner(Uuid $deckId, Uuid $userId): void;
    public function removeOwner(Uuid $deckId, Uuid $userId): void;
    /** @return array<array{deck_id: string, user_id: string, notes: string}> */
    public function ownersOf(Uuid $deckId): array;
    /** @return array<array{deck_id: string, user_id: string, notes: string, user_tags: string}> */
    public function ownedBy(Uuid $userId): array;
    public function ownedInfo(Uuid $userId, Uuid $deckId): ?array;
    public function updateUserTags(Uuid $userId, Uuid $deckId, string ...$tags): void;
    public function updateNotes(Uuid $userId, Uuid $deckId, string $notes): void;
    public function save(KeyforgeDeck $deck): void;
    public function bellCurve(?KeyforgeDeckType $deckType): array;
    public function homeCounts(): array;
}
