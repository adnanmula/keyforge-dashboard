<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;

final readonly class GetTagsQueryHandler
{
    public function __construct(
        private KeyforgeTagRepository $repository,
    ) {}

    public function __invoke(GetTagsQuery $query): array
    {
        $expressions = [];

        if (null !== $query->visibility()) {
            $expressions[] = new Filter(new FilterField('visibility'), new StringFilterValue($query->visibility()->name), FilterOperator::EQUAL);
        }

        if (null !== $query->archived()) {
            $expressions[] = new Filter(new FilterField('archived'), new StringFilterValue((string) $query->archived()), FilterOperator::EQUAL);
        }

        $filters = [new AndFilterGroup(FilterType::AND, ...$expressions)];

        if (null !== $query->ids()) {
            $idsExpressions = [];
            foreach ($query->ids() as $id) {
                $idsExpressions[] = new Filter(new FilterField('id'), new StringFilterValue($id->value()), FilterOperator::EQUAL);
            }

            $filters[] = new AndFilterGroup(FilterType::OR, ...$idsExpressions);
        }

        $criteria = new Criteria(
            null,
            null,
            new Sorting(
                new Order(
                    new FilterField('type'),
                    OrderType::ASC,
                ),
            ),
            ...$filters,
        );

        $tags = $this->repository->search($criteria);

        return [
            'tags' => $tags,
        ];
    }
}
