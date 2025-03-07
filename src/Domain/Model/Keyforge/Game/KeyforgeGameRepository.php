<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeGameRepository
{
    /** @return array<KeyforgeGame> */
    public function search(Criteria $criteria): array;

    public function searchOne(Criteria $criteria): ?KeyforgeGame;

    /** @return array<KeyforgeGame> */
    public function all(?int $offset = null, ?int $limit = null): array;

    public function count(Criteria $criteria): int;

    public function save(KeyforgeGame $game): void;

    public function remove(Uuid $id): void;
}
