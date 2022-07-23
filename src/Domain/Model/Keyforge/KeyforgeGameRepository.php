<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeGameRepository
{
    /** @return array<KeyforgeGame> */
    public function byUser(Uuid ...$id): array;

    /** @return array<KeyforgeGame> */
    public function byDeck(Uuid $id, ?Pagination $pagination, ?QueryOrder $order): array;

    /** @return array<KeyforgeGame> */
    public function byUsersAndDecks(array $users, array $decks): array;

    /** @return array<KeyforgeGame> */
    public function all(?Pagination $pagination): array;

    public function count(?Uuid $deckId = null): int;

    public function save(KeyforgeGame $game): void;
}
