<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Stat\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GenerateUserStatsCommand
{
    public Uuid $userId;

    public function __construct($userId)
    {
        Assert::lazy()->that($userId, 'user_id')->uuid()->verifyNow();

        $this->userId = Uuid::from($userId);
    }
}