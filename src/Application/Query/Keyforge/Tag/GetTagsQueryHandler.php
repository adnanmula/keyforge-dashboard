<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Order;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\OrderType;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;

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

        $filters = [new Filters(FilterType::AND, FilterType::AND, ...$expressions)];

        if (null !== $query->ids()) {
            $idsExpressions = [];
            foreach ($query->ids() as $id) {
                $idsExpressions[] = new Filter(new FilterField('id'), new StringFilterValue($id->value()), FilterOperator::EQUAL);
            }

            $filters[] = new Filters(FilterType::AND, FilterType::OR, ...$idsExpressions);
        }

        $criteria = new Criteria(
            new Sorting(
                new Order(
                    new FilterField('name'),
                    OrderType::ASC,
                ),
            ),
            null,
            null,
            ...$filters,
        );

        $tags = $this->repository->search($criteria);

        return [
            'tags' => $tags,
        ];
    }
}
