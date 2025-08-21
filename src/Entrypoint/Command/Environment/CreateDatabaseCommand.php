<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: self::NAME, description: 'Initialize database')]
final class CreateDatabaseCommand extends Command
{
    public const string NAME = 'environment:database';

    public function __construct(
        private readonly string $dbUrl,
        private readonly Connection $defaultConnection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dbName = \parse_url($this->dbUrl)['path'] ?? null;

        if (null === $dbName) {
            $io->error('Invalid database url');

            return Command::FAILURE;
        }

        $dbName = \substr($dbName, 1);

        $io->info('Database name: ' . $dbName);

        try {
            $io->info('Dropping database');
            $this->defaultConnection->createSchemaManager()->dropDatabase($dbName);
        } catch (DatabaseDoesNotExist) {
            $io->info('Database did not exist');
        }

        $io->info('Creating database');
        $this->defaultConnection->createSchemaManager()->createDatabase($dbName);

        $io->success('Database created');

        return Command::SUCCESS;
    }
}
