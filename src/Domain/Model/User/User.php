<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\User\ValueObject\UserId;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;
use AdnanMula\Cards\Domain\Model\User\ValueObject\UserUsername;

final class User
{
    private const MODEL_NAME = 'user';

    private UserId $id;
    private UserReference $reference;
    private UserUsername $username;

    private function __construct(UserId $id, UserReference $reference, UserUsername $username)
    {
        $this->id = $id;
        $this->reference = $reference;
        $this->username = $username;
    }

    public static function create(UserId $id, UserReference $reference, UserUsername $username): self
    {
        return new self($id, $reference, $username);
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

    public static function modelName(): string
    {
        return self::MODEL_NAME;
    }
}
