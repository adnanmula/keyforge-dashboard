<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
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
    public const NAME = 'deck:wins:set';

    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly KeyforgeGameRepository $gamesRepository,
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
            [$wins, $losses] = $this->games($deck->id());
            $output->writeln($wins . '/' . $losses . ' - ' . $deck->data()->name);

            $this->deckRepository->saveDeckWins($deck->id(), $wins, $losses);
        }

        return self::SUCCESS;
    }


    public function games(Uuid $deckId): array
    {
        $games = $this->gamesRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::OR,
                    new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        $wins = 0;
        $losses = 0;

        foreach ($games as $game) {
            if ($game->winnerDeck()->equalTo($deckId)) {
                $wins++;
            }

            if ($game->loserDeck()->equalTo($deckId)) {
                $losses++;
            }
        }

        return [$wins, $losses];
    }
}
