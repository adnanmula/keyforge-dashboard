<?php declare(strict_types=1);

namespace AdnanMula\Cards\Tests\Mock\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\User\User;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserId;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserUsername;

final class UserMotherObject
{
    public const MOCK_ID = 'f0f6f4b6-c1ac-4a42-804b-65fb435f1e21';
    public const MOCK_REF = '100000';
    public const MOCK_NAME = 'Username';

    private UserId $id;
    private UserReference $reference;
    private UserUsername $username;

    public function __construct()
    {
        $this->id = UserId::from(self::MOCK_ID);
        $this->reference = UserReference::from(self::MOCK_REF);
        $this->username = UserUsername::from(self::MOCK_NAME);
    }

    public function build(): User
    {
        return User::create($this->id, $this->reference, $this->username);
    }

    public function setId(UserId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setReference(UserReference $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function setUsername(UserUsername $username): self
    {
        $this->username = $username;

        return $this;
    }

    public static function buildDefault(): User
    {
        return User::create(
            UserId::from(self::MOCK_ID),
            UserReference::from(self::MOCK_REF),
            UserUsername::from(self::MOCK_NAME),
        );
    }
}
