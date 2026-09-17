<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Messaging\Dbal;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class StopwatchMiddleware implements Middleware
{
    public function __construct(
        private Stopwatch $stopwatch,
    ) {}

    public function wrap(Driver $driver): Driver
    {
        return new StopwatchDriver($driver, $this->stopwatch);
    }
}
