<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\Filter;

final class Filters
{
    private FilterType $expressionType;
    private FilterType $filtersType;
    private readonly array $filters;

    public function __construct(FilterType $expressionType, FilterType $filtersType, Filter ...$filters)
    {
        $this->expressionType = $expressionType;
        $this->filtersType = $filtersType;
        $this->filters = $filters;
    }

    public function expressionType(): FilterType
    {
        return $this->expressionType;
    }

    public function filtersType(): FilterType
    {
        return $this->filtersType;
    }

    /** @return array<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
