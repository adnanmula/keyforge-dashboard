<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\UserStats;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;
use Assert\Assert;

final class GetUserStatsQuery
{
    private UuidValueObject $userId;

    public function __construct($userId)
    {
        Assert::lazy()->that($userId, 'user_id')->uuid();

        $this->userId = UuidValueObject::from($userId);
    }

    public function userId(): UuidValueObject
    {
        return $this->userId;
    }
}
