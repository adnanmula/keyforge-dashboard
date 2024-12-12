<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use Assert\Assert;

final class CreateUserCommand
{
    private(set) string $name;
    private(set) string $password;
    private(set) Locale $locale;
    private(set) array $roles;

    public function __construct($name, $password, $locale, $roles)
    {
        Assert::lazy()
            ->that($name, 'name')->string()->notBlank()
            ->that($password, 'password')->string()->notBlank()
            ->that($locale, 'locale')->inArray(Locale::values())
            ->that($roles, 'roles')->all()->string()
            ->verifyNow();

        $this->name = $name;
        $this->password = $password;
        $this->locale = Locale::from($locale);
        $this->roles = $roles;
    }
}
