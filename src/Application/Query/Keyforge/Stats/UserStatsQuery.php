<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class UserStatsQuery
{
    private Uuid $userId;

    public function __construct($userId)
    {
        Assert::lazy()->that($userId, 'user_id')->uuid()->verifyNow();

        $this->userId = Uuid::from($userId);
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }
}
