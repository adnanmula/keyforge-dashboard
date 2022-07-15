<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository
    ) {}

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetDecksQuery $query): array
    {
        return $this->repository->all($query->page(), $query->pageSize());
    }
}
