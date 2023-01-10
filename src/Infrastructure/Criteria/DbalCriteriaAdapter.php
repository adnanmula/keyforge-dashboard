<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria;

use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterFieldInterface;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class DbalCriteriaAdapter implements CriteriaAdapter
{
    private QueryBuilder $queryBuilder;
    private readonly array $fieldMapping;
    private int $parameterIndex;

    public function __construct(QueryBuilder $queryBuilder, array $fieldMapping = [])
    {
        $this->queryBuilder = $queryBuilder;
        $this->fieldMapping = $fieldMapping;
        $this->parameterIndex = 0;
    }

    public function execute(Criteria $criteria): void
    {
        $this->applyFilters($criteria);
        $this->applySorting($criteria);
        $this->applyPagination($criteria);
    }

    private function applyFilters(Criteria $criteria): void
    {
        foreach ($criteria->filters() as $filter) {
            $expressions = \array_map(
                fn(Filter $expression) => $this->buildExpression($expression),
                $filter->filters(),
            );

            if (\count($expressions) === 0) {
                continue;
            }

            if ($filter->filtersType() === FilterType::OR) {
                $expression = $this->queryBuilder->expr()->or(...$expressions);
            } else {
                $expression = $this->queryBuilder->expr()->and(...$expressions);
            }

            if ($filter->expressionType() === FilterType::OR) {
                $this->queryBuilder->orWhere($expression);
            } else {
                $this->queryBuilder->andWhere($expression);
            }
        }
    }

    private function applySorting(Criteria $criteria): void
    {
        if (null !== $criteria->sorting()) {
            foreach ($criteria->sorting()->order() as $order) {
                $this->queryBuilder->addOrderBy(
                    $this->mapField($order->field()),
                    $order->type()->name,
                );
            }
        }
    }

    private function applyPagination(Criteria $criteria): void
    {
        if (null !== $criteria->offset()) {
            $this->queryBuilder->setFirstResult($criteria->offset());
        }

        if (null !== $criteria->limit()) {
            $this->queryBuilder->setMaxResults($criteria->limit());
        }
    }

    private function buildExpression(Filter $filter): string
    {
        $this->parameterIndex++;

        $parameterName = \str_replace('.', '', $filter->field()->name()) . $this->parameterIndex;

        $this->queryBuilder->setParameter(
            $parameterName,
            $this->mapParameterValue($filter),
            $this->mapType($filter),
        );

        $field = $this->mapField($filter->field());
        $value = ':' . $parameterName;

        switch ($filter->operator()) {
            case FilterOperator::EQUAL:
                return $this->queryBuilder->expr()->eq($field, $value);
            case FilterOperator::NOT_EQUAL:
                return $this->queryBuilder->expr()->neq($field, $value);
            case FilterOperator::GREATER:
                return $this->queryBuilder->expr()->gt($field, $value);
            case FilterOperator::GREATER_OR_EQUAL:
                return $this->queryBuilder->expr()->gte($field, $value);
            case FilterOperator::LESS:
                return $this->queryBuilder->expr()->lt($field, $value);
            case FilterOperator::LESS_OR_EQUAL:
                return $this->queryBuilder->expr()->lte($field, $value);
            case FilterOperator::CONTAINS:
                return $this->queryBuilder->expr()->like($field, $value);
            case FilterOperator::CONTAINS_INSENSITIVE:
                return $field . ' ilike ' . $value;
            case FilterOperator::NOT_CONTAINS:
                return $this->queryBuilder->expr()->notLike($field, $value);
            case FilterOperator::NOT_CONTAINS_INSENSITIVE:
                return $field . ' not ilike ' . $field;
            case FilterOperator::IN:
                return $this->queryBuilder->expr()->in($field, $value);
            case FilterOperator::NOT_IN:
                return $this->queryBuilder->expr()->notIn($field, $value);
            case FilterOperator::IS_NULL:
                return $this->queryBuilder->expr()->isNull($field);
            case FilterOperator::IS_NOT_NULL:
                return $this->queryBuilder->expr()->isNotNull($field);
            case FilterOperator::IN_ARRAY:
                return $field . '::jsonb @> ' . $value . '::jsonb';
        }

        throw new \InvalidArgumentException('Invalid operator');
    }

    private function mapParameterValue(Filter $filter): mixed
    {
        $containOperators = [FilterOperator::CONTAINS, FilterOperator::CONTAINS_INSENSITIVE, FilterOperator::NOT_CONTAINS, FilterOperator::NOT_CONTAINS_INSENSITIVE];

        if (in_array($filter->operator(), $containOperators, true)) {
            return '%' . $filter->value()->value() . '%';
        }

        return $filter->value()->value();
    }

    private function mapType(Filter $filter): ?int
    {
        if (FilterOperator::IN === $filter->operator() || FilterOperator::NOT_IN === $filter->operator()) {
            return Connection::PARAM_STR_ARRAY;
        }

        return null;
    }

    private function mapField(FilterFieldInterface $field): string
    {
        if (\array_key_exists($field->name(), $this->fieldMapping)) {
            $field->setName($this->fieldMapping[$field->name()]);
        }

        return $field->value();
    }
}
