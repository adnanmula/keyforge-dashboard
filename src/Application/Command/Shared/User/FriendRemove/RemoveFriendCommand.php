<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\FriendRemove;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class RemoveFriendCommand
{
    private(set) Uuid $user;
    private(set) Uuid $friendId;

    public function __construct($user, $friendId)
    {
        Assert::lazy()
            ->that($user, 'user')->uuid()
            ->that($friendId, 'friendId')->uuid()
            ->verifyNow();

        $this->user = Uuid::from($user);
        $this->friendId = Uuid::from($friendId);
    }
}
