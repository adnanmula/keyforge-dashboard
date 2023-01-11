<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;

final class CreateCompetitionGameCommandHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {}

    public function __invoke(CreateCompetitionGameCommand $command): void
    {
        [$winnerDeck, $loserDeck] = $this->getDecks($command->winnerDeck, $command->loserDeck);

        $game = new KeyforgeGame(
            Uuid::v4(),
            $command->winner,
            $command->loser,
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->winnerChains,
            $command->loserChains,
            $command->firstTurn,
            KeyforgeGameScore::from(3, $command->loserScore),
            $command->date,
            new \DateTimeImmutable(),
            $command->competition,
            $command->notes,
        );

        $this->gameRepository->save($game);

        $this->updateDeckWinRate($winnerDeck, $loserDeck);

        $this->updateFixture($command, $game);
    }

    private function updateFixture(CreateCompetitionGameCommand $command, KeyforgeGame $game): void
    {
        $fixture = $this->competitionRepository->fixtureById($command->fixtureId);

        if (null === $fixture) {
            return;
        }

        $fixture->addGame($game->id());
        $fixture->updateWinner($this->fixtureWinner($fixture, $game));
        $fixture->updatePlayedAt($command->date);

        $this->competitionRepository->saveFixture($fixture);
    }

    private function getDecks(string $winnerDeck, string $loserDeck): array
    {
        $winnerDeck = $this->deckRepository->byId(Uuid::from($winnerDeck));
        $loserDeck = $this->deckRepository->byId(Uuid::from($loserDeck));

        if (null === $winnerDeck || null === $loserDeck) {
            throw new \Exception('Deck not found');
        }

        return [$winnerDeck, $loserDeck];
    }

    private function updateDeckWinRate(KeyforgeDeck $winnerDeck, KeyforgeDeck $loserDeck): void
    {
        $games1 = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        $games2 = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($loserDeck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($loserDeck->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        $deck1Wins = 0;
        $deck1Losses = 0;

        foreach ($games1 as $game) {
            if ($game->winnerDeck()->equalTo($winnerDeck->id())) {
                $deck1Wins++;
            }

            if ($game->loserDeck()->equalTo($winnerDeck->id())) {
                $deck1Losses++;
            }
        }

        $deck2Wins = 0;
        $deck2Losses = 0;

        foreach ($games2 as $game) {
            if ($game->winnerDeck()->equalTo($loserDeck->id())) {
                $deck2Wins++;
            }

            if ($game->loserDeck()->equalTo($loserDeck->id())) {
                $deck2Losses++;
            }
        }

        $winnerDeck->updateWins($deck1Wins)->updateLosses($deck1Losses);
        $loserDeck->updateWins($deck2Wins)->updateLosses($deck2Losses);

        $this->deckRepository->save($winnerDeck);
        $this->deckRepository->save($loserDeck);
    }

    private function fixtureWinner(KeyforgeCompetitionFixture $fixture, KeyforgeGame $game): ?Uuid
    {
        if ($fixture->type() === CompetitionFixtureType::BEST_OF_1) {
            return $game->winner();
        }

        $filters = [];

        foreach ($fixture->games() as $gameId) {
            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($gameId->value()), FilterOperator::EQUAL);
        }

        $criteria = new Criteria(
            null,
            null,
            null,
            new Filters(FilterType::AND, FilterType::OR, ...$filters),
        );

        $games = $this->gameRepository->search($criteria);

        $winners = [
            $fixture->users()[0]->value() => 0,
            $fixture->users()[1]->value() => 0,
        ];

        foreach ($games as $game) {
            $winners[$game->winner()->value()] += 1;
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_3) {
            if ($winners[$fixture->users()[0]->value()] === 2) {
                return $fixture->users()[0];
            }

            if ($winners[$fixture->users()[1]->value()] === 2) {
                return $fixture->users()[1];
            }

            return null;
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_5) {
            if ($winners[$fixture->users()[0]->value()] === 3) {
                return $fixture->users()[0];
            }

            if ($winners[$fixture->users()[1]->value()] === 3) {
                return $fixture->users()[1];
            }

            return null;
        }

        return null;
    }
}
