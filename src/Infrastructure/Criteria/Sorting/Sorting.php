<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\Sorting;

final class Sorting
{
    private readonly array $order;

    public function __construct(Order ...$order)
    {
        $this->order = $order;
    }

    /** @return array<Order> */
    public function order(): array
    {
        return $this->order;
    }

    public function has(string $field): bool
    {
        foreach ($this->order as $order) {
            if ($order->field()->value() === $field) {
                return true;
            }
        }

        return false;
    }

    public function get(string $field): ?Order
    {
        foreach ($this->order as $order) {
            if ($order->field()->value() === $field) {
                return $order;
            }
        }

        return null;
    }
}
