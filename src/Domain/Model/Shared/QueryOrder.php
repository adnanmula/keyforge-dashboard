<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

final class QueryOrder
{
    public function __construct(
        private string $field,
        private string $order,
    ) {}

    public function field(): string
    {
        return $this->field;
    }

    public function order(): string
    {
        return $this->order;
    }
}
