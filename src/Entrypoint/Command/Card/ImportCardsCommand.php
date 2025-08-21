<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'import:card', description: 'Import cards from Decks of KeyForge')]
final class ImportCardsCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $dokClient,
        private readonly KeyforgeCardRepository $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Do not persist any changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        try {
            $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v1/cards')->toArray();
        } catch (\Throwable $e) {
            $io->error(sprintf('Unexpected error fetching cards: %s', $e->getMessage()));

            return self::FAILURE;
        }

        $total = \count($response);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        foreach ($response as $cardData) {
            try {
                $card = KeyforgeCard::fromDokData($cardData);
            } catch (\Throwable $e) {
                $io->error('Error parsing card: ' . $e->getMessage());

                return self::FAILURE;
            }

            if (false === $dryRun) {
                $this->repository->save($card);
            }

            if ($output->isVerbose()) {
                $output->writeln($card->name->get(Locale::en_GB));
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');

        $summary = sprintf('Imported %d card(s)', $total);
        if ($dryRun) {
            $summary .= ' (dry run)';
        }

        $io->success($summary);

        return self::SUCCESS;
    }
}
