<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterValue;

final class ArrayElementFilterValue implements FilterValue
{
    public function __construct(
        private readonly string $value,
    ) {}

    public function value(): string
    {
        return '["' . $this->value . '"]';
    }
}
