<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;

abstract class DbalRepository
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    final public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    final public function commit(): void
    {
        $this->connection->commit();
    }

    final public function rollback(): void
    {
        $this->connection->rollBack();
    }
}
