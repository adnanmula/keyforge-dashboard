<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\User\Friend;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetUserFriendsQuery
{
    private(set) Uuid $userId;

    public function __construct($userId)
    {
        Assert::lazy()
            ->that($userId, 'userId')->uuid()
            ->verifyNow();

        $this->userId = Uuid::from($userId);
    }
}
