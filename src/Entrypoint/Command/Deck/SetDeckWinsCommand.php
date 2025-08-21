<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'deck:wins:set', description: 'Set wins and losses on user data')]
final class SetDeckWinsCommand extends Command
{
    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly UpdateDeckWinRateService $updateDeckWinRateService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('deck', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deck = $input->getOption('deck');

        $filters = [];

        if (null !== $deck) {
            $filters[] = new Filter(
                new FilterField('id'),
                new StringFilterValue($deck),
                FilterOperator::EQUAL,
            );
        }

        $decks = $this->deckRepository->search(new Criteria(new Filters(FilterType::AND, ...$filters)));

        $total = \count($decks);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        foreach ($decks as $deck) {
            $this->updateDeckWinRateService->execute($deck->id());

            $progressBar->advance();

            if ($output->isVerbose()) {
                $output->writeln(' | ' . $deck->id() . ' ' . $deck->name());
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $io->success(sprintf('Calculated winrate for %d deck(s)', $total));

        return self::SUCCESS;
    }
}
