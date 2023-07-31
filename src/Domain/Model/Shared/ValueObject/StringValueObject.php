<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

class StringValueObject implements \JsonSerializable
{
    final protected function __construct(
        private readonly string $value,
    ) {}

    public function value(): string
    {
        return $this->value;
    }

    public function equalTo(self $other): bool
    {
        return static::class === $other::class && $this->value === $other->value;
    }

    public static function from(string $value): static
    {
        return new static($value);
    }

    final public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
