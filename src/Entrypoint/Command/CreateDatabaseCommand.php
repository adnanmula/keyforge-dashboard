<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateDatabaseCommand extends Command
{
    public const NAME = 'environment:database';

    public function __construct(
        private Connection $defaultConnection,
        private Connection $connection,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Initialize database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->defaultConnection->getSchemaManager()->dropAndCreateDatabase(
            $this->connection->getDatabase(),
        );

        return Command::SUCCESS;
    }
}
