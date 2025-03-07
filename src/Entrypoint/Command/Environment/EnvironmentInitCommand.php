<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class EnvironmentInitCommand extends Command
{
    public const string NAME = 'environment:init';

    public function __construct()
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Initialize environment');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = $this->getApplication();

        if (null === $app) {
            throw new \RuntimeException('Kernel not initialized');
        }

        $app->setAutoExit(false);

        $app->run(new ArrayInput(['command' => 'environment:database']));
        $output->writeln('Database created.');

        $app->run(new ArrayInput(['command' => 'environment:migrations']));
        $output->writeln('Migrations executed.');

        $app->run(new ArrayInput(['command' => 'environment:fixtures']));
        $output->writeln('Loaded fixtures.');

        return Command::SUCCESS;
    }
}
