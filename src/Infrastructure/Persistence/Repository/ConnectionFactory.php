<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository;

use AdnanMula\Cards\Infrastructure\Messaging\Dbal\StopwatchMiddleware;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\Stopwatch\Stopwatch;

final class ConnectionFactory
{
    public static function create(string $env, string $databaseUrl, Stopwatch $stopwatch): Connection
    {
        $parser = new DsnParser();
        $params = $parser->parse($databaseUrl);
        $params['driver'] = 'pdo_pgsql';

        $config = new Configuration();
        $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

        if ('prod' !== $env) {
            $config->setMiddlewares([new StopwatchMiddleware($stopwatch)]);
        }

        return DriverManager::getConnection($params, $config);
    }
}
