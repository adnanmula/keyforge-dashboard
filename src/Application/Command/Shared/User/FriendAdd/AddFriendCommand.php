<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\FriendAdd;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class AddFriendCommand
{
    private(set) Uuid $user;
    private(set) string $friendName;

    public function __construct($user, $friendName)
    {
        Assert::lazy()
            ->that($user, 'user')->uuid()
            ->that($friendName, 'friendName')->string()->notBlank()
            ->verifyNow();

        $this->user = Uuid::from($user);
        $this->friendName = $friendName;
    }
}
