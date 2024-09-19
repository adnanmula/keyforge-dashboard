<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared;

interface Repository
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}
