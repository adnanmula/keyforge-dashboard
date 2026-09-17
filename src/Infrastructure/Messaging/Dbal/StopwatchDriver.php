<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Messaging\Dbal;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchDriver extends AbstractDriverMiddleware
{
    public function __construct(
        Driver $driver,
        private readonly Stopwatch $stopwatch,
    ) {
        parent::__construct($driver);
    }

    public function connect(array $params): Connection
    {
        return new StopwatchConnection(
            parent::connect($params),
            $this->stopwatch,
        );
    }
}
