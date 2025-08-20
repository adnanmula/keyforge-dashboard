<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportDeckStatHistoryFromDokService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'import:deck:history', description: 'Import sas history')]
final class ImportDeckStatHistoryCommand extends Command
{
    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly ImportDeckStatHistoryFromDokService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('batch', InputArgument::OPTIONAL, 'Amount of decks to process', 10)
            ->addOption('decks', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        [$batch, $deckIds] = $this->params($input);

        $decks = $this->decks($batch, $deckIds);

        $total = \count($decks);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        foreach ($decks as $index => $deck) {
            try {
                $this->service->execute($deck->id());

                if ($io->isVerbose()) {
                    $io->writeln(' | ' . $deck->id()->value() . ' ' . $deck->name());
                }
            } catch (DeckNotExistsException) {
                if ($io->isVerbose()) {
                    $io->error(' | NOT FOUND: '. $deck->id()->value() . ' ' . $deck->name());
                }
            }

            $progressBar->advance();

            if ($index > 0 && ($index+1) % 25 === 0) {
                if ($io->isVerbose()) {
                    $io->error(' | Reached request limit sleeping for 65 seconds');
                }

                \sleep(65);
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $io->success(sprintf('Imported %d', $total));

        return self::SUCCESS;
    }

    private function params(InputInterface $input): array
    {
        $batch = (int)$input->getArgument('batch');
        $deckIds = $input->getOption('decks') ?? [];

        if ([] !== $deckIds) {
            $deckIds = \explode(',', $input->getOption('decks'));
        }

        return [$batch, $deckIds];
    }

    private function decks(int $batch, array $deckIds): array
    {
        $filters = [];

        foreach ($deckIds as $deckId) {
            if (false === Uuid::isValid($deckId)) {
                throw new \InvalidArgumentException('Invalid deck id: ' . $deckId);
            }

            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL);
        }

        return $this->deckRepository->search(
            new Criteria(
                filters: new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringArrayFilterValue(
                            '37259b93-1cdd-4ea8-8206-767b071b2643',
                            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
                            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
                            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
                        ),
                        FilterOperator::NOT_IN,
                    ),
                    new CompositeFilter(FilterType::OR, ...$filters),
                ),
                limit: $batch,
            ),
        );
    }
}
