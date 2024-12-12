<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;

final readonly class GetCompetitionsQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(GetCompetitionsQuery $query): array
    {
        $criteria = new Criteria(
            $query->start,
            $query->length,
            new Sorting(
                new Order(
                    new FilterField('created_at'),
                    OrderType::DESC,
                ),
            ),
        );

        $competitions = $this->repository->search($criteria);
        $total = $this->repository->count(new Criteria(null, null, null));

        return [
            'competitions' => $competitions,
            'total' => $total,
            'totalFiltered' => $total,
            'start' => $query->start,
            'length' => $query->length,
        ];
    }
}
