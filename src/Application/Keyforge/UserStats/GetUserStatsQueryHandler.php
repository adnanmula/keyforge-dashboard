<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\UserStats;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;

final class GetUserStatsQueryHandler
{
    private KeyforgeRepository $repository;

    public function __construct(KeyforgeRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetUserStatsQuery $query): array
    {
        return $this->repository->gamesByUser($query->userId());
    }
}
