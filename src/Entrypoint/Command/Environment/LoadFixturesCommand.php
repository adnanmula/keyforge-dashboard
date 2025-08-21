<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use AdnanMula\Cards\Infrastructure\Fixtures\FixturesRegistry;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: self::NAME, description: 'Load fixtures')]
final class LoadFixturesCommand extends Command
{
    public const string NAME = 'environment:fixtures';

    public function __construct(
        private FixturesRegistry $registry,
        private Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Truncating tables');
        foreach ($this->connection->createSchemaManager()->listTables() as $table) {
            $this->connection->executeQuery('TRUNCATE "' . $table->getName() . '" CASCADE');
        }
        $io->success('Tables truncated');

        $this->registry->execute();

        $app = $this->getApplication();

        if (null === $app) {
            $io->error('Kernel not initialized');

            return Command::FAILURE;
        }

        $app->setAutoExit(false);

        $io->info('Setting tags to decks');
        $app->run(new ArrayInput(['command' => 'deck:tag:set', '--quiet' => true]));
        $io->success('Tags set');

        return Command::SUCCESS;
    }
}
