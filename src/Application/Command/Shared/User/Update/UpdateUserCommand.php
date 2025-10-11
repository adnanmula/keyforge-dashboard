<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Update;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class UpdateUserCommand
{
    public Uuid $id;
    public ?string $password;
    public ?Locale $locale;
    public ?string $dokName;
    public ?string $tcoName;

    public function __construct(mixed $id, mixed $password, mixed $locale, mixed $dokName, mixed $tcoName)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($password, 'password')->nullOr()->string()->notBlank()
            ->that($locale, 'locale')->nullOr()->inArray(Locale::values())
            ->that($dokName, 'dokName')->nullOr()->string()->notBlank()
            ->that($tcoName, 'tcoName')->nullOr()->string()->notBlank()
            ->verifyNow();

        $this->id = Uuid::from($id);
        $this->password = $password;
        $this->locale = null === $locale ? null : Locale::from($locale);
        $this->dokName = $dokName;
        $this->tcoName = $tcoName;
    }
}
