<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeGameRepository
{
    /** @return array<KeyforgeGame> */
    public function byUser(Uuid ...$id): array;

    /** @return array<KeyforgeGame> */
    public function byDeck(Uuid $id): array;

    /** @return array<KeyforgeGame> */
    public function all(int $page, int $pageSize): array;

    public function save(KeyforgeGame $game): void;
}
