<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command;

use AdnanMula\Cards\Infrastructure\Fixtures\FixturesRegistry;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadFixturesCommand extends Command
{
    public const NAME = 'environment:fixtures';

    private FixturesRegistry $registry;
    private Connection $connection;

    public function __construct(FixturesRegistry $registry, Connection $connection)
    {
        $this->registry = $registry;
        $this->connection = $connection;

        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->connection->getSchemaManager()->listTables() as $table) {
            $this->connection->executeQuery('TRUNCATE "' . $table->getName() . '" CASCADE');
        }

        $this->registry->execute();

        return Command::SUCCESS;
    }
}
