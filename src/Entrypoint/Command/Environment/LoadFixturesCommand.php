<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use AdnanMula\Cards\Infrastructure\Fixtures\FixturesRegistry;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadFixturesCommand extends Command
{
    public const NAME = 'environment:fixtures';

    public function __construct(
        private FixturesRegistry $registry,
        private Connection $connection,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->connection->getSchemaManager()->listTables() as $table) {
            $this->connection->executeQuery('TRUNCATE "' . $table->getName() . '" CASCADE');
        }

        $this->registry->execute();

        $this->applyTags();

        return Command::SUCCESS;
    }

    private function applyTags(): void
    {
        $app = $this->getApplication();

        if (null === $app) {
            throw new \RuntimeException('Kernel not initialized');
        }

        $app->setAutoExit(false);

        $app->run(new ArrayInput(['command' => 'deck:tag:set']));
    }
}
