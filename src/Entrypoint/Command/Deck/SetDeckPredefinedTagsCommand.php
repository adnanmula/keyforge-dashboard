<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
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

#[AsCommand(name: 'deck:tag:set', description: 'Apply predefined tags to decks')]
final class SetDeckPredefinedTagsCommand extends Command
{
    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly DeckApplyPredefinedTagsService $service,
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

        $decks = $this->decks($input->getOption('deck'));

        $total = \count($decks);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        foreach ($decks as $deck) {
            $this->service->execute($deck->id());
            $progressBar->advance();

            if ($output->isVerbose()) {
                $output->writeln(' | ' . $deck->id() . ' ' . $deck->name());
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $io->success(sprintf('Applied tags for %d deck(s)', $total));

        return self::SUCCESS;
    }

    /** @return array<KeyforgeDeck> */
    private function decks(?string $deckId): array
    {
        $filters = [];

        if (null !== $deckId) {
            if (false === Uuid::isValid($deckId)) {
                throw new \InvalidArgumentException('Invalid deck id');
            }

            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL);
        }

        return $this->deckRepository->search(
            new Criteria(new Filters(FilterType::AND, ...$filters)),
        );
    }
}
