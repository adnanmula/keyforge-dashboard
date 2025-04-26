<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Tools\DsnParser;

final class ConnectionFactory
{
    public static function create(string $databaseUrl): Connection
    {
        $parser = new DsnParser();
        $params = $parser->parse($databaseUrl);
        $params['driver'] = 'pdo_pgsql';

        $config = new Configuration();
        $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

        return DriverManager::getConnection($params, $config);
    }
}
