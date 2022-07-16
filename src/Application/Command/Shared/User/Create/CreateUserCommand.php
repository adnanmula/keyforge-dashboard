<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Create;

use Assert\Assert;

final class CreateUserCommand
{
    private string $name;
    private string $password;
    private array $roles;

    public function __construct($name, $password, $roles)
    {
        Assert::lazy()
            ->that($name, 'name')->string()->notBlank()
            ->that($password, 'password')->string()->notBlank()
            ->that($roles, 'roles')->all()->string();

        $this->name = $name;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
