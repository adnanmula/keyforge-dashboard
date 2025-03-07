<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SetDeckWinsCommand extends Command
{
    public const string NAME = 'deck:wins:set';

    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly UpdateDeckWinRateService $updateDeckWinRateService,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Set wins and losses on user data')
            ->addOption('deck', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deck = $input->getOption('deck');
        $filters = [];

        if (null !== $deck) {
            $filters[] = new Filter(
                new FilterField('id'),
                new StringFilterValue($deck),
                FilterOperator::EQUAL,
            );
        }

        $decks = $this->deckRepository->search(new Criteria(null, null, null, new AndFilterGroup(FilterType::AND, ...$filters)));

        foreach ($decks as $deck) {
            $this->updateDeckWinRateService->execute($deck->id());
            $output->writeln($deck->name());
        }

        return self::SUCCESS;
    }
}
