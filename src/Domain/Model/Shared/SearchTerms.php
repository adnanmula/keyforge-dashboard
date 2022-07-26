<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

use ArrayIterator;
use Traversable;

final class SearchTerms implements \IteratorAggregate
{
    private SearchTermType $type;
    private array $terms;

    public function __construct(SearchTermType $type, SearchTerm ...$terms)
    {
        $this->type = $type;
        $this->terms = $terms;
    }

    public function type(): SearchTermType
    {
        return $this->type;
    }

    public function terms(): array
    {
        return $this->terms;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->terms);
    }
}
