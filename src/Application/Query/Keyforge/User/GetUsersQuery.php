<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class GetUsersQuery
{
    public ?int $page;
    public ?int $pageSize;
    public bool $withGames;
    public bool $withExternal;
    public bool $onlyFriends;
    public ?Uuid $userId;

    public function __construct($page, $pageSize, $withGames, $withExternal, $onlyFriends = false, $userId = null)
    {
        Assert::lazy()
            ->that($page, 'page')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($pageSize, 'page_size')->nullOr()->integerish()->greaterThan(0)
            ->that($withGames, 'with_games')->boolean()
            ->that($withExternal, 'with_external')->boolean()
            ->that($onlyFriends, 'only_friends')->boolean()
            ->that($userId, 'user_id')->nullOr()->uuid()
            ->verifyNow();

        if ($onlyFriends) {
            Assert::lazy()->that($userId, 'user_id')->uuid()->verifyNow();
        }

        $this->page = null === $page ? null : (int) $page;
        $this->pageSize = null === $pageSize ? null : (int) $pageSize;
        $this->withGames = $withGames;
        $this->withExternal = $withExternal;
        $this->onlyFriends = $onlyFriends;
        $this->userId = null === $userId ? null : Uuid::from($userId);
    }
}
