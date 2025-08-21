<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: self::NAME, description: 'Initialize environment')]
final class EnvironmentInitCommand extends Command
{
    public const string NAME = 'environment:init';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $app = $this->getApplication();

        if (null === $app) {
            $io->error('Kernel not initialized');

            return Command::FAILURE;
        }

        $app->setAutoExit(false);

        $io->info('Creating database');
        $app->run(new ArrayInput(['command' => CreateDatabaseCommand::NAME, '--quiet' => true]));
        $io->success('Database created');

        $io->info('Loading migrations');
        $app->run(new ArrayInput(['command' => LoadMigrationsCommand::NAME, '--quiet' => true]));
        $io->success('Migrations loaded');

        $io->info('Loading fixtures');
        $app->run(new ArrayInput(['command' => LoadFixturesCommand::NAME, '--quiet' => true]));
        $io->success('Fixtures loaded');

        return Command::SUCCESS;
    }
}
