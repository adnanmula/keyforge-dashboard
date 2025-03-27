<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class ApproveAccountCommand
{
    private(set) Uuid $user;
    private(set) bool $approve;

    public function __construct($user, $approve)
    {
        Assert::lazy()
            ->that($user, 'user_id')->uuid()
            ->that($approve, 'approve')->boolean()
            ->verifyNow();

        $this->user = Uuid::from($user);
        $this->approve = $approve;
    }
}
