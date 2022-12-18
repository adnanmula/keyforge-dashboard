<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use Ramsey\Uuid\Uuid as VendorUuid;

class Uuid extends StringValueObject
{
    public static function from(string $value): static
    {
        return new static(VendorUuid::fromString($value)->toString());
    }

    public static function v4(): static
    {
        return new static(VendorUuid::uuid4()->toString());
    }

    public static function isValid(string $uuid): bool
    {
        return VendorUuid::isValid($uuid);
    }
}
