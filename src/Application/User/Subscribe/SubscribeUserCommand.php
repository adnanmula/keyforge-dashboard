<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\User\Subscribe;

use AdnanMula\Cards\Domain\Model\User\ValueObject\UserId;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserUsername;
use Assert\Assert;

final class SubscribeUserCommand
{
    public const COMMAND = ['/subscribe', '/sub'];

    private UserId $id;
    private UserReference $reference;
    private UserUsername $username;

    public function __construct($id, $reference, $username)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($reference, 'reference')->string()->notBlank()
            ->that($username, 'username')->string()->notBlank()
            ->verifyNow();

        $this->id = UserId::from($id);
        $this->reference = UserReference::from($reference);
        $this->username = UserUsername::from($username);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function reference(): UserReference
    {
        return $this->reference;
    }

    public function username(): UserUsername
    {
        return $this->username;
    }
}
