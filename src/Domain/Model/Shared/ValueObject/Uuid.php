<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use Ramsey\Uuid\Uuid as VendorUuid;

class Uuid extends StringValueObject
{
    public const string NULL_UUID = '00000000-0000-0000-0000-000000000000';

    public static function from(string $value): static
    {
        return new static(VendorUuid::fromString($value)->toString());
    }

    public static function fromNullable(?string $value): ?static
    {
        if (null === $value) {
            return null;
        }

        return new static(VendorUuid::fromString($value)->toString());
    }

    /** @return array<self> */
    public static function fromArray(string ...$strings): array
    {
        return array_map(static fn (string $v): self => self::from($v), $strings);
    }

    public static function v4(): static
    {
        return new static(VendorUuid::uuid4()->toString());
    }

    public static function null(): static
    {
        return new static(self::NULL_UUID);
    }

    public static function isValid(string $uuid): bool
    {
        return VendorUuid::isValid($uuid);
    }

    public function isNull(): bool
    {
        return $this === self::null();
    }
}
