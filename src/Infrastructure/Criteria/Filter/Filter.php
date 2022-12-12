<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\Filter;

use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterFieldInterface;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterValue;

final class Filter
{
    public function __construct(
        private readonly FilterFieldInterface $field,
        private readonly FilterValue $value,
        private readonly FilterOperator $operator,
    ) {}

    public function field(): FilterFieldInterface
    {
        return $this->field;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }
}
