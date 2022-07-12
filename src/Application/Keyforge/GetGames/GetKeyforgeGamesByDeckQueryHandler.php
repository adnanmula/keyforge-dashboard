<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\GetGames;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;

final class GetKeyforgeGamesByDeckQueryHandler
{
    private KeyforgeRepository $repository;

    public function __construct(KeyforgeRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetKeyforgeGamesByDeckQuery $query): array
    {
        return $this->repository->gamesByDeck($query->deckId());
    }
}
