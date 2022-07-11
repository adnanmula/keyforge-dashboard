<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\Get;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;

final class GetKeyforgeDecksQueryHandler
{
    private KeyforgeRepository $repository;

    public function __construct(KeyforgeRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetKeyforgeDecksQuery $query): array
    {
        return $this->repository->all($query->page(), $query->pageSize());
    }
}
