<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Messaging\Dbal;

use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchStatement extends AbstractStatementMiddleware
{
    public function __construct(
        Statement $statement,
        private readonly string $sql,
        private readonly Stopwatch $stopwatch,
    ) {
        parent::__construct($statement);
    }

    public function execute(): Result
    {
        $event = $this->stopwatch->start($this->eventName($this->sql), 'db');

        try {
            return parent::execute();
        } finally {
            $event->stop();
        }
    }

    private function eventName(string $sql): string
    {
        if (preg_match('/^\s*SELECT\b.*?\bFROM\s+([a-zA-Z0-9_."]+)/is', $sql, $matches)) {
            return sprintf('DB SELECT %s', $matches[1]);
        }

        if (preg_match('/^\s*INSERT\s+INTO\s+([a-zA-Z0-9_."]+)/i', $sql, $matches)) {
            return sprintf('DB INSERT %s', $matches[1]);
        }

        if (preg_match('/^\s*UPDATE\s+([a-zA-Z0-9_."]+)/i', $sql, $matches)) {
            return sprintf('DB UPDATE %s', $matches[1]);
        }

        if (preg_match('/^\s*DELETE\s+FROM\s+([a-zA-Z0-9_."]+)/i', $sql, $matches)) {
            return sprintf('DB DELETE %s', $matches[1]);
        }

        return 'DB query';
    }
}
