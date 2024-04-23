<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateDeckSasScoreCommand extends Command
{
    public const NAME = 'deck:sas:update';

    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly ImportDeckService $service,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Update sas score')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Amount of decks to process', 10)
            ->addOption('decks', 'd', InputOption::VALUE_REQUIRED)
            ->addOption('overwrite', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$batch, $deckIds, $overwrite] = $this->params($input);

        $decks = $this->decks($batch, $deckIds);

        foreach ($decks as $index => $deck) {
            $this->service->execute($deck->id(), $deck->owner(), true);
            $output->writeln($deck->name());

            if ($index > 0 && ($index+1) % 25 === 0) {
                $output->writeln('Reached request limit sleeping for 70 seconds');
                \sleep(70);
            }
        }

        if ($overwrite) {
            $this->deckRepository->executeSasUpdate();
        }

        return self::SUCCESS;
    }

    private function params(InputInterface $input): array
    {
        $batch = (int)$input->getArgument('batch');
        $deckIds = $input->getOption('decks') ?? [];
        $overwrite = $input->getOption('overwrite');

        if ([] !== $deckIds) {
            $deckIds = \explode(',', $input->getOption('decks'));
        }

        return [$batch, $deckIds, $overwrite];
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
                null,
                $batch,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('new_sas'),
                        new NullFilterValue(),
                        FilterOperator::IS_NULL,
                    ),
                ),
                new AndFilterGroup(FilterType::OR, ...$filters),
            ),
        );
    }
}
