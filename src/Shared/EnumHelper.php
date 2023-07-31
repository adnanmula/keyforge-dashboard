<?php declare(strict_types=1);

namespace AdnanMula\Cards\Shared;

trait EnumHelper
{
    public static function values(): array
    {
        return \array_column(self::cases(), 'name');
    }

    public function __call(string $name, array $arguments): bool
    {
        if (\mb_strlen($name) >= 3 && 'is' === \mb_substr($name, 0, 2) && \ctype_upper(\mb_substr($name, 2, 1))) {
            $testValue = \mb_strtoupper(\mb_substr($name, 2, \mb_strlen($name)));
            $currentValue = \mb_strtoupper($this->name);

            return $testValue === $currentValue;
        }

        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->name;
    }
}
