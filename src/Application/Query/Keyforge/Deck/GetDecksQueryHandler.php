<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        $decks = $this->repository->all(
            $query->start(),
            $query->length(),
            $query->deck(),
            $query->set(),
            $query->house(),
            $query->order(),
        );

        $total = $this->repository->count();
        $totalFiltered = $this->repository->count($query->deck(), $query->set(), $query->house());

        return [
            'decks' => $decks,
            'total' => $total,
            'totalFiltered' => $totalFiltered,
            'start' => $query->start(),
            'length' => $query->length(),
        ];
    }
}
