<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

final class SearchTerm
{
    private SearchTermType $type;
    private array $filters;

    public function __construct(SearchTermType $type, Filter ...$filters)
    {
        $this->type = $type;
        $this->filters = $filters;
    }

    public function type(): SearchTermType
    {
        return $this->type;
    }

    /** @return array<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
