<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use Assert\Assert;

final class GetUsersQuery
{
    private ?int $page;
    private ?int $pageSize;
    private bool $withGames;
    private bool $withExternal;

    public function __construct($page, $pageSize, $withGames, $withExternal)
    {
        Assert::lazy()
            ->that($page, 'page')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($pageSize, 'page_size')->nullOr()->integerish()->greaterThan(0)
            ->that($withGames, 'with_games')->boolean()
            ->that($withExternal, 'with_external')->boolean();

        $this->page = null === $page ? null : (int) $page;
        $this->pageSize = null === $pageSize ? null : (int) $pageSize;
        $this->withGames = $withGames;
        $this->withExternal = $withExternal;
    }

    public function page(): ?int
    {
        return $this->page;
    }

    public function pageSize(): ?int
    {
        return $this->pageSize;
    }

    public function withGames(): bool
    {
        return $this->withGames;
    }

    public function withExternal(): bool
    {
        return $this->withExternal;
    }
}
