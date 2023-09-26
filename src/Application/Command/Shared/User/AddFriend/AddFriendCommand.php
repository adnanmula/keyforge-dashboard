<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\AddFriend;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class AddFriendCommand
{
    public Uuid $user;
    public string $friendName;

    public function __construct($user, $friendName)
    {
        Assert::lazy()
            ->that($user, 'name')->uuid()
            ->that($friendName, 'password')->string()->notBlank()
            ->verifyNow();

        $this->user = Uuid::from($user);
        $this->friendName = $friendName;
    }
}
