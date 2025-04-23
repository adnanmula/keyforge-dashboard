<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetUsersQuery
{
    private(set) ?int $page;
    private(set) ?int $pageSize;
    private(set) bool $withGames;
    private(set) bool $withExternal;
    private(set) bool $onlyFriends;
    private(set) ?Uuid $userId;
    private(set) ?string $name;

    public function __construct($page, $pageSize, $withGames, $withExternal, $onlyFriends = false, $userId = null, $name = null)
    {
        Assert::lazy()
            ->that($page, 'page')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($pageSize, 'page_size')->nullOr()->integerish()->greaterThan(0)
            ->that($withGames, 'with_games')->boolean()
            ->that($withExternal, 'with_external')->boolean()
            ->that($onlyFriends, 'only_friends')->boolean()
            ->that($userId, 'user_id')->nullOr()->uuid()
            ->that($name, 'name')->nullOr()->string()
            ->verifyNow();

        if ($onlyFriends) {
            Assert::lazy()->that($userId, 'user_id')->uuid()->verifyNow();
        }

        $this->page = null === $page ? null : (int) $page;
        $this->pageSize = null === $pageSize ? null : (int) $pageSize;
        $this->withGames = $withGames;
        $this->withExternal = $withExternal;
        $this->onlyFriends = $onlyFriends;
        $this->userId = Uuid::fromNullable($userId);
        $this->name = $name;
    }
}
