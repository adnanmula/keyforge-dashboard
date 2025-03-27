<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Shared\CriteriaQuery;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterGroup\FilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Assert\Assert;

final readonly class GetTagsQuery extends CriteriaQuery
{
    public function __construct($visibility, $archived, $ids)
    {
        Assert::lazy()
            ->that($visibility, 'visibility')->nullOr()->inArray(TagVisibility::values())
            ->that($archived, 'archived')->nullOr()->boolean()
            ->that($ids, 'ids')->nullOr()->all()->uuid()
            ->verifyNow();

        $filters = [];
        $filters[] = $this->visibilityFilter($visibility);
        $filters[] = $this->archivedFilter($archived);
        $filters[] = $this->idsFilter(...$ids ?? []);

        $criteria = new Criteria(
            null,
            null,
            new Sorting(
                new Order(
                    new FilterField('type'),
                    OrderType::ASC,
                ),
            ),
            ...\array_filter($filters),
        );

        parent::__construct($criteria);
    }

    private function visibilityFilter(?string $visibility): ?FilterGroup
    {
        if (null === $visibility) {
            return null;
        }

        return new AndFilterGroup(
            FilterType::AND,
            new Filter(
                new FilterField('visibility'),
                new StringFilterValue($visibility),
                FilterOperator::EQUAL,
            ),
        );
    }

    private function archivedFilter(?bool $archived): ?FilterGroup
    {
        if (null === $archived) {
            return null;
        }

        return new AndFilterGroup(
            FilterType::AND,
            new Filter(
                new FilterField('archived'),
                new StringFilterValue((string) $archived),
                FilterOperator::EQUAL,
            ),
        );
    }

    private function idsFilter(string ...$ids): ?FilterGroup
    {
        if (\count($ids) > 0) {
            return new AndFilterGroup(
                FilterType::OR,
                ...\array_map(
                    static fn (string $id) => new Filter(new FilterField('id'), new StringFilterValue($id), FilterOperator::EQUAL),
                    $ids,
                ),
            );
        }

        return null;
    }
}
