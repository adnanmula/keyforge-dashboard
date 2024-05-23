<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\KeyforgeDeckUpdateDbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportDeckStatsBulkCommand extends Command
{
    public const NAME = 'deck:stats:bulk';

    public function __construct(
        private readonly KeyforgeDeckRepository $repository,
        private readonly KeyforgeDeckUpdateDbalRepository $updateRepository,
        private readonly ImportDeckService $service,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Import deck stats in bulk')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Amount of decks to process', 10)
            ->addOption('decks', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$batch, $deckIds] = $this->params($input);

        $alreadyImported = $this->updateRepository->all();
        $decks = $this->decks($batch, $deckIds, $alreadyImported);

        foreach ($decks as $index => $deck) {
            try {
                $this->service->execute($deck->id(), $deck->userData()->owner, true, true);
                $output->writeln($deck->data()->name);
            } catch (DeckNotExistsException) {
                $output->writeln('<error>NOT FOUND: '. $deck->data()->name .'</error>');
            }

            $this->updateRepository->add($deck->id());

            if ($index*2 > 0 && ($index*2+2) % 25 === 0) {
                $output->writeln('Reached request limit sleeping for 65 seconds');
                \sleep(70);
            }
        }

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

    private function decks(int $batch, array $deckIds, array $alreadyImported): array
    {
        $draftDecks = [
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
        ];

        $alreadyImported = \array_merge($alreadyImported, $draftDecks);

        $filters = [];

        foreach ($deckIds as $deckId) {
            if (false === Uuid::isValid($deckId)) {
                throw new \InvalidArgumentException('Invalid deck id: ' . $deckId);
            }

            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL);
        }

        foreach ($deckIds as $deckId) {
            if (false === Uuid::isValid($deckId)) {
                throw new \InvalidArgumentException('Invalid deck id: ' . $deckId);
            }

            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL);
        }

        return $this->repository->search(
            new Criteria(
                null,
                $batch,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringArrayFilterValue(...$alreadyImported),
                        FilterOperator::NOT_IN,
                    ),
                ),
                new AndFilterGroup(FilterType::OR, ...$filters),
            ),
        );
    }
}
