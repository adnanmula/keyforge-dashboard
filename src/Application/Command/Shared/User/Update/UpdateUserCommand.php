<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\Update;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class UpdateUserCommand
{
    public Uuid $id;
    public ?Locale $locale;

    public function __construct($id, public ?string $password, $locale)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($password, 'password')->nullOr()->string()->notBlank()
            ->that($locale, 'locale')->nullOr()->inArray(Locale::values())
            ->verifyNow();

        $this->id = Uuid::from($id);
        $this->locale = null === $locale ? null : Locale::from($locale);
    }
}
