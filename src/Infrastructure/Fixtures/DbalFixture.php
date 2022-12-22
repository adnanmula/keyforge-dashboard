<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures;

use Doctrine\DBAL\Connection;

abstract class DbalFixture
{
    public function __construct(
        protected Connection $connection,
    ) {}
}
