<?php declare(strict_types=1);

namespace AdnanMula\Cards\Tests\Mock\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Model\User\User;

final class UserMotherObject
{
    public const MOCK_ID = 'f0f6f4b6-c1ac-4a42-804b-65fb435f1e21';
    public const MOCK_NAME = 'Username';

    private Uuid $id;
    private string $name;

    public function __construct()
    {
        $this->id = Uuid::from(self::MOCK_ID);
        $this->name = self::MOCK_NAME;
    }

    public function build(): User
    {
        return User::create($this->id, $this->name);
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public static function buildDefault(): User
    {
        return User::create(
            Uuid::from(self::MOCK_ID),
            self::MOCK_NAME,
        );
    }
}
