<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Environment;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadMigrationsCommand extends Command
{
    public const NAME = 'environment:migrations';

    protected function configure(): void
    {
        $this->setName(self::NAME)->setDescription('Execute migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phinx = new PhinxApplication();
        $command = $phinx->find('migrate');

        $arguments = [
            'command' => 'migrate',
        ];

        return $command->run(new ArrayInput($arguments), $output);
    }
}
