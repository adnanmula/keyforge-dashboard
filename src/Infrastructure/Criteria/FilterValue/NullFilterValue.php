<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterValue;

final class NullFilterValue implements FilterValue
{
    public function __construct() {}

    public function value(): mixed
    {
        return null;
    }
}
