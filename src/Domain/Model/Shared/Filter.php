<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

final class Filter
{
    public function __construct(
        private string $field,
        private string $value,
    ) {}

    public function field(): string
    {
        return $this->field;
    }

    public function value(): string
    {
        return $this->value;
    }
}
