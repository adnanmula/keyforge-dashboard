<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterField;

interface FilterFieldInterface
{
    public function name(): string;

    public function value(): string;

    public function setName(string $field): self;
}
