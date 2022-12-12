<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Gwent\Deck;

use AdnanMula\Cards\Domain\Model\Gwent\GwentDeckRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

final class GetDecksQueryHandler
{
    public function __construct(
        private GwentDeckRepository $repository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        $decks = $this->repository->search(new Criteria(
            null,
            $query->start(),
            $query->length(),
        ));

        return [
            'decks' => $decks,
            'total' => null,
            'totalFiltered' => null,
            'start' => $query->start(),
            'length' => $query->length(),
        ];
    }
}
