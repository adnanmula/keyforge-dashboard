<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportMyDecksFromDokService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import:my-decks', description: 'Import my decks from dok')]
final class ImportMyDecksFromDokCommand extends Command
{
    public function __construct(
        private readonly ImportMyDecksFromDokService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('dok-token', InputArgument::REQUIRED, 'Dok token to import my decks')
            ->addArgument('owner', InputArgument::REQUIRED, 'Owner id to set to decks imported')
            ->addOption('force-update', 'f', InputOption::VALUE_NONE, 'Update decks if they are already imported');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $input->getArgument('dok-token') ?? null;
        $owner = $input->getArgument('owner') ?? null;
        $forceUpdate = $input->getOption('force-update');

        $this->service->execute($token, Uuid::from($owner), $forceUpdate);

        return self::SUCCESS;
    }
}
