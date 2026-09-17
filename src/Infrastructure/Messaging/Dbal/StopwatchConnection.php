<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Messaging\Dbal;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchConnection extends AbstractConnectionMiddleware
{
    public function __construct(
        Connection $connection,
        private readonly Stopwatch $stopwatch,
    ) {
        parent::__construct($connection);
    }

    public function prepare(string $sql): Statement
    {
        return new StopwatchStatement(
            parent::prepare($sql),
            $sql,
            $this->stopwatch,
        );
    }

    public function query(string $sql): Result
    {
        $event = $this->stopwatch->start($sql, 'db');

        try {
            return parent::query($sql);
        } finally {
            $event->stop();
        }
    }

    public function exec(string $sql): int|string
    {
        $event = $this->stopwatch->start($sql, 'db');

        try {
            return parent::exec($sql);
        } finally {
            $event->stop();
        }
    }
}
