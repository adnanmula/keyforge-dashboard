<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterFieldInterface;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Order;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\OrderType;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;

final readonly class GetCompetitionsQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(GetCompetitionsQuery $query): array
    {
        $criteria = new Criteria(
            new Sorting(
                new Order(
                    new FilterField('created_at'),
                    OrderType::DESC
                ),
            ),
            $query->start(),
            $query->length(),
        );

        $competitions = $this->repository->search($criteria);
        $total = $this->repository->count(new Criteria(null, null, null));

        return [
            'competitions' => $competitions,
            'total' => $total,
            'totalFiltered' => $total,
            'start' => $query->start(),
            'length' => $query->length(),
        ];
    }
}
