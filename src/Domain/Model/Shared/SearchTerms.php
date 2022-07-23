<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

use ArrayIterator;
use Traversable;

final class SearchTerms implements \IteratorAggregate
{
    private array $terms;

    public function __construct(SearchTerm ...$terms)
    {
        $this->terms = $terms;
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
