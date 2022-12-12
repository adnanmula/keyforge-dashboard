<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterField;

final class ArrayElementFilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
        private readonly int $index,
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

    public function index(): int
    {
        return $this->index;
    }

    public function value(): string
    {
        return $this->name . '->>' . $this->index;
    }
}
