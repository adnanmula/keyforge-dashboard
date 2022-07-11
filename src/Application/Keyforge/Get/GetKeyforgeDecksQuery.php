<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\Get;

use Assert\Assert;

final class GetKeyforgeDecksQuery
{
    private int $page;
    private int $pageSize;

    public function __construct($page, $pageSize)
    {
        Assert::lazy()
            ->that($page, 'page')->integerish()->greaterOrEqualThan(0)
            ->that($pageSize, 'page_size')->integerish()->greaterThan(0);

        $this->page = (int) $page;
        $this->pageSize = (int) $pageSize;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }
}
