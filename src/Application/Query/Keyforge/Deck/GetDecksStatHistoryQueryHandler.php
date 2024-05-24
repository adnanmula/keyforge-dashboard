<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckStatHistoryRepository;

final class GetDecksStatHistoryQueryHandler
{
    public function __construct(
        private KeyforgeDeckStatHistoryRepository $repository,
    ) {}

    public function __invoke(GetDecksStatHistoryQuery $query): array
    {
        return $this->repository->byDeckIds(...$query->ids);
    }
}
