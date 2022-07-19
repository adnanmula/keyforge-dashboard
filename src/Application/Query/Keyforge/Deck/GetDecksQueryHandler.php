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
        $decks = $this->repository->all($query->start(), $query->length(), $query->order());
        $total = $this->repository->count();

        return [
            'decks' => $decks,
            'total' => $total,
            'start' => $query->start(),
            'length' => $query->length(),
        ];
    }
}
