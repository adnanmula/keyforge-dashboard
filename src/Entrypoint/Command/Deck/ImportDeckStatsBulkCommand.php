<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck\KeyforgeDeckUpdateDbalRepository;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportDeckAllianceFromDokService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'import:deck', description: 'Import decks')]
final class ImportDeckStatsBulkCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly KeyforgeDeckUpdateDbalRepository $updateRepository,
        private readonly ImportDeckService $service,
        private readonly ImportDeckAllianceFromDokService $allianceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('batch', InputArgument::OPTIONAL, 'Amount of decks to process', 10)
            ->addOption('with-history', null, InputOption::VALUE_NONE, 'Import stats history')
            ->addOption('decks', 'd', InputOption::VALUE_REQUIRED, 'Filter by deck ids, comma separated')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Filter by deck type', KeyforgeDeckType::STANDARD->value)
            ->addOption('onlyFormat', 'f', InputOption::VALUE_NONE, 'Save deck to apply new format, dont fetch from third party')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        [$batch, $withHistory, $deckIds, $type, $onlyFormat] = $this->params($input);

        $alreadyImported = $this->updateRepository->all();
        $decks = $this->decks($batch, $deckIds, $alreadyImported, $type);

        $total = \count($decks);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        foreach ($decks as $index => $deckId) {
            try {
                if ($onlyFormat) {
                    $deck = $this->deckRepository->searchOne(new Criteria(
                        new Filters(
                            FilterType::AND,
                            new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL),
                        ),
                    ));

                    $this->deckRepository->save($deck);

                    continue;
                }

                if (KeyforgeDeckType::ALLIANCE === $type) {
                    $this->allianceService->execute(Uuid::from($deckId), null, true);
                } else {
                    $this->service->execute(Uuid::from($deckId), null, true, $withHistory);
                }

                if ($io->isVerbose()) {
                    $io->writeln(' | ' . $deckId);
                }
            } catch (DeckNotExistsException) {
                if ($io->isVerbose()) {
                    $io->error(' | NOT FOUND: '. $deckId);
                }
            }

            $this->updateRepository->add(Uuid::from($deckId));

            $progressBar->advance();

            if (false === $onlyFormat && $index*2 > 0 && ($index*2+2) % 25 === 0) {
                if ($io->isVerbose()) {
                    $io->error(' | Reached request limit sleeping for 65 seconds');
                }

                \sleep(65);
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $io->success(sprintf('Imported %d deck(s)', $total));

        return self::SUCCESS;
    }

    private function params(InputInterface $input): array
    {
        $batch = (int) $input->getArgument('batch');
        $withHistory = $input->getOption('with-history') ?? false;
        $deckIds = $input->getOption('decks') ?? [];
        $type = KeyforgeDeckType::from($input->getOption('type'));
        $onlyFormat = $input->getOption('onlyFormat');

        if ([] !== $deckIds) {
            $deckIds = \explode(',', $input->getOption('decks'));
        }

        return [$batch, $withHistory, $deckIds, $type, $onlyFormat];
    }

    private function decks(int $batch, array $deckIds, array $alreadyImported, KeyforgeDeckType $type): array
    {
        $draftDecks = [
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
        ];

        $alreadyImported = \array_merge($alreadyImported, $draftDecks);

        $query = $this->connection->createQueryBuilder()
            ->select('a.id')
            ->from('keyforge_decks', 'a')
            ->where('a.id not in (:already_imported)')
            ->andWhere('a.deck_type = :type')
            ->setParameter('already_imported', $alreadyImported, ArrayParameterType::STRING)
            ->setParameter('type', $type->value);

        if (\count($deckIds) > 0) {
            $query->andWhere('a.id in (:decks)')
                ->setParameter('decks', $deckIds, ArrayParameterType::STRING);
        }

        return $query->setMaxResults($batch)->executeQuery()->fetchFirstColumn();
    }
}
