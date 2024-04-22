<?php declare(strict_types=1);

namespace AdnanMula\Cards\Shared;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;

final class LocalizedString implements \JsonSerializable
{
    private function __construct(private array $values)
    {
    }

    public static function fromLocale(string $value, Locale $locale = Locale::es_ES): self
    {
        return new self([$locale->value => $value]);
    }

    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    public function get(Locale $locale): ?string
    {
        return $this->values[$locale->value] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->values;
    }
}
