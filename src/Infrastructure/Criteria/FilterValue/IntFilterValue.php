<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterValue;

final class IntFilterValue implements FilterValue
{
    public function __construct(
        private readonly int $value,
    ) {}

    public function value(): int
    {
        return $this->value;
    }
}
