<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterField;

final class FilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function value(): string
    {
        return $this->name;
    }
}
