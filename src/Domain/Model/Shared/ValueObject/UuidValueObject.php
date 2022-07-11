<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use Ramsey\Uuid\Uuid;

class UuidValueObject extends StringValueObject
{
    public static function from(string $value)
    {
        return new static(Uuid::fromString($value)->toString());
    }

    public static function v4()
    {
        return new static(Uuid::uuid4()->toString());
    }
}
