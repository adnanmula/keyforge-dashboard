<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class ApproveAccountCommand
{
    public Uuid $user;

    public function __construct($user)
    {
        Assert::lazy()
            ->that($user, 'user_id')->uuid()
            ->verifyNow();

        $this->user = Uuid::from($user);
    }
}
