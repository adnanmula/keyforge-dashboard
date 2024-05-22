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
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

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
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($winnerDeck->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        $games2 = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
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

        $this->deckRepository->saveDeckWins($winnerDeck->id(), $deck1Wins, $deck1Losses);
        $this->deckRepository->saveDeckWins($loserDeck->id(), $deck2Wins, $deck2Losses);
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

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(FilterType::OR, ...$filters),
        ));

        $winners = [
            $fixture->users()[0]->value() => 0,
            $fixture->users()[1]->value() => 0,
        ];

        foreach ($games as $game) {
            $winners[$game->winner()->value()] += 1;
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_3) {
            return $this->checkWins($fixture, $winners, 2);
        }

        if ($fixture->type() === CompetitionFixtureType::BEST_OF_5) {
            return $this->checkWins($fixture, $winners, 3);
        }

        $gamesCount = $winners[$fixture->users()[0]->value()] + $winners[$fixture->users()[1]->value()];

        if ($fixture->type() === CompetitionFixtureType::GAMES_3) {
            if ($gamesCount === 3) {
                return $this->checkWins($fixture, $winners, 2);
            }

            return null;
        }

        if ($fixture->type() === CompetitionFixtureType::GAMES_5) {
            if ($gamesCount === 5) {
                return $this->checkWins($fixture, $winners, 3);
            }

            return null;
        }

        return null;
    }

    private function checkWins(KeyforgeCompetitionFixture $fixture, array $winners, int $expectedWins): ?Uuid
    {
        if ($winners[$fixture->users()[0]->value()] >= $expectedWins) {
            return $fixture->users()[0];
        }

        if ($winners[$fixture->users()[1]->value()] >= $expectedWins) {
            return $fixture->users()[1];
        }

        return null;
    }
}
